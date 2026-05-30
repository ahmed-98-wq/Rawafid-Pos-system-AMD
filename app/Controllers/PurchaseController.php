<?php
class PurchaseController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('purchases');
        $page      = 'purchases';
        $action    = 'index';
        $settings  = Helper::getSettings();
        $purchases = $this->db->fetchAll(
            "SELECT p.*, s.name as supplier_name, u.full_name as user_name
             FROM purchases p
             LEFT JOIN suppliers s ON p.supplier_id = s.id
             LEFT JOIN users u ON p.user_id = u.id
             ORDER BY p.created_at DESC LIMIT 60"
        );
        require ROOT . '/app/Views/layouts/main.php';
    }

    public function store(): void {
        Auth::requirePermission('purchases');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Helper::redirect('?page=purchases');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=purchases');
        }

        $items = $_POST['items'] ?? [];
        $validItems = array_filter($items, fn($i) =>
            !empty($i['product_id']) && (float)($i['quantity'] ?? 0) > 0
        );

        if (empty($validItems)) {
            Session::flash('error', 'يرجى إضافة منتج واحد على الأقل بكمية صحيحة');
            Helper::redirect('?page=purchases');
        }

        $this->db->beginTransaction();
        try {
            $purchaseNumber = Helper::generatePurchaseNumber();
            $total          = (float)($_POST['total'] ?? 0);
            $paid           = (float)($_POST['paid_amount'] ?? $total);
            $supplierId     = (int)($_POST['supplier_id'] ?? 0) ?: null;

            $purchaseId = $this->db->insert('purchases', [
                'purchase_number' => $purchaseNumber,
                'supplier_id'     => $supplierId,
                'user_id'         => Auth::id(),
                'total'           => $total,
                'paid_amount'     => $paid,
                'status'          => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'pending'),
                'notes'           => Helper::sanitize($_POST['notes'] ?? ''),
            ]);

            foreach ($validItems as $item) {
                $productId = (int)$item['product_id'];
                $qty       = (float)$item['quantity'];
                $price     = (float)$item['price'];
                $lineTotal = round($qty * $price, 2);

                $product = $this->db->fetchOne(
                    "SELECT id, stock_qty FROM products WHERE id = ? FOR UPDATE",
                    [$productId]
                );
                if (!$product) continue;

                $this->db->insert('purchase_items', [
                    'purchase_id' => $purchaseId,
                    'product_id'  => $productId,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'total'       => $lineTotal,
                ]);

                $newQty = $product['stock_qty'] + $qty;
                // تحديث المخزون وسعر الشراء
                $this->db->update('products', [
                    'stock_qty'      => $newQty,
                    'purchase_price' => $price,
                ], 'id = ?', [$productId]);

                $this->db->insert('stock_movements', [
                    'product_id'     => $productId,
                    'type'           => 'in',
                    'quantity'       => $qty,
                    'before_qty'     => $product['stock_qty'],
                    'after_qty'      => $newQty,
                    'reference_type' => 'purchase',
                    'reference_id'   => $purchaseId,
                    'user_id'        => Auth::id(),
                    'notes'          => 'فاتورة شراء رقم ' . $purchaseNumber,
                ]);
            }

            // تحديث رصيد المورد إن كان هناك مبلغ غير مسدَّد
            if ($supplierId && $paid < $total) {
                $due = $total - $paid;
                $this->db->query(
                    "UPDATE suppliers SET balance = balance + ? WHERE id = ?",
                    [$due, $supplierId]
                );
            }

            $this->db->commit();
            Session::flash('success', 'تمت إضافة فاتورة الشراء «' . $purchaseNumber . '» وتم تحديث المخزون');
        } catch (Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'حدث خطأ: ' . $e->getMessage());
        }

        Helper::redirect('?page=purchases');
    }

    public function delete(): void {
        Auth::requirePermission('admin');
        $id = (int)($_GET['id'] ?? 0);
        $p  = $this->db->fetchOne("SELECT purchase_number FROM purchases WHERE id = ?", [$id]);
        if (!$p) { Helper::redirect('?page=purchases'); }

        // عكس حركات المخزون
        $items = $this->db->fetchAll("SELECT * FROM purchase_items WHERE purchase_id = ?", [$id]);
        $this->db->beginTransaction();
        try {
            foreach ($items as $item) {
                $product = $this->db->fetchOne("SELECT stock_qty FROM products WHERE id = ?", [$item['product_id']]);
                if (!$product) continue;
                $newQty = max(0, $product['stock_qty'] - $item['quantity']);
                $this->db->update('products', ['stock_qty' => $newQty], 'id = ?', [$item['product_id']]);
                $this->db->insert('stock_movements', [
                    'product_id'     => $item['product_id'],
                    'type'           => 'out',
                    'quantity'       => $item['quantity'],
                    'before_qty'     => $product['stock_qty'],
                    'after_qty'      => $newQty,
                    'reference_type' => 'purchase_cancel',
                    'reference_id'   => $id,
                    'user_id'        => Auth::id(),
                    'notes'          => 'إلغاء فاتورة شراء ' . $p['purchase_number'],
                ]);
            }
            $this->db->delete('purchases', 'id = ?', [$id]);
            $this->db->commit();
            Session::flash('success', 'تم حذف فاتورة الشراء «' . $p['purchase_number'] . '»');
        } catch (Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'حدث خطأ: ' . $e->getMessage());
        }

        Helper::redirect('?page=purchases');
    }

    public function view(): void {
        Auth::requirePermission('purchases');
        $id       = (int)($_GET['id'] ?? 0);
        $purchase = $this->db->fetchOne(
            "SELECT p.*, s.name as supplier_name, u.full_name as user_name
             FROM purchases p
             LEFT JOIN suppliers s ON p.supplier_id = s.id
             LEFT JOIN users u ON p.user_id = u.id
             WHERE p.id = ?",
            [$id]
        );
        if (!$purchase) Helper::redirect('?page=purchases');

        $items = $this->db->fetchAll(
            "SELECT pi.*, pr.name as product_name, pr.unit
             FROM purchase_items pi
             JOIN products pr ON pi.product_id = pr.id
             WHERE pi.purchase_id = ?",
            [$id]
        );

        $settings = Helper::getSettings();
        require ROOT . '/app/Views/purchases/view.php';
    }
}
