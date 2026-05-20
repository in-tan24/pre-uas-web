<?php
use App\Utils\Csrf;
/** @var array $candidate */
/** @var array|null $application */
/** @var array|null $enrollment */
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-3">Reenrollment</h4>
    <?php if ($enrollment): ?>
      <div class="alert alert-success">Enrollment sudah aktif. Student ID: <b><?= htmlspecialchars((string)$enrollment['student_id']) ?></b></div>
      <a class="btn btn-primary" href="/pre-uas/public/candidate/payment">Lanjut pembayaran</a>
    <?php else: ?>
      <p class="text-muted">Enrollment hanya tersedia jika status kandidat <b>accepted</b>.</p>
      <form method="post" action="/pre-uas/public/candidate/enrollment">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <button class="btn btn-success" <?= ((string)$candidate['status'] === 'accepted') ? '' : 'disabled' ?>>
          Create Enrollment
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

