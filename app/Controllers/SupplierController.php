<?php
class SupplierController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('suppliers');
        $page     = 'suppliers';
        $action   = 'index';
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    public function store(): void {
        Auth::requirePermission('suppliers');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=suppliers');
        }

        $name = Helper::sanitize($_POST['name'] ?? '');
        if (empty($name)) {
            Session::flash('error', 'اسم المورد مطلوب');
            Helper::redirect('?page=suppliers');
        }

        $data = [
            'name'    => $name,
            'phone'   => Helper::sanitize($_POST['phone']   ?? ''),
            'email'   => Helper::sanitize($_POST['email']   ?? ''),
            'address' => Helper::sanitize($_POST['address'] ?? ''),
            'notes'   => Helper::sanitize($_POST['notes']   ?? ''),
        ];

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->db->update('suppliers', $data, 'id = ?', [$id]);
            Session::flash('success', 'تم تحديث بيانات المورد «' . $name . '»');
        } else {
            $this->db->insert('suppliers', $data);
            Session::flash('success', 'تمت إضافة المورد «' . $name . '» بنجاح');
        }

        Helper::redirect('?page=suppliers');
    }

    public function update(): void {
        $this->store();
    }

    public function delete(): void {
        Auth::requirePermission('suppliers');
        $id = (int)($_GET['id'] ?? 0);
        $s  = $this->db->fetchOne("SELECT name, balance FROM suppliers WHERE id = ?", [$id]);
        if (!$s) { Helper::redirect('?page=suppliers'); }
        if ((float)$s['balance'] > 0) {
            Session::flash('error', 'لا يمكن حذف المورد «' . $s['name'] . '» لأن لديه رصيداً مستحقاً');
            Helper::redirect('?page=suppliers');
        }
        $this->db->delete('suppliers', 'id = ?', [$id]);
        Session::flash('success', 'تم حذف المورد «' . $s['name'] . '»');
        Helper::redirect('?page=suppliers');
    }

    public function getOne(): void {
        $id = (int)($_GET['id'] ?? 0);
        $s  = $this->db->fetchOne("SELECT * FROM suppliers WHERE id = ?", [$id]);
        Helper::jsonResponse(['success' => (bool)$s, 'supplier' => $s]);
    }

    // سداد مستحقات مورد
    public function payment(): void {
        Auth::requirePermission('suppliers');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Helper::redirect('?page=suppliers');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=suppliers');
        }

        $id     = (int)($_POST['supplier_id'] ?? 0);
        $amount = (float)($_POST['payment_amount'] ?? 0);

        if ($id <= 0 || $amount <= 0) {
            Session::flash('error', 'بيانات غير صحيحة');
            Helper::redirect('?page=suppliers');
        }

        $supplier = $this->db->fetchOne("SELECT * FROM suppliers WHERE id = ?", [$id]);
        if (!$supplier) { Helper::redirect('?page=suppliers'); }

        $newBalance = max(0, (float)$supplier['balance'] - $amount);
        $this->db->update('suppliers', ['balance' => $newBalance], 'id = ?', [$id]);

        Session::flash('success',
            'تم تسجيل دفعة ' . number_format($amount, 2) . ' للمورد «' . $supplier['name'] . '»'
        );
        Helper::redirect('?page=suppliers');
    }
}
