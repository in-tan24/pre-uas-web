<?php
declare(strict_types=1);

session_start();

const APP_NAME = 'PMB CampusFlow';

$programs = [
    'Informatika' => 'Fakultas Teknologi dan Rekayasa',
    'Sistem Informasi' => 'Fakultas Teknologi dan Rekayasa',
    'Manajemen' => 'Fakultas Ekonomi dan Bisnis',
    'Akuntansi' => 'Fakultas Ekonomi dan Bisnis',
    'Ilmu Komunikasi' => 'Fakultas Sosial dan Humaniora',
];

$steps = [
    'register' => ['Pendaftaran Online', 'Isi biodata dan pilihan program studi.'],
    'documents' => ['Seleksi Berkas', 'Unggah dokumen dan tunggu verifikasi panitia.'],
    'exam' => ['Ujian Masuk', 'Kerjakan ujian tulis atau asesmen online.'],
    'result' => ['Pengumuman Hasil', 'Lihat keputusan kelulusan seleksi.'],
    'payment' => ['Daftar Ulang & Pembayaran', 'Lengkapi registrasi dan biaya awal.'],
    'ospek' => ['OSPEK', 'Ikuti pengenalan kampus dan komunitas.'],
];

function base_url(string $path = ''): string
{
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return ($base === '' ? '' : $base) . '/index.php' . $path;
}

