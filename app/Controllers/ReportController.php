<?php
class ReportController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('reports');
        $page     = 'reports';
        $action   = 'index';
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    // تصدير CSV
    public function exportExcel(): void {
        Auth::requirePermission('reports');
        $from     = Helper::sanitize($_GET['from'] ?? date('Y-m-01'));
        $to       = Helper::sanitize($_GET['to']   ?? date('Y-m-d'));
        $invoices = $this->db->fetchAll(
            "SELECT i.invoice_number, c.name as customer,
                    i.subtotal, i.discount_amount, i.tax_amount,
                    i.total, i.payment_method, i.status, i.created_at
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             WHERE i.type = 'sale'
               AND DATE(i.created_at) BETWEEN ? AND ?
             ORDER BY i.created_at DESC",
            [$from, $to]
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sales_' . $from . '_' . $to . '.csv"');
        $f = fopen('php://output', 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($f, ['رقم الفاتورة','العميل','المجموع الفرعي','الخصم','الضريبة','الإجمالي','طريقة الدفع','الحالة','التاريخ']);
        foreach ($invoices as $inv) {
            fputcsv($f, [
                $inv['invoice_number'], $inv['customer'] ?? 'نقدي',
                $inv['subtotal'], $inv['discount_amount'], $inv['tax_amount'],
                $inv['total'], $inv['payment_method'], $inv['status'], $inv['created_at'],
            ]);
        }
        fclose($f);
        exit;
    }

    // تقرير المبيعات التفصيلي (JSON للطباعة)
    public function salesDetail(): void {
        Auth::requirePermission('reports');
        $from = Helper::sanitize($_GET['from'] ?? date('Y-m-01'));
        $to   = Helper::sanitize($_GET['to']   ?? date('Y-m-d'));

        $data = $this->db->fetchAll(
            "SELECT i.invoice_number, i.total, i.payment_method,
                    i.status, i.created_at,
                    c.name as customer_name,
                    u.full_name as cashier_name
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             LEFT JOIN users u ON i.user_id = u.id
             WHERE i.type = 'sale'
               AND DATE(i.created_at) BETWEEN ? AND ?
             ORDER BY i.created_at DESC",
            [$from, $to]
        );

        Helper::jsonResponse(['success' => true, 'invoices' => $data]);
    }

    // تقرير أفضل العملاء
    public function topCustomers(): void {
        Auth::requirePermission('reports');
        $from = Helper::sanitize($_GET['from'] ?? date('Y-m-01'));
        $to   = Helper::sanitize($_GET['to']   ?? date('Y-m-d'));

        $data = $this->db->fetchAll(
            "SELECT c.name, c.phone,
                    COUNT(i.id) as invoice_count,
                    SUM(i.total) as total_purchases
             FROM invoices i
             JOIN customers c ON i.customer_id = c.id
             WHERE i.type = 'sale' AND i.status != 'returned'
               AND DATE(i.created_at) BETWEEN ? AND ?
             GROUP BY i.customer_id
             ORDER BY total_purchases DESC
             LIMIT 10",
            [$from, $to]
        );

        Helper::jsonResponse(['success' => true, 'customers' => $data]);
    }
}
