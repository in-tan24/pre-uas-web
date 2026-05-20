<?php
use App\Utils\Csrf;
/** @var array|null $application */
/** @var array $documents */
/** @var array $requiredTypes */
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-3">Document Upload</h4>
    <?php if (!$application): ?>
      <div class="alert alert-warning">
        Buat aplikasi dulu di halaman Application.
      </div>
      <a class="btn btn-primary" href="/pre-uas/public/candidate/application">Ke Application</a>
    <?php else: ?>
      <form method="post" action="/pre-uas/public/candidate/documents/upload" enctype="multipart/form-data" class="row g-2">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <div class="col-md-4">
          <label class="form-label">Document type *</label>
          <select class="form-select" name="document_type" required>
            <option value="">-- pilih dokumen --</option>
            <?php foreach (($requiredTypes ?? []) as $t): ?>
              <option value="<?= htmlspecialchars((string)$t) ?>"><?= htmlspecialchars((string)$t) ?></option>
            <?php endforeach; ?>
            <option value="Lainnya">Lainnya</option>
          </select>
          <div class="text-muted small mt-1">Upload tipe yang sama lagi = replace file sebelumnya.</div>
        </div>
        <div class="col-md-5">
          <label class="form-label">File *</label>
          <input class="form-control" type="file" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
          <div class="text-muted small mt-1">Format: PDF/DOC/DOCX/JPG/PNG (maks 10MB). JPG/PNG akan bisa dipreview di modal.</div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-primary w-100">Upload</button>
        </div>
      </form>
      <hr>
      <h6>Uploaded documents</h6>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead><tr><th>Type</th><th>Status</th><th>File</th><th>Verified by</th></tr></thead>
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
              <td><?= htmlspecialchars((string)($d['verified_by_name'] ?? '-')) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($documents === []): ?>
            <tr><td colspan="4" class="text-muted">Belum ada dokumen.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
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
    <?php endif; ?>
  </div>
</div>