function asset_url(string $path): string
{
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function redirect_to(string $path): never
{
    header('Location: ' . base_url($path));
    exit;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    $email = $_SESSION['candidate_email'] ?? null;
    return $email ? ($_SESSION['candidates'][$email] ?? null) : null;
}

function save_candidate(array $candidate): void
{
    $_SESSION['candidates'][$candidate['email']] = $candidate;
    $_SESSION['candidate_email'] = $candidate['email'];
}

function require_candidate(): array
{
    $candidate = current_user();
    if (!$candidate) {
        flash('Silakan login sebagai calon mahasiswa terlebih dahulu.', 'warning');
        redirect_to('/login');
    }

    return $candidate;
}

function status_label(string $status): string
{
    return [
        'submitted' => 'Menunggu Seleksi Berkas',
        'doc_review' => 'Berkas Diverifikasi',
        'exam_scheduled' => 'Ujian Masuk',
        'accepted' => 'Diterima',
        'rejected' => 'Tidak Lulus',
        'enrolled' => 'Mahasiswa Baru',
    ][$status] ?? 'Draft';
}

function active_step(string $status): int
{
    return [
        'draft' => 1,
        'submitted' => 2,
        'doc_review' => 3,
        'exam_scheduled' => 4,
        'accepted' => 5,
        'enrolled' => 6,
    ][$status] ?? 1;
}

function handle_post(array $programs): void
{
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $email = strtolower(trim((string) $_POST['email']));
        $candidate = [
            'name' => trim((string) $_POST['name']),
            'email' => $email,
            'phone' => trim((string) $_POST['phone']),
            'school' => trim((string) $_POST['school']),
            'program' => trim((string) $_POST['program']),
            'status' => 'submitted',
            'documents' => [
                'KTP/Kartu Pelajar' => 'pending',
                'Ijazah/SKL' => 'pending',
                'Pas Foto' => 'pending',
            ],
            'score' => null,
            'payment' => 'pending',
            'ospek' => 'not_started',
            'created_at' => date('d M Y H:i'),
        ];

        if ($candidate['name'] === '' || $email === '' || !isset($programs[$candidate['program']])) {
            flash('Lengkapi nama, email, dan program studi dengan benar.', 'danger');
            redirect_to('/register');
        }

        save_candidate($candidate);
        flash('Pendaftaran berhasil dikirim. Lanjutkan ke seleksi berkas.');
        redirect_to('/dashboard');
    }

    if ($action === 'login') {
        $email = strtolower(trim((string) $_POST['email']));
        if (isset($_SESSION['candidates'][$email])) {
            $_SESSION['candidate_email'] = $email;
            flash('Selamat datang kembali.');
            redirect_to('/dashboard');
        }

        flash('Akun tidak ditemukan. Silakan daftar terlebih dahulu.', 'danger');
        redirect_to('/login');
    }

    if ($action === 'admin-login') {
        if (($_POST['username'] ?? '') === 'admin' && ($_POST['password'] ?? '') === 'admin123') {
            $_SESSION['admin'] = true;
            flash('Login admin berhasil.');
            redirect_to('/admin');
        }

        flash('Username atau password admin salah.', 'danger');
        redirect_to('/admin-login');
    }

    if ($action === 'logout') {
        unset($_SESSION['candidate_email'], $_SESSION['admin']);
        flash('Anda sudah logout.');
        redirect_to('/');
    }

    $candidate = current_user();
    if (!$candidate) {
        redirect_to('/login');
    }

    if ($action === 'verify-documents') {
        foreach ($candidate['documents'] as $key => $value) {
            $candidate['documents'][$key] = 'verified';
        }
        $candidate['status'] = 'doc_review';
        save_candidate($candidate);
        flash('Berkas sudah diverifikasi. Jadwal ujian masuk tersedia.');
        redirect_to('/dashboard');
    }

    if ($action === 'take-exam') {
        $candidate['score'] = random_int(72, 96);
        $candidate['status'] = 'exam_scheduled';
        save_candidate($candidate);
        flash('Ujian selesai. Nilai sementara sudah tersimpan.');
        redirect_to('/dashboard');
    }

    if ($action === 'publish-result') {
        $candidate['status'] = ((int) $candidate['score'] >= 75) ? 'accepted' : 'rejected';
        save_candidate($candidate);
        flash($candidate['status'] === 'accepted' ? 'Selamat, kamu diterima!' : 'Hasil seleksi belum memenuhi nilai minimum.');
        redirect_to('/dashboard');
    }

    if ($action === 'pay') {
        $candidate['payment'] = 'completed';
        $candidate['status'] = 'enrolled';
        save_candidate($candidate);
        flash('Pembayaran daftar ulang berhasil dikonfirmasi.');
        redirect_to('/dashboard');
    }

    if ($action === 'check-ospek') {
        $candidate['ospek'] = 'present';
        save_candidate($candidate);
        flash('Presensi OSPEK berhasil. Selamat bergabung di kampus.');
        redirect_to('/dashboard');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_post($programs);
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$route = '/' . trim(substr($path, strlen($scriptDir . '/index.php')), '/');
$route = $route === '/' ? '/' : $route;
$flash = flash();
$candidate = current_user();
$isAdmin = (bool) ($_SESSION['admin'] ?? false);

function render_header(?array $candidate, bool $isAdmin, ?array $flash): void
{
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= APP_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= asset_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
      <i class="bi bi-mortarboard-fill me-2"></i><?= APP_NAME ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Alur PMB</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/register') ?>">Daftar</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/admin-login') ?>">Admin</a></li>
        <?php if ($candidate || $isAdmin): ?>
          <li class="nav-item">
            <form method="post" class="d-inline">
              <input type="hidden" name="action" value="logout">
              <button class="btn btn-sm btn-outline-light">Logout</button>
            </form>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-sm btn-brand" href="<?= base_url('/login') ?>">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<?php if ($flash): ?>
  <div class="container mt-3">
    <div class="alert alert-<?= e($flash['type']) ?> shadow-sm mb-0"><?= e($flash['message']) ?></div>
  </div>
<?php endif; ?>
<?php
}

function render_footer(): void
{
?>
<footer class="border-top py-4 mt-5">
  <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small text-muted">
    <span>Copyright 2026 <?= APP_NAME ?></span>
    <span>Latihan alur penerimaan mahasiswa baru</span>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
<?php
}

render_header($candidate, $isAdmin, $flash);

if ($route === '/') {
?>
<main class="hero-shell">
  <section class="container py-5">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <span class="eyebrow">Seleksi Penerimaan Mahasiswa Baru</span>
        <h1 class="display-5 fw-bold mt-3">Satu portal untuk pendaftaran sampai OSPEK.</h1>
        <p class="lead text-muted mt-3">Calon mahasiswa bisa mendaftar, cek berkas, mengikuti ujian, melihat hasil, daftar ulang, dan presensi OSPEK dalam satu dashboard.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <a class="btn btn-brand btn-lg" href="<?= base_url('/register') ?>"><i class="bi bi-pencil-square me-2"></i>Mulai Daftar</a>
          <a class="btn btn-outline-secondary btn-lg" href="<?= base_url('/login') ?>"><i class="bi bi-box-arrow-in-right me-2"></i>Login Peserta</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="process-board">
          <?php $number = 1; foreach ($GLOBALS['steps'] as $key => $step): ?>
            <div class="process-card">
              <div class="step-icon"><?= $number ?></div>
              <div>
                <h3><?= e($step[0]) ?></h3>
                <p><?= e($step[1]) ?></p>
              </div>
            </div>
          <?php $number++; endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <section class="container pb-5">
    <div class="row g-3">
      <div class="col-md-4"><div class="metric"><strong>6</strong><span>Tahap utama</span></div></div>
      <div class="col-md-4"><div class="metric"><strong>5</strong><span>Pilihan program studi</span></div></div>
      <div class="col-md-4"><div class="metric"><strong>24/7</strong><span>Simulasi portal online</span></div></div>
    </div>
  </section>
</main>
<?php
} elseif ($route === '/register') {
?>
<main class="container py-5">
  <div class="auth-card mx-auto">
    <h1 class="h3 fw-bold">Pendaftaran Online</h1>
    <p class="text-muted">Isi data awal sesuai alur pada gambar, lalu lanjut ke dashboard peserta.</p>
    <form method="post" class="row g-3">
      <input type="hidden" name="action" value="register">
      <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input class="form-control" name="name" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">No. HP</label>
        <input class="form-control" name="phone" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Asal Sekolah</label>
        <input class="form-control" name="school" required>
      </div>
      <div class="col-12">
        <label class="form-label">Program Studi</label>
        <select class="form-select" name="program" required>
          <option value="">Pilih program studi</option>
          <?php foreach ($programs as $program => $faculty): ?>
            <option value="<?= e($program) ?>"><?= e($program) ?> - <?= e($faculty) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 d-grid d-sm-flex gap-2">
        <button class="btn btn-brand"><i class="bi bi-send me-2"></i>Kirim Pendaftaran</button>
        <a class="btn btn-outline-secondary" href="<?= base_url('/login') ?>">Sudah punya akun</a>
      </div>
    </form>
  </div>
</main>
<?php
} elseif ($route === '/login') {
?>
<main class="container py-5">
  <div class="auth-card mx-auto">
    <h1 class="h3 fw-bold">Login Peserta</h1>
    <p class="text-muted">Masuk menggunakan email yang dipakai saat pendaftaran.</p>
    <form method="post" class="vstack gap-3">
      <input type="hidden" name="action" value="login">
      <div>
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>
      <button class="btn btn-brand align-self-start"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
      <p class="small text-muted mb-0">Belum punya akun? <a href="<?= base_url('/register') ?>">Daftar di sini</a>.</p>
    </form>
  </div>
</main>
<?php
} elseif ($route === '/dashboard') {
    $candidate = require_candidate();
    $stepNow = active_step($candidate['status']);
?>
<main class="container py-4">
  <div class="dashboard-head p-4 p-lg-5 mb-4">
    <div class="row align-items-center g-3">
      <div class="col-lg-8">
        <span class="eyebrow">Dashboard Peserta</span>
        <h1 class="h2 fw-bold mt-2 mb-1"><?= e($candidate['name']) ?></h1>
        <p class="mb-0 text-muted"><?= e($candidate['program']) ?> - <?= e($GLOBALS['programs'][$candidate['program']]) ?></p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <span class="status-pill"><?= e(status_label($candidate['status'])) ?></span>
      </div>
    </div>
  </div>

  <div class="stepper mb-4">
    <?php $i = 1; foreach ($steps as $key => $step): ?>
      <div class="step <?= $i <= $stepNow ? 'done' : '' ?>">
        <span><?= $i ?></span>
        <strong><?= e($step[0]) ?></strong>
      </div>
    <?php $i++; endforeach; ?>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel h-100">
        <h2 class="h5 fw-bold">Aktivitas Tahap Saat Ini</h2>
        <div class="list-group list-group-flush mt-3">
          <?php foreach ($candidate['documents'] as $doc => $status): ?>
            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
              <span><i class="bi bi-file-earmark-text me-2 text-brand"></i><?= e($doc) ?></span>
              <span class="badge text-bg-<?= $status === 'verified' ? 'success' : 'warning' ?>"><?= e($status) ?></span>
            </div>
          <?php endforeach; ?>
          <div class="list-group-item px-0 d-flex justify-content-between">
            <span><i class="bi bi-clipboard2-check me-2 text-brand"></i>Nilai ujian</span>
            <strong><?= $candidate['score'] ? e((string) $candidate['score']) : '-' ?></strong>
          </div>
          <div class="list-group-item px-0 d-flex justify-content-between">
            <span><i class="bi bi-credit-card me-2 text-brand"></i>Status pembayaran</span>
            <strong><?= e($candidate['payment']) ?></strong>
          </div>
          <div class="list-group-item px-0 d-flex justify-content-between">
            <span><i class="bi bi-people me-2 text-brand"></i>Presensi OSPEK</span>
            <strong><?= e($candidate['ospek']) ?></strong>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="panel h-100">
        <h2 class="h5 fw-bold">Aksi Simulasi</h2>
        <p class="text-muted small">Klik tombol secara berurutan untuk menjalankan alur seperti pada gambar.</p>
        <form method="post" class="d-grid gap-2">
          <button name="action" value="verify-documents" class="btn btn-outline-brand" <?= $candidate['status'] !== 'submitted' ? 'disabled' : '' ?>>
            <i class="bi bi-folder-check me-2"></i>Verifikasi Berkas
          </button>
          <button name="action" value="take-exam" class="btn btn-outline-brand" <?= $candidate['status'] !== 'doc_review' ? 'disabled' : '' ?>>
            <i class="bi bi-pencil me-2"></i>Ikuti Ujian Masuk
          </button>
          <button name="action" value="publish-result" class="btn btn-outline-brand" <?= $candidate['status'] !== 'exam_scheduled' ? 'disabled' : '' ?>>
            <i class="bi bi-megaphone me-2"></i>Lihat Pengumuman
          </button>
          <button name="action" value="pay" class="btn btn-outline-brand" <?= $candidate['status'] !== 'accepted' ? 'disabled' : '' ?>>
            <i class="bi bi-wallet2 me-2"></i>Daftar Ulang & Bayar
          </button>
          <button name="action" value="check-ospek" class="btn btn-brand" <?= $candidate['status'] !== 'enrolled' || $candidate['ospek'] === 'present' ? 'disabled' : '' ?>>
            <i class="bi bi-person-check me-2"></i>Presensi OSPEK
          </button>
        </form>
      </div>
    </div>
  </div>
</main>
<?php
} elseif ($route === '/admin-login') {
?>
<main class="container py-5">
  <div class="auth-card mx-auto">
    <h1 class="h3 fw-bold">Login Admin</h1>
    <p class="text-muted">Gunakan username <strong>admin</strong> dan password <strong>admin123</strong>.</p>
    <form method="post" class="vstack gap-3">
      <input type="hidden" name="action" value="admin-login">
      <div>
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required>
      </div>
      <div>
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>
      <button class="btn btn-brand align-self-start"><i class="bi bi-shield-lock me-2"></i>Login Admin</button>
    </form>
  </div>
</main>
<?php
} elseif ($route === '/admin') {
    if (!$isAdmin) {
        flash('Silakan login admin terlebih dahulu.', 'warning');
        redirect_to('/admin-login');
    }
    $candidates = $_SESSION['candidates'] ?? [];
?>
<main class="container py-4">
  <div class="dashboard-head p-4 mb-4">
    <span class="eyebrow">Panel Admin</span>
    <h1 class="h2 fw-bold mt-2">Monitoring Seleksi PMB</h1>
    <p class="text-muted mb-0">Pantau pendaftar dan status proses dari seleksi berkas sampai OSPEK.</p>
  </div>
  <div class="panel">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Program</th>
            <th>Status</th>
            <th>Nilai</th>
            <th>Pembayaran</th>
            <th>OSPEK</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$candidates): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pendaftar.</td></tr>
          <?php endif; ?>
          <?php foreach ($candidates as $item): ?>
            <tr>
              <td>
                <strong><?= e($item['name']) ?></strong><br>
                <span class="small text-muted"><?= e($item['email']) ?></span>
              </td>
              <td><?= e($item['program']) ?></td>
              <td><span class="badge rounded-pill text-bg-primary"><?= e(status_label($item['status'])) ?></span></td>
              <td><?= $item['score'] ? e((string) $item['score']) : '-' ?></td>
              <td><?= e($item['payment']) ?></td>
              <td><?= e($item['ospek']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php
} else {
    http_response_code(404);
?>
<main class="container py-5">
  <div class="auth-card mx-auto text-center">
    <h1 class="h3 fw-bold">Halaman tidak ditemukan</h1>
    <p class="text-muted">Rute yang dibuka belum tersedia.</p>
    <a class="btn btn-brand" href="<?= base_url('/') ?>">Kembali</a>
  </div>
</main>
<?php
}

render_footer();
