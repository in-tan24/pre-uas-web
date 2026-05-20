<?php
/** @var array $applications */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Applications</h3>
  <div class="d-flex gap-2">
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/applications">All</a>
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/applications?status=submitted">Submitted</a>
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/applications?status=reviewed">Reviewed</a>
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/applications?status=revise">Revise</a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>ID</th><th>Candidate</th><th>Program</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($applications as $a): ?>
          <tr>
            <td><?= (int)$a['id'] ?></td>
            <td><?= htmlspecialchars((string)$a['email']) ?></td>
            <td><?= htmlspecialchars((string)$a['program_name']) ?></td>
            <td><span class="badge text-bg-info"><?= htmlspecialchars((string)$a['status']) ?></span></td>
            <td><?= htmlspecialchars((string)($a['submission_date'] ?? '-')) ?></td>
            <td><a class="btn btn-sm btn-primary" href="/pre-uas/public/admin/applications/view?id=<?= (int)$a['id'] ?>">Open</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($applications === []): ?>
          <tr><td colspan="6" class="text-muted">Tidak ada data.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

