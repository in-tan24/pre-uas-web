<?php
use App\Utils\Csrf;
/** @var array $schedules */
/** @var array $programs */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">OSPEK Schedule</h3>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/dashboard">Back</a>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="mb-2">Create schedule</h5>
    <form method="post" action="/pre-uas/public/admin/ospek/create" class="row g-2">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <div class="col-md-3">
        <label class="form-label">Program</label>
        <select class="form-select" name="program_id">
          <option value="ALL">ALL (semua program)</option>
          <?php foreach (($programs ?? []) as $p): ?>
            <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars((string)$p['faculty_name'] . ' - ' . (string)$p['program_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Title *</label>
        <input class="form-control" name="title" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Start *</label>
        <input class="form-control" type="datetime-local" name="start_at" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">End *</label>
        <input class="form-control" type="datetime-local" name="end_at" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Location</label>
        <input class="form-control" name="location">
      </div>
      <div class="col-md-9 d-flex align-items-end">
        <button class="btn btn-primary w-100">Create</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="mb-2">Schedules</h5>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead><tr><th>Title</th><th>Program</th><th>Start</th><th>End</th><th>Location</th></tr></thead>
        <tbody>
        <?php foreach ($schedules as $s): ?>
          <tr>
            <td><?= htmlspecialchars((string)$s['title']) ?></td>
            <td><?= htmlspecialchars((string)($s['program_id'] ?? 'ALL')) ?></td>
            <td><?= htmlspecialchars((string)$s['start_at']) ?></td>
            <td><?= htmlspecialchars((string)$s['end_at']) ?></td>
            <td><?= htmlspecialchars((string)($s['location'] ?? '-')) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($schedules === []): ?>
          <tr><td colspan="5" class="text-muted">Belum ada jadwal.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
