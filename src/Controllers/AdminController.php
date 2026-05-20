<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Document;
use App\Models\EntranceExam;
use App\Models\Faculty;
use App\Models\OspekSchedule;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use App\Utils\Auth;
use App\Utils\Csrf;
use App\Utils\Flash;
use App\Utils\Http;
use App\Utils\View;

final class AdminController
{
    public function dashboard(): void
    {
        $user = Auth::requireUser(['admin', 'finance', 'superadmin']);

        $apps = Application::listAll();
        $payments = Payment::listAll('pending');

        View::render('admin/dashboard', [
            'user' => $user,
            'appsCount' => count($apps),
            'pendingPaymentsCount' => count($payments),
        ]);
    }

    public function applications(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        $status = isset($_GET['status']) ? (string)$_GET['status'] : null;
        $applications = Application::listAll($status && $status !== '' ? $status : null);
        View::render('admin/applications', ['applications' => $applications, 'status' => $status]);
    }

    public function viewApplication(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        $id = (int)($_GET['id'] ?? 0);
        $app = $id > 0 ? Application::findById($id) : null;
        if (!$app) {
            http_response_code(404);
            View::render('errors/404', ['path' => '/admin/applications/view']);
            return;
        }
        $docs = Document::forApplication((int)$app['id']);
        $exam = EntranceExam::forApplication((int)$app['id']);
        View::render('admin/application_view', ['app' => $app, 'documents' => $docs, 'exam' => $exam]);
    }

    public function reviewApplication(): void
    {
        $user = Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/applications');
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        $notes = trim((string)($_POST['review_notes'] ?? ''));
        if ($appId <= 0 || !in_array($status, ['reviewed', 'revise', 'rejected'], true)) {
            Flash::set('danger', 'Input review tidak valid.');
            Http::redirect('/pre-uas/public/admin/applications');
        }

        $app = Application::findById($appId);
        if (!$app) {
            Flash::set('danger', 'Aplikasi tidak ditemukan.');
            Http::redirect('/pre-uas/public/admin/applications');
        }

        Application::setStatus($appId, $status, $notes !== '' ? $notes : null);
        Candidate::updateStatus((int)$app['candidate_id'], $status === 'rejected' ? 'rejected' : 'doc_review');

        Flash::set('success', 'Review aplikasi tersimpan.');
        Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
    }

    public function verifyDocument(): void
    {
        $user = Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/applications');
        }

        $docId = (int)($_POST['document_id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        $appId = (int)($_POST['application_id'] ?? 0);
        $notes = trim((string)($_POST['notes'] ?? ''));

        if ($docId <= 0 || $appId <= 0 || !in_array($status, ['verified', 'rejected', 'revise'], true)) {
            Flash::set('danger', 'Input verifikasi dokumen tidak valid.');
            Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
        }

        Document::verify($docId, $status, (int)$user['id'], $notes !== '' ? $notes : null);
        Flash::set('success', 'Dokumen terverifikasi.');
        Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
    }

    public function exams(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        $exams = EntranceExam::listAll();
        View::render('admin/exams', ['exams' => $exams]);
    }

    public function scheduleExam(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/exams');
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $examDate = (string)($_POST['exam_date'] ?? '');
        $location = trim((string)($_POST['exam_location'] ?? ''));
        $type = (string)($_POST['exam_type'] ?? 'Written');

        if ($appId <= 0 || $examDate === '' || !in_array($type, ['Written', 'Online'], true)) {
            Flash::set('danger', 'Input jadwal ujian tidak valid.');
            Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
        }

        EntranceExam::schedule($appId, $examDate, $location, $type);
        $app = Application::findById($appId);
        if ($app) {
            Candidate::updateStatus((int)$app['candidate_id'], 'exam_scheduled');
        }

        Flash::set('success', 'Jadwal ujian tersimpan.');
        Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
    }

    public function setExamScore(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/exams');
        }

        $examId = (int)($_POST['exam_id'] ?? 0);
        $appId = (int)($_POST['application_id'] ?? 0);
        $score = (float)($_POST['score'] ?? 0);
        $status = (string)($_POST['status'] ?? 'completed');

        if ($examId <= 0 || $appId <= 0 || $score < 0 || $score > 100 || !in_array($status, ['completed', 'passed', 'failed', 'absent'], true)) {
            Flash::set('danger', 'Input score tidak valid.');
            Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
        }

        EntranceExam::setScore($examId, $score, $status);
        Flash::set('success', 'Score ujian tersimpan.');
        Http::redirect('/pre-uas/public/admin/applications/view?id=' . $appId);
    }

    public function results(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        $applications = Application::listAll();
        View::render('admin/results', ['applications' => $applications]);
    }

    public function publishResult(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/results');
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $decision = (string)($_POST['decision'] ?? '');
        if ($appId <= 0 || !in_array($decision, ['accepted', 'rejected'], true)) {
            Flash::set('danger', 'Input hasil tidak valid.');
            Http::redirect('/pre-uas/public/admin/results');
        }

        $app = Application::findById($appId);
        if (!$app) {
            Flash::set('danger', 'Aplikasi tidak ditemukan.');
            Http::redirect('/pre-uas/public/admin/results');
        }

        Application::setStatus($appId, $decision === 'accepted' ? 'approved' : 'rejected', null);
        Candidate::updateStatus((int)$app['candidate_id'], $decision);

        Flash::set('success', 'Hasil dipublish.');
        Http::redirect('/pre-uas/public/admin/results');
    }

