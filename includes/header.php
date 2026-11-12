<?php $me = current_user(); $title = $page_title ?? APP_NAME; ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark site-nav">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= url('pages/index.php') ?>"><span class="brand-mark">KS</span> KAKANG SENDUK <small>PONOROGO</small></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="<?= url('pages/kontestan.php') ?>">Kontestan</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('pages/klasemen.php') ?>">Klasemen</a></li>
        <?php if ($me && $me['role'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('admin/dashboard.php') ?>">Dashboard Admin</a></li>
        <?php elseif ($me): ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('pages/voting.php') ?>">Voting Saya</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('pages/bukti-voting.php') ?>">Bukti Voting</a></li>
        <?php endif; ?>
        <?php if ($me): ?>
          <li class="nav-item"><span class="nav-user">Hai, <?= e($me['nama']) ?></span></li>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light rounded-pill px-3" href="<?= url('auth/logout.php') ?>">Keluar</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('auth/login.php') ?>">Masuk</a></li>
          <li class="nav-item"><a class="btn btn-gold btn-sm rounded-pill px-3" href="<?= url('auth/register.php') ?>">Daftar Pemilih</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="site-main">
<?php foreach (['success'=>'success','error'=>'danger','info'=>'info'] as $key=>$type): if ($msg = flash($key)): ?>
  <div class="container pt-3"><div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert"><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
<?php endif; endforeach; ?>
