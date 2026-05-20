<?php
use App\Utils\Csrf;
?>
<div class="row justify-content-center g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Login Calon Mahasiswa</h4>
        <form method="post" action="/pre-uas/public/candidate/login">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input class="form-control" name="email" type="email" autocomplete="email" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" autocomplete="current-password" required>
          </div>
          <button class="btn btn-primary mt-2">Login</button>
          <div class="text-muted small mt-2">Link admin tidak ditampilkan di halaman awal. Akses admin via `/admin/login`.</div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Register</h4>
        <form method="post" action="/pre-uas/public/candidate/register">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">First name *</label>
              <input class="form-control" name="first_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last name</label>
              <input class="form-control" name="last_name">
            </div>
            <div class="col-12">
              <label class="form-label">Email *</label>
              <input class="form-control" name="email" type="email" autocomplete="email" required>
            </div>
            <div class="col-12">
              <label class="form-label">Password *</label>
              <input class="form-control" name="password" type="password" minlength="6" autocomplete="new-password" required>
            </div>
          </div>
          <button class="btn btn-outline-primary mt-3">Create account</button>
        </form>
      </div>
    </div>
  </div>
</div>
