<?php
require_once __DIR__ . '/../config/bootstrap.php';
$id = (int)($_GET['id'] ?? 0); $stmt = $pdo->prepare('SELECT * FROM kontestan WHERE id = ?'); $stmt->execute([$id]); $c = $stmt->fetch();
if (!$c) { http_response_code(404); $page_title='Kontestan tidak ditemukan'; require __DIR__.'/../includes/header.php'; echo '<div class="container section-pad"><div class="empty-state"><strong>Kontestan tidak ditemukan.</strong><a href="'.url('pages/kontestan.php').'">Kembali ke daftar kontestan</a></div></div>'; require __DIR__.'/../includes/footer.php'; exit; }
$page_title = $c['nama']; require __DIR__ . '/../includes/header.php';
?>
<header class="page-head"><div class="container"><span class="eyebrow">Profil kontestan · <?= e($c['kategori']) ?></span><h1 class="mt-2 mb-0"><?= e($c['nama']) ?></h1></div></header>
<section class="section-pad">
  <div class="container">
    <div class="row g-4 align-items-start">
      <div class="col-lg-5">
        <img class="detail-photo" src="<?= $c['foto'] ? url('uploads/' . rawurlencode($c['foto'])) : url('assets/img/placeholder.svg') ?>" alt="<?= e($c['nama']) ?>">
        <?php if (!empty($c['instagram_url'])): ?>
          <a class="btn btn-outline-secondary rounded-pill mt-3" href="<?= e($c['instagram_url']) ?>" target="_blank" rel="noopener noreferrer">Lihat foto di Instagram</a>
        <?php endif; ?>
      </div>
      <div class="col-lg-7">
        <span class="candidate-number"><?= e($c['nomor_peserta']) ?></span>
        <span class="category-pill <?= $c['kategori'] === 'Kakang' ? 'category-kakang' : 'category-senduk' ?> ms-2"><?= e($c['kategori']) ?></span>
        <h2 class="fw-bold mt-3"><?= e($c['nama']) ?></h2>
        <p class="text-muted-custom"><?= e($c['asal_instansi']) ?></p>
        <div class="panel-card mb-3">
          <h5 class="fw-bold">Profil Singkat</h5>
          <p><?= nl2br(e($c['deskripsi'] ?: 'Belum ada deskripsi.')) ?></p>
          <?php if ($c['tempat_lahir'] || $c['tanggal_lahir']): ?>
            <div class="small-muted">Tempat/tanggal lahir: <?= e($c['tempat_lahir']) ?> <?= $c['tanggal_lahir'] ? date('d M Y', strtotime($c['tanggal_lahir'])) : '' ?></div>
          <?php endif; ?>
        </div>
        <div class="panel-card mb-3"><h5 class="fw-bold">Visi</h5><p class="mb-0"><?= nl2br(e($c['visi'] ?: 'Belum diisi.')) ?></p></div>
        <div class="panel-card mb-3"><h5 class="fw-bold">Misi</h5><p class="mb-0"><?= nl2br(e($c['misi'] ?: 'Belum diisi.')) ?></p></div>
        <div class="panel-card mb-4"><h5 class="fw-bold">Prestasi</h5><p class="mb-0"><?= nl2br(e($c['prestasi'] ?: 'Belum diisi.')) ?></p></div>
        <a class="btn btn-red rounded-pill px-4" href="<?= url(current_user() ? 'pages/voting.php' : 'auth/login.php') ?>">Dukung Kontestan</a>
        <a class="btn btn-outline-secondary rounded-pill px-4" href="<?= url('pages/kontestan.php') ?>">Kembali</a>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
