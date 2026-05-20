<?php
use App\Utils\Csrf;
/** @var array $faculties */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Faculties</h3>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/dashboard">Back</a>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="mb-2">Tambah Fakultas</h5>
    <form method="post" action="/pre-uas/public/admin/faculties/create" class="row g-2">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <div class="col-md-4">
        <label class="form-label">Nama Fakultas *</label>
        <input class="form-control" name="faculty_name" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Deskripsi</label>
        <input class="form-control" name="description">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Tambah</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="mb-2">Daftar Fakultas</h5>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead><tr><th>ID</th><th>Nama</th><th>Deskripsi</th></tr></thead>
        <tbody>
        <?php foreach ($faculties as $f): ?>
          <tr>
            <td><?= (int)$f['id'] ?></td>
            <td><?= htmlspecialchars((string)$f['faculty_name']) ?></td>
            <td><?= htmlspecialchars((string)($f['description'] ?? '-')) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($faculties === []): ?>
          <tr><td colspan="3" class="text-muted">Belum ada data.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

