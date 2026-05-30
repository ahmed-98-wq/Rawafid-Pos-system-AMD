<?php
class AuthController {
    public function index(): void {
        $this->login();
    }

    public function login(): void {
        if (Auth::isLoggedIn()) {
            Helper::redirect('?page=dashboard');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
                $error = 'طلب غير صالح، يرجى المحاولة مرة أخرى';
            } else {
                $username = Helper::sanitize($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($username) || empty($password)) {
                    $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
                } else {
                    $user = Auth::attempt($username, $password);
                    if ($user) {
                        Auth::login($user);
                        if ($_POST['remember'] ?? false) {
                            setcookie('remember_user', $username, time() + 86400 * 30, '/', '', false, true);
                        }
                        Helper::redirect('?page=dashboard');
                    } else {
                        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
                    }
                }
            }
        }

        $settings = Helper::getSettings();
        require ROOT . '/app/Views/auth/login.php';
    }

    public function logout(): void {
        Auth::logout();
        Helper::redirect('?page=login');
    }
}
