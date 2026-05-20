<?php
/** @var array|null $enrollment */
/** @var array $schedules */
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-3">OSPEK</h4>
    <?php if (!$enrollment): ?>
      <div class="alert alert-info mb-0">OSPEK bisa dilihat setelah enrollment dibuat.</div>
    <?php else: ?>
      <div class="text-muted mb-3">Program ID: <?= (int)$enrollment['program_id'] ?>, Student ID: <b><?= htmlspecialchars((string)$enrollment['student_id']) ?></b></div>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead><tr><th>Title</th><th>Start</th><th>End</th><th>Location</th></tr></thead>
          <tbody>
          <?php foreach ($schedules as $s): ?>
            <tr>
              <td><?= htmlspecialchars((string)$s['title']) ?></td>
              <td><?= htmlspecialchars((string)$s['start_at']) ?></td>
              <td><?= htmlspecialchars((string)$s['end_at']) ?></td>
              <td><?= htmlspecialchars((string)($s['location'] ?? '-')) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($schedules === []): ?>
            <tr><td colspan="4" class="text-muted">Belum ada jadwal OSPEK.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

