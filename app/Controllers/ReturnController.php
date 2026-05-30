<?php
class ReturnController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /* =============================================
       قائمة المرتجعات
    ============================================= */
    public function index(): void {
        Auth::requirePermission('pos');
        $page   = 'returns';
        $action = 'index';

        $search   = Helper::sanitize($_GET['search'] ?? '');
        $from     = Helper::sanitize($_GET['from']   ?? date('Y-m-01'));
        $to       = Helper::sanitize($_GET['to']     ?? date('Y-m-d'));
        $pageNum  = max(1, (int)($_GET['p'] ?? 1));
        $perPage  = 20;
        $offset   = ($pageNum - 1) * $perPage;

        $where  = "WHERE i.type = 'return'";
        $params = [];
        if ($search) {
            $where   .= " AND (i.invoice_number LIKE ? OR c.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $where   .= " AND DATE(i.created_at) BETWEEN ? AND ?";
        $params[] = $from;
        $params[] = $to;

        $total = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id $where",
            $params
        );

        $returns = $this->db->fetchAll(
            "SELECT i.*, c.name as customer_name, u.full_name as cashier_name
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             LEFT JOIN users u ON i.user_id = u.id
             $where
             ORDER BY i.created_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        // إحصائيات
        $stats = $this->db->fetchOne(
            "SELECT COUNT(*) as total_count, COALESCE(SUM(total),0) as total_amount
             FROM invoices
             WHERE type='return' AND DATE(created_at) BETWEEN ? AND ?",
            [$from, $to]
        );

        $totalPages = (int)ceil($total / $perPage);
        $settings   = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    /* =============================================
       البحث عن فاتورة لإرجاعها
    ============================================= */
    public function search(): void {
        Auth::requirePermission('pos');
        $page   = 'returns';
        $action = 'search';

        $q       = Helper::sanitize($_GET['q'] ?? '');
        $invoice = null;
        $items   = [];
        $error   = '';

        if ($q) {
            $invoice = $this->db->fetchOne(
                "SELECT i.*, c.name as customer_name
                 FROM invoices i
                 LEFT JOIN customers c ON i.customer_id = c.id
                 WHERE i.invoice_number = ? AND i.type = 'sale'",
                [$q]
            );

            if (!$invoice) {
                $error = 'لم يتم العثور على الفاتورة «' . $q . '» أو أنها ليست فاتورة مبيعات';
            } else {
                $invId = (int)$invoice['id'];
                $items = $this->db->fetchAll(
                    "SELECT ii.*, p.name as product_name, p.unit,
                            COALESCE((
                                SELECT SUM(ri.quantity)
                                FROM invoice_items ri
                                JOIN invoices r ON ri.invoice_id = r.id
                                WHERE r.type = 'return'
                                  AND r.notes LIKE ?
                                  AND ri.product_id = ii.product_id
                            ), 0) as already_returned
                     FROM invoice_items ii
                     JOIN products p ON ii.product_id = p.id
                     WHERE ii.invoice_id = ?",
                    ["%REF:{$invId}%", $invId]
                );
            }
        }

        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    /* =============================================
       تنفيذ المرتجع
    ============================================= */
    public function store(): void {
        Auth::requirePermission('pos');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('?page=returns&action=search');
        }
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=returns&action=search');
        }

        $originalId = (int)($_POST['original_invoice_id'] ?? 0);
        $reason     = Helper::sanitize($_POST['reason'] ?? '');
        $items      = $_POST['return_items'] ?? [];

        // التحقق من الفاتورة الأصلية
        $original = $this->db->fetchOne(
            "SELECT * FROM invoices WHERE id = ? AND type = 'sale'",
            [$originalId]
        );
        if (!$original) {
            Session::flash('error', 'الفاتورة الأصلية غير موجودة');
            Helper::redirect('?page=returns&action=search');
        }

        // تصفية الأصناف المراد إرجاعها
        $validItems = [];
        foreach ($items as $itemId => $data) {
            $qty = (float)($data['qty'] ?? 0);
            if ($qty <= 0) continue;

            $originalItem = $this->db->fetchOne(
                "SELECT ii.*, p.name as product_name
                 FROM invoice_items ii
                 JOIN products p ON ii.product_id = p.id
                 WHERE ii.id = ? AND ii.invoice_id = ?",
                [(int)$itemId, $originalId]
            );
            if (!$originalItem) continue;
            if ($qty > $originalItem['quantity']) {
                Session::flash('error', 'كمية المرتجع للمنتج «' . $originalItem['product_name'] . '» تتجاوز الكمية الأصلية');
                Helper::redirect('?page=returns&action=search&q=' . urlencode($original['invoice_number']));
            }

            $validItems[] = [
                'product_id'   => $originalItem['product_id'],
                'product_name' => $originalItem['product_name'],
                'quantity'     => $qty,
                'unit_price'   => $originalItem['unit_price'],
                'total'        => round($qty * $originalItem['unit_price'], 2),
            ];
        }

        if (empty($validItems)) {
            Session::flash('error', 'يرجى تحديد كمية للإرجاع لمنتج واحد على الأقل');
            Helper::redirect('?page=returns&action=search&q=' . urlencode($original['invoice_number']));
        }

        $this->db->beginTransaction();
        try {
            $returnTotal = array_sum(array_column($validItems, 'total'));
            $returnNum   = 'RET-' . strtoupper(substr(md5(uniqid()), 0, 8));

            // إنشاء فاتورة المرتجع
            $returnId = $this->db->insert('invoices', [
                'invoice_number'  => $returnNum,
                'type'            => 'return',
                'customer_id'     => $original['customer_id'],
                'user_id'         => Auth::id(),
                'subtotal'        => $returnTotal,
                'discount_amount' => 0,
                'tax_rate'        => 0,
                'tax_amount'      => 0,
                'total'           => $returnTotal,
                'paid_amount'     => $returnTotal,
                'change_amount'   => 0,
                'payment_method'  => $original['payment_method'],
                'status'          => 'returned',
                'notes'           => "مرتجع من: {$original['invoice_number']} | REF:{$originalId} | السبب: {$reason}",
            ]);

            // إضافة أصناف المرتجع وإعادة المخزون
            foreach ($validItems as $item) {
                $this->db->insert('invoice_items', [
                    'invoice_id'   => $returnId,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'purchase_price' => 0,
                    'total'        => $item['total'],
                ]);

                // إعادة الكمية للمخزون
                $product = $this->db->fetchOne(
                    "SELECT stock_qty FROM products WHERE id = ?",
                    [$item['product_id']]
                );
                if ($product) {
                    $newQty = $product['stock_qty'] + $item['quantity'];
                    $this->db->update('products', ['stock_qty' => $newQty], 'id = ?', [$item['product_id']]);
                    $this->db->insert('stock_movements', [
                        'product_id'     => $item['product_id'],
                        'type'           => 'return',
                        'quantity'       => $item['quantity'],
                        'before_qty'     => $product['stock_qty'],
                        'after_qty'      => $newQty,
                        'reference_type' => 'return',
                        'reference_id'   => $returnId,
                        'user_id'        => Auth::id(),
                        'notes'          => 'مرتجع من فاتورة ' . $original['invoice_number'],
                    ]);
                }
            }

            // تحديث حالة الفاتورة الأصلية
            $this->db->update('invoices', ['status' => 'returned'], 'id = ?', [$originalId]);

            // إعادة المبلغ لرصيد العميل إن كانت آجل
            if ($original['payment_method'] === 'credit' && $original['customer_id']) {
                $this->db->query(
                    "UPDATE customers SET balance = balance - ? WHERE id = ? AND balance >= ?",
                    [$returnTotal, $original['customer_id'], $returnTotal]
                );
            }

            $this->db->commit();
            Session::flash('success', 'تمت عملية الإرجاع بنجاح — رقم مرتجع: ' . $returnNum);
            Helper::redirect('?page=returns&action=printReturn&id=' . $returnId);

        } catch (Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'حدث خطأ: ' . $e->getMessage());
            Helper::redirect('?page=returns&action=search&q=' . urlencode($original['invoice_number']));
        }
    }

    /* =============================================
       طباعة إيصال المرتجع
    ============================================= */
    public function printReturn(): void {
        Auth::requirePermission('pos');
        $id = (int)($_GET['id'] ?? 0);

        $invoice = $this->db->fetchOne(
            "SELECT i.*, c.name as customer_name, u.full_name as cashier_name
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             LEFT JOIN users u ON i.user_id = u.id
             WHERE i.id = ? AND i.type = 'return'",
            [$id]
        );
        if (!$invoice) Helper::redirect('?page=returns');

        $items    = $this->db->fetchAll("SELECT * FROM invoice_items WHERE invoice_id = ?", [$id]);
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/returns/print.php';
    }

    /* =============================================
       API: جلب تفاصيل فاتورة للإرجاع
    ============================================= */
    public function getInvoice(): void {
        Auth::requirePermission('pos');
        $num = Helper::sanitize($_GET['number'] ?? '');
        if (empty($num)) {
            Helper::jsonResponse(['success' => false, 'message' => 'رقم الفاتورة مطلوب']);
        }

        $invoice = $this->db->fetchOne(
            "SELECT i.*, c.name as customer_name
             FROM invoices i LEFT JOIN customers c ON i.customer_id = c.id
             WHERE i.invoice_number = ? AND i.type = 'sale'",
            [$num]
        );

        if (!$invoice) {
            Helper::jsonResponse(['success' => false, 'message' => 'الفاتورة غير موجودة']);
        }

        $items = $this->db->fetchAll(
            "SELECT ii.*, p.name as product_name, p.unit
             FROM invoice_items ii
             JOIN products p ON ii.product_id = p.id
             WHERE ii.invoice_id = ?",
            [$invoice['id']]
        );

        Helper::jsonResponse(['success' => true, 'invoice' => $invoice, 'items' => $items]);
    }
}
