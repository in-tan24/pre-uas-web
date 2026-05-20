<?php
/** @var array $candidate */
/** @var array|null $application */
/** @var array|null $enrollment */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-0">Dashboard</h3>
    <div class="text-muted">Status: <span class="badge text-bg-secondary step-badge"><?= htmlspecialchars((string)$candidate['status']) ?></span></div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary" href="/pre-uas/public/candidate/application">Application</a>
    <a class="btn btn-outline-primary" href="/pre-uas/public/candidate/documents">Documents</a>
    <a class="btn btn-outline-primary" href="/pre-uas/public/candidate/exam">Exam</a>
    <a class="btn btn-outline-primary" href="/pre-uas/public/candidate/results">Results</a>
  </div>
</div>

<?php
$status = (string)$candidate['status'];
$steps = [
  ['key' => 'draft', 'title' => 'Online Registration', 'desc' => 'Buat akun, isi aplikasi, upload dokumen.'],
  ['key' => 'submitted', 'title' => 'Submitted', 'desc' => 'Aplikasi sudah dikirim, menunggu review berkas.'],
  ['key' => 'doc_review', 'title' => 'Document Review', 'desc' => 'Admin memverifikasi berkas (pass/revise/reject).'],
  ['key' => 'exam_scheduled', 'title' => 'Entrance Exam', 'desc' => 'Ikuti ujian sesuai jadwal.'],
  ['key' => 'accepted', 'title' => 'Accepted', 'desc' => 'Diterima, lanjut daftar ulang (enrollment).'],
  ['key' => 'rejected', 'title' => 'Rejected', 'desc' => 'Tidak diterima.'],
  ['key' => 'enrolled', 'title' => 'Fully Enrolled', 'desc' => 'Daftar ulang selesai + pembayaran diproses + OSPEK.'],
];

$badgeClass = static function (string $key, string $current): string {
  if ($current === $key) return 'primary';
  if ($current === 'rejected' && $key === 'rejected') return 'danger';
  if ($current === 'accepted' && $key === 'accepted') return 'success';
  if ($current === 'enrolled' && $key === 'enrolled') return 'success';
  return 'secondary';
};
?>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title mb-2">Status yang Mungkin</h5>
    <div class="text-muted mb-3">Sistem akan mengarahkan proses sampai hasil akhir: <b>Accepted</b> atau <b>Rejected</b>.</div>
    <div class="list-group">
      <?php foreach ($steps as $s): ?>
        <div class="list-group-item d-flex justify-content-between align-items-start">
          <div class="me-3">
            <div class="fw-semibold"><?= htmlspecialchars($s['title']) ?></div>
            <div class="text-muted small"><?= htmlspecialchars($s['desc']) ?></div>
          </div>
          <span class="badge text-bg-<?= htmlspecialchars($badgeClass($s['key'], $status)) ?>"><?= htmlspecialchars($s['key']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Online Registration</h5>
        <?php if ($application): ?>
          <div class="text-muted">Program: <?= htmlspecialchars((string)$application['program_name']) ?></div>
          <div class="text-muted">Status aplikasi: <span class="badge text-bg-info step-badge"><?= htmlspecialchars((string)$application['status']) ?></span></div>
          <a class="btn btn-sm btn-primary mt-2" href="/pre-uas/public/candidate/application">Lihat / edit</a>
        <?php else: ?>
          <p class="text-muted mb-2">Belum membuat aplikasi.</p>
          <a class="btn btn-sm btn-primary" href="/pre-uas/public/candidate/application">Buat aplikasi</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Reenrollment & Payment</h5>
        <?php if ($enrollment): ?>
          <div class="text-muted">Student ID: <b><?= htmlspecialchars((string)$enrollment['student_id']) ?></b></div>
          <a class="btn btn-sm btn-primary mt-2" href="/pre-uas/public/candidate/payment">Kelola pembayaran</a>
          <a class="btn btn-sm btn-outline-primary mt-2" href="/pre-uas/public/candidate/ospek">Lihat OSPEK</a>
        <?php else: ?>
          <p class="text-muted mb-2">Buat enrollment setelah status Accepted.</p>
          <a class="btn btn-sm btn-outline-primary" href="/pre-uas/public/candidate/enrollment">Halaman enrollment</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
