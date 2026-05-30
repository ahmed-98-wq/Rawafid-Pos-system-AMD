<?php
class UserController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index(): void {
        Auth::requirePermission('manage_users');
        $page     = 'users';
        $action   = 'index';
        $settings = Helper::getSettings();
        require ROOT . '/app/Views/layouts/main.php';
    }

    public function store(): void {
        Auth::requirePermission('manage_users');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'طلب غير صالح');
            Helper::redirect('?page=users');
        }

        $fullName = Helper::sanitize($_POST['full_name'] ?? '');
        $username = Helper::sanitize($_POST['username']  ?? '');
        $role     = in_array($_POST['role'] ?? '', ['admin','supervisor','cashier','warehouse'])
                    ? $_POST['role'] : 'cashier';

        if (empty($fullName) || empty($username)) {
            Session::flash('error', 'الاسم الكامل واسم الدخول مطلوبان');
            Helper::redirect('?page=users');
        }

        $data = [
            'full_name' => $fullName,
            'username'  => $username,
            'role'      => $role,
            'phone'     => Helper::sanitize($_POST['phone'] ?? ''),
            'email'     => Helper::sanitize($_POST['email'] ?? ''),
            'is_active' => 1,
        ];

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            // تعديل — تحديث كلمة المرور فقط إن أُدخلت
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    Session::flash('error', 'كلمة المرور يجب أن تكون 6 أحرف على الأقل');
                    Helper::redirect('?page=users');
                }
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }
            // منع تغيير صلاحية المدير الأخير
            if ($role !== 'admin') {
                $adminCount = (int)$this->db->fetchColumn(
                    "SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1 AND id != ?", [$id]
                );
                if ($adminCount === 0) {
                    Session::flash('error', 'يجب الإبقاء على مدير واحد على الأقل');
                    Helper::redirect('?page=users');
                }
            }
            $this->db->update('users', $data, 'id = ?', [$id]);
            Session::flash('success', 'تم تحديث بيانات المستخدم «' . $fullName . '»');
        } else {
            // إضافة — كلمة مرور مطلوبة
            if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
                Session::flash('error', 'كلمة المرور مطلوبة (6 أحرف على الأقل)');
                Helper::redirect('?page=users');
            }
            // تحقق من تكرار اسم المستخدم
            $exists = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM users WHERE username = ?", [$username]
            );
            if ($exists > 0) {
                Session::flash('error', 'اسم المستخدم «' . $username . '» مستخدم مسبقاً');
                Helper::redirect('?page=users');
            }
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $this->db->insert('users', $data);
            Session::flash('success', 'تمت إضافة المستخدم «' . $fullName . '» بنجاح');
        }

        Helper::redirect('?page=users');
    }

    public function update(): void {
        $this->store();
    }

    public function toggle(): void {
        Auth::requirePermission('manage_users');
        $id = (int)($_GET['id'] ?? 0);
        if ($id === Auth::id()) {
            Session::flash('error', 'لا يمكنك تعطيل حسابك الخاص');
            Helper::redirect('?page=users');
        }
        $u = $this->db->fetchOne("SELECT is_active, role FROM users WHERE id = ?", [$id]);
        if (!$u) { Helper::redirect('?page=users'); }

        // منع تعطيل آخر مدير نشط
        if ($u['role'] === 'admin' && $u['is_active']) {
            $activeAdmins = (int)$this->db->fetchColumn(
                "SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1"
            );
            if ($activeAdmins <= 1) {
                Session::flash('error', 'لا يمكن تعطيل آخر مدير نشط في النظام');
                Helper::redirect('?page=users');
            }
        }

        $newState = $u['is_active'] ? 0 : 1;
        $this->db->update('users', ['is_active' => $newState], 'id = ?', [$id]);
        Session::flash('success', $newState ? 'تم تفعيل المستخدم' : 'تم تعطيل المستخدم');
        Helper::redirect('?page=users');
    }

    public function getOne(): void {
        $id = (int)($_GET['id'] ?? 0);
        $u  = $this->db->fetchOne(
            "SELECT id, full_name, username, role, phone, email, is_active FROM users WHERE id = ?",
            [$id]
        );
        Helper::jsonResponse(['success' => (bool)$u, 'user' => $u]);
    }

    public function resetPassword(): void {
        Auth::requirePermission('manage_users');
        $id   = (int)($_POST['user_id'] ?? 0);
        $pass = $_POST['new_password'] ?? '';
        if ($id <= 0 || strlen($pass) < 6) {
            Helper::jsonResponse(['success' => false, 'message' => 'بيانات غير صالحة']);
        }
        $hashed = password_hash($pass, PASSWORD_BCRYPT);
        $this->db->update('users', ['password' => $hashed], 'id = ?', [$id]);
        Helper::jsonResponse(['success' => true, 'message' => 'تم تغيير كلمة المرور بنجاح']);
    }
}