    public function payments(): void
    {
        Auth::requireUser(['finance', 'admin', 'superadmin']);
        $status = isset($_GET['status']) ? (string)$_GET['status'] : null;
        $payments = Payment::listAll($status && $status !== '' ? $status : null);
        View::render('admin/payments', ['payments' => $payments, 'status' => $status]);
    }

    public function verifyPayment(): void
    {
        $user = Auth::requireUser(['finance', 'admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/payments');
        }

        $paymentId = (int)($_POST['payment_id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        if ($paymentId <= 0 || !in_array($status, ['verified', 'completed', 'rejected'], true)) {
            Flash::set('danger', 'Input verifikasi tidak valid.');
            Http::redirect('/pre-uas/public/admin/payments');
        }

        Payment::verify($paymentId, $status, (int)$user['id']);
        Flash::set('success', 'Pembayaran terverifikasi.');
        Http::redirect('/pre-uas/public/admin/payments');
    }

    public function ospek(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        $schedules = OspekSchedule::listForProgram(null);
        $programs = Program::allWithFaculty();
        View::render('admin/ospek', ['schedules' => $schedules, 'programs' => $programs]);
    }

    public function createOspek(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/ospek');
        }

        $programIdRaw = trim((string)($_POST['program_id'] ?? ''));
        $programId = ($programIdRaw === '' || $programIdRaw === 'ALL') ? null : (int)$programIdRaw;
        $title = trim((string)($_POST['title'] ?? ''));
        $startAt = (string)($_POST['start_at'] ?? '');
        $endAt = (string)($_POST['end_at'] ?? '');
        $location = trim((string)($_POST['location'] ?? ''));

        if ($title === '' || $startAt === '' || $endAt === '') {
            Flash::set('danger', 'Title, start, end wajib diisi.');
            Http::redirect('/pre-uas/public/admin/ospek');
        }

        if ($programId !== null && !Program::findById($programId)) {
            Flash::set('danger', 'Program ID tidak ditemukan. Pilih dari dropdown atau pakai ALL.');
            Http::redirect('/pre-uas/public/admin/ospek');
        }

        OspekSchedule::create($programId, $title, $startAt, $endAt, $location);
        Flash::set('success', 'Jadwal OSPEK dibuat.');
        Http::redirect('/pre-uas/public/admin/ospek');
    }

    public function faculties(): void
    {
        Auth::requireUser(['superadmin']);
        $faculties = Faculty::all();
        View::render('admin/faculties', ['faculties' => $faculties]);
    }

    public function createFaculty(): void
    {
        Auth::requireUser(['superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/faculties');
        }

        $name = trim((string)($_POST['faculty_name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        if ($name === '') {
            Flash::set('danger', 'Nama fakultas wajib diisi.');
            Http::redirect('/pre-uas/public/admin/faculties');
        }

        Faculty::create($name, $description !== '' ? $description : null);
        Flash::set('success', 'Fakultas berhasil ditambahkan.');
        Http::redirect('/pre-uas/public/admin/faculties');
    }

    public function programs(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        $programs = Program::allWithFaculty();
        $faculties = Faculty::all();
        View::render('admin/programs', ['programs' => $programs, 'faculties' => $faculties]);
    }

    public function createProgram(): void
    {
        Auth::requireUser(['admin', 'superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/programs');
        }

        $facultyId = (int)($_POST['faculty_id'] ?? 0);
        $name = trim((string)($_POST['program_name'] ?? ''));
        $capacity = (int)($_POST['capacity'] ?? 0);
        $requirements = trim((string)($_POST['requirements'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $isActive = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true;

        if ($facultyId <= 0 || $name === '' || $capacity < 0) {
            Flash::set('danger', 'Input program tidak valid.');
            Http::redirect('/pre-uas/public/admin/programs');
        }

        Program::create(
            $facultyId,
            $name,
            $capacity,
            $requirements !== '' ? $requirements : null,
            $description !== '' ? $description : null,
            $isActive
        );
        Flash::set('success', 'Program berhasil ditambahkan.');
        Http::redirect('/pre-uas/public/admin/programs');
    }

    public function users(): void
    {
        Auth::requireUser(['superadmin']);
        $users = User::listAll();
        $roles = db()->query('SELECT id, role_key, role_name FROM roles ORDER BY id ASC')->fetchAll() ?: [];
        View::render('admin/users', ['users' => $users, 'roles' => $roles]);
    }

    public function createUser(): void
    {
        Auth::requireUser(['superadmin']);
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/admin/users');
        }

        $roleId = (int)($_POST['role_id'] ?? 0);
        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $username = strtolower(trim((string)($_POST['username'] ?? '')));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $isActive = isset($_POST['is_active']) ? true : false;

        if ($roleId <= 0 || $fullName === '' || $username === '' || $password === '') {
            Flash::set('danger', 'Role, nama, username, dan password wajib diisi.');
            Http::redirect('/pre-uas/public/admin/users');
        }

        $role = db()->prepare('SELECT id FROM roles WHERE id = :id');
        $role->execute(['id' => $roleId]);
        if (!$role->fetch()) {
            Flash::set('danger', 'Role tidak valid.');
            Http::redirect('/pre-uas/public/admin/users');
        }

        if (User::findByUsername($username)) {
            Flash::set('danger', 'Username sudah dipakai.');
            Http::redirect('/pre-uas/public/admin/users');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        User::create($roleId, $fullName, $username, $email !== '' ? $email : null, $hash, $isActive);

        Flash::set('success', 'User berhasil dibuat.');
        Http::redirect('/pre-uas/public/admin/users');
    }
}
