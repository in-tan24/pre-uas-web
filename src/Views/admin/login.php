<?php
use App\Utils\Csrf;
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Admin Login</h4>
        <p class="text-muted">Gunakan akun `users` (role admin/finance/superadmin).</p>
        <form method="post" action="/pre-uas/public/admin/login">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
          <div class="mb-2">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" autocomplete="username" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" required>
          </div>
          <button class="btn btn-dark mt-3">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>
