<?php
class CustomerController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('customers');
        $page     = 'customers';
        $action   = 'index';
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    public function store(): void {
        Auth::requirePermission('customers');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=customers');
        }

        $name = Helper::sanitize($_POST['name'] ?? '');
        if (empty($name)) {
            Session::flash('error', 'اسم العميل مطلوب');
            Helper::redirect('?page=customers');
        }

        $data = [
            'name'         => $name,
            'phone'        => Helper::sanitize($_POST['phone']  ?? ''),
            'email'        => Helper::sanitize($_POST['email']  ?? ''),
            'address'      => Helper::sanitize($_POST['address'] ?? ''),
            'notes'        => Helper::sanitize($_POST['notes']  ?? ''),
            'credit_limit' => (float)($_POST['credit_limit'] ?? 0),
        ];

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            // تعديل الرصيد يدوياً إن طُلب
            if (!empty($_POST['adjust_balance'])) {
                $data['balance'] = (float)($_POST['new_balance'] ?? 0);
            }
            $this->db->update('customers', $data, 'id = ?', [$id]);
            Session::flash('success', 'تم تحديث بيانات العميل «' . $name . '»');
        } else {
            $data['balance'] = (float)($_POST['opening_balance'] ?? 0);
            $this->db->insert('customers', $data);
            Session::flash('success', 'تمت إضافة العميل «' . $name . '» بنجاح');
        }

        Helper::redirect('?page=customers');
    }

    public function update(): void {
        $this->store();
    }

    public function delete(): void {
        Auth::requirePermission('customers');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 1) { // حماية عميل نقدي (id=1)
            Session::flash('error', 'لا يمكن حذف هذا العميل');
            Helper::redirect('?page=customers');
        }
        $c = $this->db->fetchOne("SELECT name, balance FROM customers WHERE id = ?", [$id]);
        if (!$c) { Helper::redirect('?page=customers'); }
        if ((float)$c['balance'] > 0) {
            Session::flash('error', 'لا يمكن حذف العميل «' . $c['name'] . '» لأن لديه رصيداً مستحقاً');
            Helper::redirect('?page=customers');
        }
        $this->db->delete('customers', 'id = ?', [$id]);
        Session::flash('success', 'تم حذف العميل «' . $c['name'] . '»');
        Helper::redirect('?page=customers');
    }

    public function getOne(): void {
        $id = (int)($_GET['id'] ?? 0);
        $c  = $this->db->fetchOne("SELECT * FROM customers WHERE id = ?", [$id]);
        Helper::jsonResponse(['success' => (bool)$c, 'customer' => $c]);
    }

    public function getStats(): void {
        $id = (int)($_GET['id'] ?? 0);
        $purchases = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(total),0) FROM invoices WHERE customer_id=? AND type='sale' AND status!='returned'",
            [$id]
        );
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM invoices WHERE customer_id=? AND type='sale'",
            [$id]
        );
        Helper::jsonResponse([
            'success'       => true,
            'purchases'     => $purchases,
            'invoice_count' => $count,
        ]);
    }

    public function search(): void {
        $q = Helper::sanitize($_GET['q'] ?? '');
        $customers = $this->db->fetchAll(
            "SELECT id, name, phone, balance FROM customers
             WHERE name LIKE ? OR phone LIKE ?
             ORDER BY name LIMIT 10",
            ["%$q%", "%$q%"]
        );
        Helper::jsonResponse(['customers' => $customers]);
    }

    public function payment(): void {
        Auth::requirePermission('customers');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Helper::redirect('?page=customers');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=customers');
        }

        $id     = (int)($_POST['customer_id'] ?? 0);
        $amount = (float)($_POST['payment_amount'] ?? 0);

        if ($id <= 0 || $amount <= 0) {
            Session::flash('error', 'بيانات الدفعة غير صحيحة');
            Helper::redirect('?page=customers');
        }

        $customer = $this->db->fetchOne("SELECT * FROM customers WHERE id = ?", [$id]);
        if (!$customer) {
            Session::flash('error', 'العميل غير موجود');
            Helper::redirect('?page=customers');
        }

        $newBalance = max(0, (float)$customer['balance'] - $amount);
        $this->db->update('customers', ['balance' => $newBalance], 'id = ?', [$id]);

        Session::flash('success',
            'تم تسجيل دفعة ' . number_format($amount, 2) . ' للعميل «' . $customer['name'] . '»'
        );
        Helper::redirect('?page=customers');
    }

    public function statement(): void {
        Auth::requirePermission('customers');
        $id = (int)($_GET['id'] ?? 0);

        $customer = $this->db->fetchOne("SELECT * FROM customers WHERE id = ?", [$id]);
        if (!$customer) Helper::redirect('?page=customers');

        $from = Helper::sanitize($_GET['from'] ?? date('Y-m-01'));
        $to   = Helper::sanitize($_GET['to']   ?? date('Y-m-d'));

        $invoices = $this->db->fetchAll(
            "SELECT i.*, u.full_name as cashier_name
             FROM invoices i
             LEFT JOIN users u ON i.user_id = u.id
             WHERE i.customer_id = ?
               AND DATE(i.created_at) BETWEEN ? AND ?
             ORDER BY i.created_at ASC",
            [$id, $from, $to]
        );

        $stats = $this->db->fetchOne(
            "SELECT
               COALESCE(SUM(CASE WHEN type='sale' AND status!='returned' THEN total ELSE 0 END), 0) as total_sales,
               COALESCE(SUM(CASE WHEN type='return' THEN total ELSE 0 END), 0) as total_returns,
               COALESCE(SUM(CASE WHEN type='sale' AND status!='returned' THEN paid_amount ELSE 0 END), 0) as total_paid,
               COUNT(CASE WHEN type='sale' THEN 1 END) as invoice_count
             FROM invoices
             WHERE customer_id = ? AND DATE(created_at) BETWEEN ? AND ?",
            [$id, $from, $to]
        );

        $settings = Helper::getSettings();
        require ROOT . '/app/Views/customers/statement.php';
    }
}
