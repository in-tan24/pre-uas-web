<?php
use App\Utils\Csrf;
/** @var array $programs */
/** @var array $faculties */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Programs</h3>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/dashboard">Back</a>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="mb-2">Tambah Program</h5>
    <form method="post" action="/pre-uas/public/admin/programs/create" class="row g-2">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <div class="col-md-4">
        <label class="form-label">Fakultas *</label>
        <select class="form-select" name="faculty_id" required>
          <option value="">-- pilih --</option>
          <?php foreach ($faculties as $f): ?>
            <option value="<?= (int)$f['id'] ?>"><?= htmlspecialchars((string)$f['faculty_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Nama Program *</label>
        <input class="form-control" name="program_name" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Kapasitas *</label>
        <input class="form-control" name="capacity" type="number" min="0" value="0" required>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Tambah</button>
      </div>
      <div class="col-12">
        <label class="form-label">Requirements</label>
        <input class="form-control" name="requirements" placeholder="Ijazah SMA/SMK, dll">
      </div>
      <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <input class="form-control" name="description">
      </div>
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
          <label class="form-check-label">Aktif</label>
        </div>
      </div>
    </form>
    <?php if ($faculties === []): ?>
      <div class="alert alert-warning mt-3 mb-0">Belum ada fakultas. Tambahkan dulu di menu Faculties.</div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="mb-2">Daftar Program</h5>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>ID</th><th>Fakultas</th><th>Program</th><th>Capacity</th><th>Active</th></tr></thead>
        <tbody>
        <?php foreach ($programs as $p): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= htmlspecialchars((string)$p['faculty_name']) ?></td>
            <td><?= htmlspecialchars((string)$p['program_name']) ?></td>
            <td><?= (int)$p['capacity'] ?></td>
            <td><span class="badge text-bg-<?= ((int)$p['is_active'] === 1) ? 'success' : 'secondary' ?>"><?= ((int)$p['is_active'] === 1) ? 'yes' : 'no' ?></span></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($programs === []): ?>
          <tr><td colspan="5" class="text-muted">Belum ada data.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

