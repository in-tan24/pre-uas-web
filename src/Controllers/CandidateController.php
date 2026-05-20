<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Document;
use App\Models\EntranceExam;
use App\Models\EnrollmentRecord;
use App\Models\OspekSchedule;
use App\Models\Payment;
use App\Models\Program;
use App\Utils\Auth;
use App\Utils\Csrf;
use App\Utils\FileUpload;
use App\Utils\Flash;
use App\Utils\Http;
use App\Utils\View;

final class CandidateController
{
    public function dashboard(): void
    {
        $candidate = Auth::requireCandidate();
        $application = Application::findByCandidateId((int)$candidate['id']);
        $enrollment = EnrollmentRecord::forCandidate((int)$candidate['id']);
        View::render('candidate/dashboard', [
            'candidate' => $candidate,
            'application' => $application,
            'enrollment' => $enrollment,
        ]);
    }

    public function showApplication(): void
    {
        $candidate = Auth::requireCandidate();
        $application = Application::findByCandidateId((int)$candidate['id']);
        $programs = Program::all();
        View::render('candidate/application', [
            'candidate' => $candidate,
            'application' => $application,
            'programs' => $programs,
        ]);
    }

    public function saveApplication(): void
    {
        $candidate = Auth::requireCandidate();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/application');
        }

        $programId = (int)($_POST['program_id'] ?? 0);
        $applicationDate = (string)($_POST['application_date'] ?? date('Y-m-d'));
        if ($programId <= 0 || !Program::findById($programId)) {
            Flash::set('danger', 'Program tidak valid.');
            Http::redirect('/pre-uas/public/candidate/application');
        }

