<?php
use App\Utils\Csrf;
/** @var array $app */
/** @var array $documents */
/** @var array|null $exam */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-0">Application #<?= (int)$app['id'] ?></h3>
    <div class="text-muted"><?= htmlspecialchars((string)$app['candidate_email']) ?> · <?= htmlspecialchars((string)$app['faculty_name']) ?> - <?= htmlspecialchars((string)$app['program_name']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="/pre-uas/public/admin/applications">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-2">Review (Document Review)</h5>
        <div class="mb-2">Status aplikasi: <span class="badge text-bg-info"><?= htmlspecialchars((string)$app['status']) ?></span></div>
        <form method="post" action="/pre-uas/public/admin/applications/review">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
          <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
          <div class="mb-2">
            <label class="form-label">Set status</label>
            <select class="form-select" name="status">
              <option value="reviewed">reviewed (pass)</option>
              <option value="revise">revise</option>
              <option value="rejected">rejected</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Notes</label>
            <textarea class="form-control" rows="3" name="review_notes"><?= htmlspecialchars((string)($app['review_notes'] ?? '')) ?></textarea>
          </div>
          <button class="btn btn-primary">Save review</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-2">Documents</h5>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead><tr><th>Type</th><th>Status</th><th>File</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($documents as $d): ?>
              <?php $ext = strtolower((string)pathinfo((string)$d['file_path'], PATHINFO_EXTENSION)); ?>
              <?php $isImage = in_array($ext, ['jpg', 'jpeg', 'png'], true); ?>
              <tr>
                <td><?= htmlspecialchars((string)$d['document_type']) ?></td>
                <td><span class="badge text-bg-secondary"><?= htmlspecialchars((string)$d['status']) ?></span></td>
                <td>
                  <?php if ($isImage): ?>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-secondary js-doc-preview"
                      data-bs-toggle="modal"
                      data-bs-target="#docPreviewModal"
                      data-doc-type="<?= htmlspecialchars((string)$d['document_type']) ?>"
                      data-doc-path="/pre-uas/public<?= htmlspecialchars((string)$d['file_path']) ?>"
                    >Preview</button>
                  <?php else: ?>
                    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public<?= htmlspecialchars((string)$d['file_path']) ?>" download>Download</a>
                  <?php endif; ?>
                </td>
                <td>
                  <form method="post" action="/pre-uas/public/admin/documents/verify" class="d-flex gap-2">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
                    <input type="hidden" name="document_id" value="<?= (int)$d['id'] ?>">
                    <select class="form-select form-select-sm" name="status">
                      <option value="verified">verified</option>
                      <option value="revise">revise</option>
                      <option value="rejected">rejected</option>
                    </select>
                    <input class="form-control form-control-sm" name="notes" placeholder="notes (optional)">
                    <button class="btn btn-sm btn-outline-primary">Go</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if ($documents === []): ?>
              <tr><td colspan="4" class="text-muted">Belum ada dokumen diupload kandidat.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Document Preview Modal -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="docPreviewTitle">Document Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img id="docPreviewImage" alt="preview" class="img-fluid d-none" style="max-height:75vh;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
(() => {
  const modalEl = document.getElementById('docPreviewModal');
  if (!modalEl) return;
  const titleEl = document.getElementById('docPreviewTitle');
  const imgEl = document.getElementById('docPreviewImage');

  modalEl.addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    if (!btn) return;
    const docType = btn.getAttribute('data-doc-type') || 'Document';
    const docPath = btn.getAttribute('data-doc-path') || '';

    titleEl.textContent = `Preview: ${docType}`;

    imgEl.classList.add('d-none');
    imgEl.src = '';

    imgEl.classList.remove('d-none');
    imgEl.src = docPath;
  });

  modalEl.addEventListener('hidden.bs.modal', () => {
    imgEl.src = '';
  });
})();
</script>

<div class="card mt-3">
  <div class="card-body">
    <h5 class="mb-2">Entrance Exam</h5>
    <?php if ($exam): ?>
      <div class="text-muted mb-2">Current: <?= htmlspecialchars((string)$exam['exam_date']) ?> · <?= htmlspecialchars((string)$exam['exam_type']) ?> · <?= htmlspecialchars((string)$exam['status']) ?> · score <?= htmlspecialchars((string)($exam['score'] ?? '-')) ?></div>
    <?php else: ?>
      <div class="text-muted mb-2">Belum ada jadwal ujian.</div>
    <?php endif; ?>
    <div class="row g-3">
      <div class="col-lg-6">
        <form method="post" action="/pre-uas/public/admin/exams/schedule">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
          <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Exam date *</label>
              <input class="form-control" type="datetime-local" name="exam_date" required>
            </div>
            <div class="col-6">
              <label class="form-label">Type</label>
              <select class="form-select" name="exam_type">
                <option value="Written">Written</option>
                <option value="Online">Online</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Location</label>
              <input class="form-control" name="exam_location" placeholder="Ruang / link meeting">
            </div>
          </div>
          <button class="btn btn-outline-primary mt-2">Save schedule</button>
        </form>
      </div>
      <div class="col-lg-6">
        <?php if ($exam): ?>
          <form method="post" action="/pre-uas/public/admin/exams/score">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
            <input type="hidden" name="exam_id" value="<?= (int)$exam['id'] ?>">
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label">Score (0-100)</label>
                <input class="form-control" name="score" type="number" min="0" max="100" step="0.01" value="<?= htmlspecialchars((string)($exam['score'] ?? '')) ?>" required>
              </div>
              <div class="col-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                  <option value="completed">completed</option>
                  <option value="passed">passed</option>
                  <option value="failed">failed</option>
                  <option value="absent">absent</option>
                </select>
              </div>
            </div>
            <button class="btn btn-outline-success mt-2">Save score</button>
          </form>
        <?php else: ?>
          <div class="alert alert-info mb-0">Jadwalkan ujian dulu.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
