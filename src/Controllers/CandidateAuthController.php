<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Candidate;
use App\Utils\Csrf;
use App\Utils\Flash;
use App\Utils\Http;
use App\Utils\View;

final class CandidateAuthController
{
    public function showRegister(): void
    {
        View::render('candidate/register');
    }

    public function register(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/register');
        }

        $firstName = trim((string)($_POST['first_name'] ?? ''));
        $lastName = trim((string)($_POST['last_name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        if ($firstName === '' || $email === '' || $password === '') {
            Flash::set('danger', 'Nama depan, email, dan password wajib diisi.');
            Http::redirect('/pre-uas/public/candidate/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::set('danger', 'Email tidak valid.');
            Http::redirect('/pre-uas/public/candidate/register');
        }

        if (Candidate::findByEmail($email)) {
            Flash::set('warning', 'Email sudah terdaftar, silakan login.');
            Http::redirect('/pre-uas/public/candidate/login');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id = Candidate::create($firstName, $lastName, $email, $hash);
        $_SESSION['candidate_id'] = $id;
        Flash::set('success', 'Registrasi berhasil.');
        Http::redirect('/pre-uas/public/candidate/dashboard');
    }

    public function showLogin(): void
    {
        View::render('candidate/login');
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/login');
        }

        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        $candidate = $email !== '' ? Candidate::findByEmail($email) : null;
        if (!$candidate || !password_verify($password, (string)$candidate['password_hash'])) {
            Flash::set('danger', 'Email atau password salah.');
            Http::redirect('/pre-uas/public/candidate/login');
        }

        $_SESSION['candidate_id'] = (int)$candidate['id'];
        Flash::set('success', 'Login berhasil.');
        Http::redirect('/pre-uas/public/candidate/dashboard');
    }

    public function logout(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/');
        }

        unset($_SESSION['candidate_id']);
        Flash::set('success', 'Logout berhasil.');
        Http::redirect('/pre-uas/public/');
    }
}