        $appId = Application::createOrUpdate((int)$candidate['id'], $programId, $applicationDate);
        Flash::set('success', 'Aplikasi tersimpan.');
        Http::redirect('/pre-uas/public/candidate/application');
    }

    public function submitApplication(): void
    {
        $candidate = Auth::requireCandidate();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/application');
        }

        $app = Application::findByCandidateId((int)$candidate['id']);
        if (!$app) {
            Flash::set('warning', 'Buat aplikasi terlebih dahulu.');
            Http::redirect('/pre-uas/public/candidate/application');
        }
        if (!in_array((string)$app['status'], ['pending', 'revise'], true)) {
            Flash::set('warning', 'Aplikasi sudah disubmit / diproses.');
            Http::redirect('/pre-uas/public/candidate/dashboard');
        }

        Application::submit((int)$app['id']);
        Candidate::updateStatus((int)$candidate['id'], 'submitted');
        Flash::set('success', 'Aplikasi berhasil disubmit. Tunggu review berkas.');
        Http::redirect('/pre-uas/public/candidate/dashboard');
    }

    public function documents(): void
    {
        $candidate = Auth::requireCandidate();
        $app = Application::findByCandidateId((int)$candidate['id']);
        $docs = $app ? Document::forApplication((int)$app['id']) : [];
        View::render('candidate/documents', [
            'candidate' => $candidate,
            'application' => $app,
            'documents' => $docs,
            'requiredTypes' => ['KTP', 'Ijazah', 'Transkrip', 'Pas Foto'],
        ]);
    }

    public function uploadDocument(): void
    {
        $candidate = Auth::requireCandidate();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/documents');
        }

        $app = Application::findByCandidateId((int)$candidate['id']);
        if (!$app) {
            Flash::set('warning', 'Buat aplikasi terlebih dahulu.');
            Http::redirect('/pre-uas/public/candidate/application');
        }

        $type = trim((string)($_POST['document_type'] ?? ''));
        if ($type === '') {
            Flash::set('danger', 'Jenis dokumen wajib diisi.');
            Http::redirect('/pre-uas/public/candidate/documents');
        }

        $upload = FileUpload::save('document_file', 'documents', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], 10 * 1024 * 1024);
        if (!$upload['ok']) {
            Flash::set('danger', (string)($upload['error'] ?? 'Upload gagal.'));
            Http::redirect('/pre-uas/public/candidate/documents');
        }

        $existing = Document::findByApplicationAndType((int)$app['id'], $type);
        if ($existing) {
            Document::replace((int)$existing['id'], (string)$upload['path']);
            Flash::set('success', 'Dokumen berhasil diganti (replace).');
            Http::redirect('/pre-uas/public/candidate/documents');
        }

        Document::create((int)$app['id'], $type, (string)$upload['path']);
        Flash::set('success', 'Dokumen berhasil diupload.');
        Http::redirect('/pre-uas/public/candidate/documents');
    }

    public function exam(): void
    {
        $candidate = Auth::requireCandidate();
        $app = Application::findByCandidateId((int)$candidate['id']);
        $exam = $app ? EntranceExam::forApplication((int)$app['id']) : null;
        View::render('candidate/exam', ['application' => $app, 'exam' => $exam]);
    }

    public function results(): void
    {
        $candidate = Auth::requireCandidate();
        $app = Application::findByCandidateId((int)$candidate['id']);
        $exam = $app ? EntranceExam::forApplication((int)$app['id']) : null;
        View::render('candidate/results', [
            'candidate' => $candidate,
            'application' => $app,
            'exam' => $exam,
        ]);
    }

    public function enrollment(): void
    {
        $candidate = Auth::requireCandidate();
        $app = Application::findByCandidateId((int)$candidate['id']);
        $enrollment = EnrollmentRecord::forCandidate((int)$candidate['id']);
        View::render('candidate/enrollment', ['candidate' => $candidate, 'application' => $app, 'enrollment' => $enrollment]);
    }

    public function createEnrollment(): void
    {
        $candidate = Auth::requireCandidate();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/enrollment');
        }

        if ((string)$candidate['status'] !== 'accepted') {
            Flash::set('warning', 'Enrollment hanya untuk kandidat yang Accepted.');
            Http::redirect('/pre-uas/public/candidate/results');
        }

        $app = Application::findByCandidateId((int)$candidate['id']);
        if (!$app) {
            Flash::set('warning', 'Aplikasi tidak ditemukan.');
            Http::redirect('/pre-uas/public/candidate/dashboard');
        }

        $existing = EnrollmentRecord::forCandidate((int)$candidate['id']);
        if ($existing) {
            Flash::set('info', 'Enrollment sudah dibuat.');
            Http::redirect('/pre-uas/public/candidate/payment');
        }

        $studentId = 'STU' . date('Y') . str_pad((string)$candidate['id'], 6, '0', STR_PAD_LEFT);
        EnrollmentRecord::create((int)$candidate['id'], (int)$app['program_id'], $studentId);
        Candidate::updateStatus((int)$candidate['id'], 'enrolled');
        Flash::set('success', 'Enrollment berhasil. Silakan upload pembayaran.');
        Http::redirect('/pre-uas/public/candidate/payment');
    }

    public function payment(): void
    {
        $candidate = Auth::requireCandidate();
        $enrollment = EnrollmentRecord::forCandidate((int)$candidate['id']);
        $payments = $enrollment ? Payment::forEnrollment((int)$enrollment['id']) : [];
        View::render('candidate/payment', ['enrollment' => $enrollment, 'payments' => $payments]);
    }

    public function uploadPayment(): void
    {
        $candidate = Auth::requireCandidate();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::set('danger', 'CSRF token tidak valid.');
            Http::redirect('/pre-uas/public/candidate/payment');
        }

        $enrollment = EnrollmentRecord::forCandidate((int)$candidate['id']);
        if (!$enrollment) {
            Flash::set('warning', 'Buat enrollment terlebih dahulu.');
            Http::redirect('/pre-uas/public/candidate/enrollment');
        }

        $amount = (float)($_POST['amount'] ?? 0);
        $method = (string)($_POST['payment_method'] ?? 'transfer');
        if ($amount <= 0) {
            Flash::set('danger', 'Nominal pembayaran tidak valid.');
            Http::redirect('/pre-uas/public/candidate/payment');
        }

        $upload = FileUpload::save('receipt_file', 'receipts', ['pdf', 'jpg', 'jpeg', 'png'], 5 * 1024 * 1024);
        if (!$upload['ok']) {
            Flash::set('danger', (string)($upload['error'] ?? 'Upload gagal.'));
            Http::redirect('/pre-uas/public/candidate/payment');
        }

        Payment::create((int)$enrollment['id'], $amount, $method, (string)$upload['path']);
        Flash::set('success', 'Bukti pembayaran terkirim. Menunggu verifikasi.');
        Http::redirect('/pre-uas/public/candidate/payment');
    }

    public function ospek(): void
    {
        $candidate = Auth::requireCandidate();
        $enrollment = EnrollmentRecord::forCandidate((int)$candidate['id']);
        $programId = $enrollment ? (int)$enrollment['program_id'] : null;
        $schedules = OspekSchedule::listForProgram($programId);
        View::render('candidate/ospek', ['enrollment' => $enrollment, 'schedules' => $schedules]);
    }
}
