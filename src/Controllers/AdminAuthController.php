<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Utils\Csrf;
use App\Utils\Flash;
use App\Utils\Http;
use App\Utils\View;

final class AdminAuthController
{
    public function showLogin(): void
    {
        View::render('admin/login');
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/login');
        }

        $username = strtolower(trim((string)($_POST['username'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        $user = $username !== '' ? User::findByUsername($username) : null;
        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            Flash::set('danger', 'Username atau password salah.');
            Http::redirect('/pre-uas/public/admin/login');
        }

        if (!(int)$user['is_active']) {
            Flash::set('danger', 'Akun nonaktif.');
            Http::redirect('/pre-uas/public/admin/login');
        }

        $_SESSION['user_id'] = (int)$user['id'];
        Flash::set('success', 'Login admin berhasil.');
        Http::redirect('/pre-uas/public/admin/dashboard');
    }

    public function logout(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/');
        }

        unset($_SESSION['user_id']);
        Flash::set('success', 'Logout berhasil.');
        Http::redirect('/pre-uas/public/');
    }
}
