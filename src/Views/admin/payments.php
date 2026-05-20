<?php
use App\Utils\Csrf;
/** @var array $payments */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Payments</h3>
  <div class="d-flex gap-2">
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/payments">All</a>
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/payments?status=pending">Pending</a>
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/payments?status=verified">Verified</a>
    <a class="btn btn-sm btn-outline-secondary" href="/pre-uas/public/admin/payments?status=completed">Completed</a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>ID</th><th>Candidate</th><th>Student ID</th><th>Amount</th><th>Method</th><th>Status</th><th>Receipt</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= htmlspecialchars((string)$p['candidate_email']) ?></td>
            <td><?= htmlspecialchars((string)$p['student_id']) ?></td>
            <td><?= htmlspecialchars((string)$p['amount']) ?></td>
            <td><?= htmlspecialchars((string)$p['payment_method']) ?></td>
            <td><span class="badge text-bg-secondary"><?= htmlspecialchars((string)$p['status']) ?></span></td>
            <td>
              <?php if (!empty($p['receipt_file'])): ?>
                <a target="_blank" href="/pre-uas/public<?= htmlspecialchars((string)$p['receipt_file']) ?>">open</a>
              <?php else: ?>-<?php endif; ?>
            </td>
            <td>
              <form method="post" action="/pre-uas/public/admin/payments/verify" class="d-flex gap-2">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                <input type="hidden" name="payment_id" value="<?= (int)$p['id'] ?>">
                <select class="form-select form-select-sm" name="status">
                  <option value="verified">verified</option>
                  <option value="completed">completed</option>
                  <option value="rejected">rejected</option>
                </select>
                <button class="btn btn-sm btn-outline-primary">Go</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if ($payments === []): ?>
          <tr><td colspan="8" class="text-muted">Tidak ada pembayaran.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

