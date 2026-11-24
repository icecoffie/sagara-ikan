<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }

        if ($this->isPost()) {
            $this->guardPost();
            $email = trim((string) post('email'));
            $password = (string) post('password');

            $user = Auth::attempt($email, $password);
            if ($user) {
                log_activity('login', 'User masuk: ' . $user['email']);
                flash('success', 'Selamat datang, ' . $user['name'] . '!');
                redirect('dashboard');
            }

            flash('error', 'Email atau kata sandi salah.');
            flash_input(['email' => $email]);
            redirect('login');
        }

        $flashes = flash();
        $data = [
            'appName'  => Config::get('app_name', 'Sagara Resto'),
            'error'    => $flashes['error'] ?? null,
            'oldEmail' => old('email'),
        ];
        clear_old();
        $this->raw('auth/login', $data);
    }

    public function logout(): void
    {
        if (Auth::check()) {
            log_activity('logout', 'User keluar');
        }
        Auth::logout();
        redirect('login');
    }
}
