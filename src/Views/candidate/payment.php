<?php
use App\Utils\Csrf;
/** @var array|null $enrollment */
/** @var array $payments */
?>
<div class="card">
  <div class="card-body">
    <h4 class="mb-3">Payment</h4>
    <?php if (!$enrollment): ?>
      <div class="alert alert-warning mb-0">Enrollment belum dibuat.</div>
    <?php else: ?>
      <div class="mb-3 text-muted">Student ID: <b><?= htmlspecialchars((string)$enrollment['student_id']) ?></b></div>
      <form method="post" action="/pre-uas/public/candidate/payment/upload" enctype="multipart/form-data" class="row g-2">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <div class="col-md-3">
          <label class="form-label">Amount *</label>
          <input class="form-control" name="amount" type="number" min="1" step="0.01" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Method *</label>
          <select class="form-select" name="payment_method">
            <option value="transfer">transfer</option>
            <option value="va">va</option>
            <option value="ewallet">ewallet</option>
            <option value="cash">cash</option>
            <option value="card">card</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Receipt file *</label>
          <input class="form-control" type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100">Upload</button>
        </div>
      </form>
      <hr>
      <h6>Payment history</h6>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Status</th><th>Receipt</th></tr></thead>
          <tbody>
          <?php foreach ($payments as $p): ?>
            <tr>
              <td><?= htmlspecialchars((string)$p['payment_date']) ?></td>
              <td><?= htmlspecialchars((string)$p['amount']) ?></td>
              <td><?= htmlspecialchars((string)$p['payment_method']) ?></td>
              <td><span class="badge text-bg-secondary"><?= htmlspecialchars((string)$p['status']) ?></span></td>
              <td>
                <?php if (!empty($p['receipt_file'])): ?>
                  <a target="_blank" href="/pre-uas/public<?= htmlspecialchars((string)$p['receipt_file']) ?>">open</a>
                <?php else: ?>-<?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if ($payments === []): ?>
            <tr><td colspan="5" class="text-muted">Belum ada pembayaran.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

