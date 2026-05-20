<?php
declare(strict_types=1);

namespace App;

use App\Http\Router;

final class App
{
    public function run(): void
    {
        $router = new Router();

        // Public (default landing = candidate login/register)
        $router->get('/', [Controllers\CandidateAuthController::class, 'showLogin']);

        // Candidate auth + flow
        $router->get('/candidate/register', [Controllers\CandidateAuthController::class, 'showRegister']);
        $router->post('/candidate/register', [Controllers\CandidateAuthController::class, 'register']);
        $router->get('/candidate/login', [Controllers\CandidateAuthController::class, 'showLogin']);
        $router->post('/candidate/login', [Controllers\CandidateAuthController::class, 'login']);
        $router->post('/candidate/logout', [Controllers\CandidateAuthController::class, 'logout']);

        $router->get('/candidate/dashboard', [Controllers\CandidateController::class, 'dashboard']);
        $router->get('/candidate/application', [Controllers\CandidateController::class, 'showApplication']);
        $router->post('/candidate/application', [Controllers\CandidateController::class, 'saveApplication']);
        $router->post('/candidate/application/submit', [Controllers\CandidateController::class, 'submitApplication']);
        $router->get('/candidate/documents', [Controllers\CandidateController::class, 'documents']);
        $router->post('/candidate/documents/upload', [Controllers\CandidateController::class, 'uploadDocument']);
        $router->get('/candidate/exam', [Controllers\CandidateController::class, 'exam']);
        $router->get('/candidate/results', [Controllers\CandidateController::class, 'results']);
        $router->get('/candidate/enrollment', [Controllers\CandidateController::class, 'enrollment']);
        $router->post('/candidate/enrollment', [Controllers\CandidateController::class, 'createEnrollment']);
        $router->get('/candidate/payment', [Controllers\CandidateController::class, 'payment']);
        $router->post('/candidate/payment/upload', [Controllers\CandidateController::class, 'uploadPayment']);
        $router->get('/candidate/ospek', [Controllers\CandidateController::class, 'ospek']);

        // Admin auth + flow
        $router->get('/admin/login', [Controllers\AdminAuthController::class, 'showLogin']);
        $router->post('/admin/login', [Controllers\AdminAuthController::class, 'login']);
        $router->post('/admin/logout', [Controllers\AdminAuthController::class, 'logout']);

        $router->get('/admin/dashboard', [Controllers\AdminController::class, 'dashboard']);
        $router->get('/admin/applications', [Controllers\AdminController::class, 'applications']);
        $router->get('/admin/applications/view', [Controllers\AdminController::class, 'viewApplication']);
        $router->post('/admin/applications/review', [Controllers\AdminController::class, 'reviewApplication']);
        $router->post('/admin/documents/verify', [Controllers\AdminController::class, 'verifyDocument']);
        $router->get('/admin/exams', [Controllers\AdminController::class, 'exams']);
        $router->post('/admin/exams/schedule', [Controllers\AdminController::class, 'scheduleExam']);
        $router->post('/admin/exams/score', [Controllers\AdminController::class, 'setExamScore']);
        $router->get('/admin/results', [Controllers\AdminController::class, 'results']);
        $router->post('/admin/results/publish', [Controllers\AdminController::class, 'publishResult']);
        $router->get('/admin/payments', [Controllers\AdminController::class, 'payments']);
        $router->post('/admin/payments/verify', [Controllers\AdminController::class, 'verifyPayment']);
        $router->get('/admin/ospek', [Controllers\AdminController::class, 'ospek']);
        $router->post('/admin/ospek/create', [Controllers\AdminController::class, 'createOspek']);
        $router->get('/admin/faculties', [Controllers\AdminController::class, 'faculties']);
        $router->post('/admin/faculties/create', [Controllers\AdminController::class, 'createFaculty']);
        $router->get('/admin/programs', [Controllers\AdminController::class, 'programs']);
        $router->post('/admin/programs/create', [Controllers\AdminController::class, 'createProgram']);
        $router->get('/admin/users', [Controllers\AdminController::class, 'users']);
        $router->post('/admin/users/create', [Controllers\AdminController::class, 'createUser']);

        $router->dispatch();
    }
}
