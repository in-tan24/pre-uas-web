<?php
use App\Utils\Csrf;
?>
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Candidate Register</h4>
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
              <input class="form-control" name="email" type="email" required>
            </div>
            <div class="col-12">
              <label class="form-label">Password *</label>
              <input class="form-control" name="password" type="password" minlength="6" required>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="/pre-uas/public/candidate/login">Sudah punya akun? Login</a>
            <button class="btn btn-primary">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

