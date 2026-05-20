<?php
/** @var array|null $application */
/** @var array|null $exam */
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-3">Entrance Exam</h4>
    <?php if (!$application): ?>
      <div class="alert alert-warning mb-0">Belum ada aplikasi.</div>
    <?php elseif (!$exam): ?>
      <div class="alert alert-info mb-0">Jadwal ujian belum dibuat admin.</div>
    <?php else: ?>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between"><span>Tanggal</span><b><?= htmlspecialchars((string)$exam['exam_date']) ?></b></li>
        <li class="list-group-item d-flex justify-content-between"><span>Lokasi</span><b><?= htmlspecialchars((string)($exam['exam_location'] ?? '-')) ?></b></li>
        <li class="list-group-item d-flex justify-content-between"><span>Tipe</span><b><?= htmlspecialchars((string)$exam['exam_type']) ?></b></li>
        <li class="list-group-item d-flex justify-content-between"><span>Status</span><b><?= htmlspecialchars((string)$exam['status']) ?></b></li>
        <li class="list-group-item d-flex justify-content-between"><span>Score</span><b><?= htmlspecialchars((string)($exam['score'] ?? '-')) ?></b></li>
      </ul>
    <?php endif; ?>
  </div>
</div>

