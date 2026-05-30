<?php
class DashboardController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('dashboard');

        // هذان المتغيران ضروريان للـ layout
        $page   = 'dashboard';
        $action = 'index';

        $today = date('Y-m-d');
        $month = date('Y-m');

        $todaySales = $this->db->fetchOne(
            "SELECT COUNT(*) as count, COALESCE(SUM(total),0) as total
             FROM invoices WHERE DATE(created_at)=? AND type='sale' AND status!='returned'",
            [$today]
        );

        $monthSales = $this->db->fetchOne(
            "SELECT COALESCE(SUM(total),0) as total,
                    COALESCE(SUM(total-discount_amount-tax_amount),0) as profit
             FROM invoices WHERE DATE_FORMAT(created_at,'%Y-%m')=? AND type='sale' AND status!='returned'",
            [$month]
        );

        $newCustomers = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM customers WHERE DATE(created_at)=?", [$today]
        );

        $lowStockCount = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE stock_qty <= min_stock_alert AND is_active=1"
        );

        $outOfStock = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE stock_qty <= 0 AND is_active=1"
        );

        $weeklySales = $this->db->fetchAll(
            "SELECT DATE(created_at) as day, COALESCE(SUM(total),0) as total
             FROM invoices
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
               AND type='sale' AND status!='returned'
             GROUP BY DATE(created_at) ORDER BY day ASC"
        );

        $recentInvoices = $this->db->fetchAll(
            "SELECT i.*, c.name as customer_name, u.full_name as cashier_name
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             LEFT JOIN users u ON i.user_id = u.id
             ORDER BY i.created_at DESC LIMIT 10"
        );

        $lowStockProducts = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock_qty <= p.min_stock_alert AND p.is_active=1
             ORDER BY p.stock_qty ASC LIMIT 8"
        );

        $topProducts = $this->db->fetchAll(
            "SELECT p.name, SUM(ii.quantity) as total_qty, SUM(ii.total) as total_revenue
             FROM invoice_items ii
             JOIN invoices i ON ii.invoice_id = i.id
             JOIN products p ON ii.product_id = p.id
             WHERE DATE_FORMAT(i.created_at,'%Y-%m')=? AND i.type='sale'
             GROUP BY ii.product_id
             ORDER BY total_qty DESC LIMIT 5",
            [$month]
        );

        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }
}
