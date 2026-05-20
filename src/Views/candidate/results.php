<?php
/** @var array $candidate */
/** @var array|null $application */
/** @var array|null $exam */
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-2">Results Announcement</h4>
    <div class="text-muted mb-3">Status kandidat: <span class="badge text-bg-secondary"><?= htmlspecialchars((string)$candidate['status']) ?></span></div>

    <?php if (!$application): ?>
      <div class="alert alert-warning mb-0">Belum ada aplikasi.</div>
    <?php else: ?>
      <div class="mb-2">Status aplikasi: <span class="badge text-bg-info"><?= htmlspecialchars((string)$application['status']) ?></span></div>
      <?php if ($exam): ?>
        <div class="mb-3">Exam status: <span class="badge text-bg-secondary"><?= htmlspecialchars((string)$exam['status']) ?></span>, score: <b><?= htmlspecialchars((string)($exam['score'] ?? '-')) ?></b></div>
      <?php endif; ?>

      <?php if ((string)$candidate['status'] === 'accepted'): ?>
        <div class="alert alert-success">Selamat! Anda <b>Accepted</b>. Silakan daftar ulang (enrollment).</div>
        <a class="btn btn-success" href="/pre-uas/public/candidate/enrollment">Daftar Ulang</a>
      <?php elseif ((string)$candidate['status'] === 'rejected'): ?>
        <div class="alert alert-danger mb-0">Maaf, Anda <b>Rejected</b>.</div>
      <?php else: ?>
        <div class="alert alert-info mb-0">Hasil akhir belum dipublish admin.</div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

