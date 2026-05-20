<?php
use App\Utils\Csrf;
/** @var array $applications */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Results Announcement</h3>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/applications">Applications</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>ID</th><th>Candidate</th><th>Program</th><th>Status</th><th>Decision</th></tr></thead>
        <tbody>
        <?php foreach ($applications as $a): ?>
          <tr>
            <td><a href="/pre-uas/public/admin/applications/view?id=<?= (int)$a['id'] ?>">#<?= (int)$a['id'] ?></a></td>
            <td><?= htmlspecialchars((string)$a['email']) ?></td>
            <td><?= htmlspecialchars((string)$a['program_name']) ?></td>
            <td><span class="badge text-bg-info"><?= htmlspecialchars((string)$a['status']) ?></span></td>
            <td>
              <form method="post" action="/pre-uas/public/admin/results/publish" class="d-flex gap-2">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                <input type="hidden" name="application_id" value="<?= (int)$a['id'] ?>">
                <select class="form-select form-select-sm" name="decision">
                  <option value="accepted">accepted</option>
                  <option value="rejected">rejected</option>
                </select>
                <button class="btn btn-sm btn-outline-success">Publish</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if ($applications === []): ?>
          <tr><td colspan="5" class="text-muted">Tidak ada data.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

