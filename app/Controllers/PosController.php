<?php
class PosController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('pos');
        $page     = 'pos';
        $action   = 'index';
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    // API: جلب المنتجات مع بيانات الصلاحية
    public function getProducts(): void {
        Auth::requirePermission('pos');
        $catId = (int)($_GET['cat'] ?? 0);

        $sql    = "SELECT p.id, p.name, p.barcode, p.sale_price, p.stock_qty,
                          p.unit, p.image, p.expiry_date, p.category_id,
                          c.name as category_name,
                          DATEDIFF(p.expiry_date, CURDATE()) as days_left
                   FROM products p
                   LEFT JOIN categories c ON p.category_id = c.id
                   WHERE p.is_active = 1";
        $params = [];

        if ($catId > 0) {
            $sql    .= " AND p.category_id = ?";
            $params[] = $catId;
        }
        $sql .= " ORDER BY p.name ASC";

        $products   = $this->db->fetchAll($sql, $params);
        $categories = $this->db->fetchAll("SELECT id, name FROM categories ORDER BY name");

        Helper::jsonResponse([
            'success'    => true,
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    // API: بحث عن منتجات
    public function searchProducts(): void {
        Auth::requirePermission('pos');
        $q = Helper::sanitize($_GET['q'] ?? '');
        $products = $this->db->fetchAll(
            "SELECT p.id, p.name, p.barcode, p.sale_price, p.stock_qty,
                    p.unit, p.expiry_date,
                    DATEDIFF(p.expiry_date, CURDATE()) as days_left
             FROM products p
             WHERE p.is_active = 1
               AND (p.name LIKE ? OR p.barcode LIKE ?)
             ORDER BY p.name ASC LIMIT 20",
            ["%{$q}%", "%{$q}%"]
        );
        Helper::jsonResponse(['success' => true, 'products' => $products]);
    }

    // حفظ الفاتورة
    public function saveInvoice(): void {
        Auth::requirePermission('pos');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::jsonResponse(['success' => false, 'message' => 'طريقة غير مسموحة']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['items'])) {
            Helper::jsonResponse(['success' => false, 'message' => 'لا توجد منتجات في الفاتورة']);
        }

        // التحقق من صلاحية جميع المنتجات قبل البيع
        foreach ($data['items'] as $item) {
            $product = $this->db->fetchOne(
                "SELECT name, expiry_date FROM products WHERE id = ?",
                [(int)$item['product_id']]
            );
            if ($product && !empty($product['expiry_date'])) {
                if (strtotime($product['expiry_date']) < strtotime('today')) {
                    Helper::jsonResponse([
                        'success' => false,
                        'message' => 'لا يمكن بيع المنتج «' . $product['name'] . '» لأنه منتهي الصلاحية!',
                    ]);
                }
            }
        }

        $this->db->beginTransaction();
        try {
            $invNumber = Helper::generateInvoiceNumber();
            $subtotal  = 0;
            $taxRate   = (float)Helper::getSetting('tax_rate', 15);
            $taxEnabled = (bool)Helper::getSetting('tax_enabled', 1);

            foreach ($data['items'] as $item) {
                $subtotal += (float)$item['price'] * (float)$item['qty'];
            }

            $discountType   = $data['discount_type']  ?? 'fixed';
            $discountValue  = (float)($data['discount_value'] ?? 0);
            $discountAmount = $discountType === 'percent'
                ? round($subtotal * $discountValue / 100, 2)
                : min($discountValue, $subtotal);

            $afterDiscount = $subtotal - $discountAmount;
            $taxAmount     = $taxEnabled ? round($afterDiscount * $taxRate / 100, 2) : 0;
            $total         = $afterDiscount + $taxAmount;
            $paidAmount    = (float)($data['paid_amount'] ?? $total);
            $change        = max(0, $paidAmount - $total);

            $invoiceId = $this->db->insert('invoices', [
                'invoice_number'  => $invNumber,
                'type'            => 'sale',
                'customer_id'     => $data['customer_id'] ?: null,
                'user_id'         => Auth::id(),
                'subtotal'        => $subtotal,
                'discount_type'   => $discountType,
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmount,
                'total'           => $total,
                'paid_amount'     => $paidAmount,
                'change_amount'   => $change,
                'payment_method'  => $data['payment_method'] ?? 'cash',
                'status'          => $paidAmount >= $total ? 'paid' : 'partial',
                'notes'           => $data['notes'] ?? '',
            ]);

            foreach ($data['items'] as $item) {
                $product = $this->db->fetchOne(
                    "SELECT id, name, purchase_price, stock_qty FROM products WHERE id = ? FOR UPDATE",
                    [(int)$item['product_id']]
                );
                if (!$product) continue;

                $qty   = (float)$item['qty'];
                $price = (float)$item['price'];
                $disc  = (float)($item['discount'] ?? 0);
                $lineTotal = round(($price * $qty) * (1 - $disc / 100), 2);

                $this->db->insert('invoice_items', [
                    'invoice_id'     => $invoiceId,
                    'product_id'     => $product['id'],
                    'product_name'   => $product['name'],
                    'quantity'       => $qty,
                    'unit_price'     => $price,
                    'purchase_price' => $product['purchase_price'],
                    'discount_percent' => $disc,
                    'total'          => $lineTotal,
                ]);

                $newQty = max(0, $product['stock_qty'] - $qty);
                $this->db->update('products', ['stock_qty' => $newQty], 'id = ?', [$product['id']]);
                $this->db->insert('stock_movements', [
                    'product_id'     => $product['id'],
                    'type'           => 'out',
                    'quantity'       => $qty,
                    'before_qty'     => $product['stock_qty'],
                    'after_qty'      => $newQty,
                    'reference_type' => 'invoice',
                    'reference_id'   => $invoiceId,
                    'user_id'        => Auth::id(),
                ]);
            }

            // تحديث رصيد العميل
            if (!empty($data['customer_id']) && $paidAmount < $total) {
                $this->db->query(
                    "UPDATE customers SET balance = balance + ? WHERE id = ?",
                    [$total - $paidAmount, $data['customer_id']]
                );
            }

            $this->db->commit();
            Helper::jsonResponse([
                'success'        => true,
                'invoice_id'     => $invoiceId,
                'invoice_number' => $invNumber,
                'total'          => $total,
                'change'         => $change,
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();
            Helper::jsonResponse(['success' => false, 'message' => 'خطأ في الحفظ: ' . $e->getMessage()]);
        }
    }

    // طباعة الفاتورة
    public function printInvoice(): void {
        Auth::requirePermission('pos');
        $id = (int)($_GET['id'] ?? 0);

        $invoice = $this->db->fetchOne(
            "SELECT i.*, c.name as customer_name, u.full_name as cashier_name
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             LEFT JOIN users u ON i.user_id = u.id
             WHERE i.id = ?",
            [$id]
        );
        if (!$invoice) Helper::redirect('?page=pos');

        $items    = $this->db->fetchAll("SELECT * FROM invoice_items WHERE invoice_id = ?", [$id]);
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/pos/print.php';
    }
}
