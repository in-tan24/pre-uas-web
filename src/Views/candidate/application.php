<?php
use App\Utils\Csrf;
/** @var array|null $application */
/** @var array $programs */

$canEdit = !$application || in_array((string)$application['status'], ['pending', 'revise'], true);
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-3">Application Form</h4>
    <?php if ($application): ?>
      <div class="mb-3">
        <span class="badge text-bg-info">Status: <?= htmlspecialchars((string)$application['status']) ?></span>
        <?php if (!empty($application['review_notes'])): ?>
          <div class="text-muted mt-2">Catatan reviewer: <?= nl2br(htmlspecialchars((string)$application['review_notes'])) ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/pre-uas/public/candidate/application">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <div class="mb-2">
        <label class="form-label">Program *</label>
        <select class="form-select" name="program_id" <?= $canEdit ? '' : 'disabled' ?> required>
          <option value="">-- pilih program --</option>
          <?php foreach ($programs as $p): ?>
            <?php $selected = $application && (int)$application['program_id'] === (int)$p['id']; ?>
            <option value="<?= (int)$p['id'] ?>" <?= $selected ? 'selected' : '' ?>>
              <?= htmlspecialchars((string)$p['faculty_name'] . ' - ' . (string)$p['program_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Application date *</label>
        <input class="form-control" type="date" name="application_date"
               value="<?= htmlspecialchars((string)($application['application_date'] ?? date('Y-m-d'))) ?>"
               <?= $canEdit ? '' : 'readonly' ?> required>
      </div>
      <div class="d-flex gap-2 mt-3">
        <?php if ($canEdit): ?>
          <button class="btn btn-primary">Save</button>
        <?php endif; ?>
        <a class="btn btn-outline-secondary" href="/pre-uas/public/candidate/documents">Upload documents</a>
      </div>
    </form>

    <?php if ($application && $canEdit): ?>
      <hr>
      <form method="post" action="/pre-uas/public/candidate/application/submit" onsubmit="return confirm('Submit aplikasi sekarang?');">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <button class="btn btn-success">Submit Application</button>
        <div class="text-muted small mt-1">Setelah submit, admin akan review berkas.</div>
      </form>
    <?php endif; ?>
  </div>
</div>

