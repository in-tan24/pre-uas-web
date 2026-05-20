<?php
declare(strict_types=1);

use App\Utils\Auth;
use App\Utils\Csrf;
use App\Utils\Flash;

$flash = Flash::get();
$candidate = Auth::candidate();
$user = Auth::user();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Seleksi Penerimaan Mahasiswa baru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/pre-uas/public/css/main.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container container-narrow">
    <a class="navbar-brand" href="/pre-uas/public/">Seleksi Penerimaan Mahasiswa baru</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <!-- intentionally minimal: no top dashboard menu -->
      </ul>
      <div class="d-flex gap-2">
        <?php if ($candidate): ?>
          <span class="navbar-text text-white small"><?= htmlspecialchars($candidate['email']) ?></span>
          <form method="post" action="/pre-uas/public/candidate/logout" class="m-0">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <button class="btn btn-sm btn-light">Logout</button>
          </form>
        <?php elseif ($user): ?>
          <span class="navbar-text text-white small"><?= htmlspecialchars((string)($user['username'] ?? ($user['email'] ?? 'user'))) ?></span>
          <form method="post" action="/pre-uas/public/admin/logout" class="m-0">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <button class="btn btn-sm btn-light">Logout</button>
          </form>
        <?php else: ?>
          <a class="btn btn-sm btn-light" href="/pre-uas/public/">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<main class="container container-narrow py-4">
  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
  <?php endif; ?>
