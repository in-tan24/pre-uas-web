<?php
/** @var array $user */
?>
<div class="p-4 bg-white border rounded mb-3">
  <h3 class="mb-1">Admin Dashboard</h3>
  <div class="text-muted">Role: <b><?= htmlspecialchars((string)$user['role_name']) ?></b></div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted">Applications</div>
        <div class="display-6"><?= (int)$appsCount ?></div>
        <a class="btn btn-sm btn-primary" href="/pre-uas/public/admin/applications">Manage</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted">Pending payments</div>
        <div class="display-6"><?= (int)$pendingPaymentsCount ?></div>
        <a class="btn btn-sm btn-dark" href="/pre-uas/public/admin/payments?status=pending">Verify</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted">Shortcuts</div>
        <div class="d-flex flex-wrap gap-2 mt-2">
          <a class="btn btn-sm btn-outline-primary" href="/pre-uas/public/admin/exams">Exams</a>
          <a class="btn btn-sm btn-outline-primary" href="/pre-uas/public/admin/results">Results</a>
          <a class="btn btn-sm btn-outline-primary" href="/pre-uas/public/admin/ospek">OSPEK</a>
          <a class="btn btn-sm btn-outline-primary" href="/pre-uas/public/admin/programs">Programs</a>
          <?php if (($user['role_key'] ?? '') === 'superadmin'): ?>
            <a class="btn btn-sm btn-outline-primary" href="/pre-uas/public/admin/faculties">Faculties</a>
            <a class="btn btn-sm btn-outline-dark" href="/pre-uas/public/admin/users">Users</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
