<?php
class InventoryController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('inventory');
        $page     = 'inventory';
        $action   = 'index';
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    // تقرير حركات مخزون منتج
    public function movements(): void {
        Auth::requirePermission('inventory');
        $productId = (int)($_GET['product_id'] ?? 0);
        $product   = $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$productId]);
        if (!$product) { Helper::jsonResponse(['success' => false, 'message' => 'المنتج غير موجود']); }

        $movements = $this->db->fetchAll(
            "SELECT sm.*, u.full_name as user_name
             FROM stock_movements sm
             LEFT JOIN users u ON sm.user_id = u.id
             WHERE sm.product_id = ?
             ORDER BY sm.created_at DESC
             LIMIT 50",
            [$productId]
        );

        Helper::jsonResponse([
            'success'   => true,
            'product'   => $product,
            'movements' => $movements,
        ]);
    }

    // تسوية مخزون دفعية (جرد)
    public function bulkAdjust(): void {
        Auth::requirePermission('inventory');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Helper::redirect('?page=inventory');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=inventory');
        }

        $items = $_POST['items'] ?? [];
        $count = 0;

        $this->db->beginTransaction();
        try {
            foreach ($items as $productId => $newQty) {
                $newQty    = (float)$newQty;
                $productId = (int)$productId;
                $product   = $this->db->fetchOne("SELECT stock_qty FROM products WHERE id = ?", [$productId]);
                if (!$product) continue;

                $before = (float)$product['stock_qty'];
                if (abs($before - $newQty) < 0.001) continue; // لا تغيير

                $this->db->update('products', ['stock_qty' => $newQty], 'id = ?', [$productId]);
                $this->db->insert('stock_movements', [
                    'product_id'     => $productId,
                    'type'           => 'adjustment',
                    'quantity'       => abs($newQty - $before),
                    'before_qty'     => $before,
                    'after_qty'      => $newQty,
                    'reference_type' => 'inventory_count',
                    'user_id'        => Auth::id(),
                    'notes'          => 'تسوية جرد',
                ]);
                $count++;
            }
            $this->db->commit();
            Session::flash('success', "تم تحديث مخزون {$count} منتج بنجاح");
        } catch (Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'حدث خطأ: ' . $e->getMessage());
        }

        Helper::redirect('?page=inventory');
    }

    // تقرير المخزون PDF/CSV
    public function export(): void {
        Auth::requirePermission('inventory');
        $products = $this->db->fetchAll(
            "SELECT p.name, p.barcode, c.name as category, p.unit,
                    p.purchase_price, p.sale_price, p.stock_qty,
                    p.min_stock_alert, p.expiry_date,
                    (p.stock_qty * p.purchase_price) as stock_value
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.is_active = 1
             ORDER BY p.name ASC"
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="inventory_' . date('Y-m-d') . '.csv"');
        $f = fopen('php://output', 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($f, ['المنتج','الباركود','التصنيف','الوحدة','سعر الشراء','سعر البيع','المخزون','حد التنبيه','انتهاء الصلاحية','قيمة المخزون']);
        foreach ($products as $p) {
            fputcsv($f, [
                $p['name'], $p['barcode'] ?? '', $p['category'] ?? '',
                $p['unit'], $p['purchase_price'], $p['sale_price'],
                $p['stock_qty'], $p['min_stock_alert'],
                $p['expiry_date'] ?? '', $p['stock_value']
            ]);
        }
        fclose($f);
        exit;
    }
}
