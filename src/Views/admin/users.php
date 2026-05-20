<?php
use App\Utils\Csrf;
/** @var array $users */
/** @var array $roles */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">User Management (Superadmin)</h3>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/dashboard">Back</a>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="mb-2">Create User</h5>
    <form method="post" action="/pre-uas/public/admin/users/create" class="row g-2">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <div class="col-md-3">
        <label class="form-label">Role *</label>
        <select class="form-select" name="role_id" required>
          <option value="">-- pilih --</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars((string)$r['role_name']) ?> (<?= htmlspecialchars((string)$r['role_key']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Full name *</label>
        <input class="form-control" name="full_name" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Username *</label>
        <input class="form-control" name="username" autocomplete="username" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Password *</label>
        <input class="form-control" name="password" type="password" minlength="6" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Email (optional)</label>
        <input class="form-control" name="email" type="email">
      </div>
      <div class="col-md-2">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
          <label class="form-check-label">Active</label>
        </div>
      </div>
      <div class="col-md-10 d-flex align-items-end">
        <button class="btn btn-dark w-100">Create</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="mb-2">Users</h5>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>ID</th><th>Username</th><th>Full name</th><th>Role</th><th>Active</th><th>Email</th><th>Created</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars((string)$u['username']) ?></td>
            <td><?= htmlspecialchars((string)$u['full_name']) ?></td>
            <td><?= htmlspecialchars((string)$u['role_name']) ?></td>
            <td><span class="badge text-bg-<?= ((int)$u['is_active'] === 1) ? 'success' : 'secondary' ?>"><?= ((int)$u['is_active'] === 1) ? 'yes' : 'no' ?></span></td>
            <td><?= htmlspecialchars((string)($u['email'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string)$u['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($users === []): ?>
          <tr><td colspan="7" class="text-muted">Belum ada data.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

