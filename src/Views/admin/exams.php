<?php
/** @var array $exams */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Entrance Exams</h3>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/applications">Applications</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>App</th><th>Candidate</th><th>Program</th><th>Date</th><th>Type</th><th>Status</th><th>Score</th></tr></thead>
        <tbody>
        <?php foreach ($exams as $e): ?>
          <tr>
            <td><a href="/pre-uas/public/admin/applications/view?id=<?= (int)$e['application_id'] ?>">#<?= (int)$e['application_id'] ?></a></td>
            <td><?= htmlspecialchars((string)$e['candidate_email']) ?></td>
            <td><?= htmlspecialchars((string)$e['program_name']) ?></td>
            <td><?= htmlspecialchars((string)$e['exam_date']) ?></td>
            <td><?= htmlspecialchars((string)$e['exam_type']) ?></td>
            <td><span class="badge text-bg-secondary"><?= htmlspecialchars((string)$e['status']) ?></span></td>
            <td><?= htmlspecialchars((string)($e['score'] ?? '-')) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($exams === []): ?>
          <tr><td colspan="7" class="text-muted">Belum ada ujian.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

