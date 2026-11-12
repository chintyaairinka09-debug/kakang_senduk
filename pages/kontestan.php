<?php
require_once __DIR__ . '/../config/bootstrap.php';
$page_title = 'Daftar Kontestan';
$category = $_GET['kategori'] ?? 'Semua'; $search = trim($_GET['q'] ?? '');
$sql = 'SELECT * FROM kontestan WHERE 1=1'; $params = [];
if (in_array($category, ['Kakang','Senduk'], true)) { $sql .= ' AND kategori = ?'; $params[] = $category; }
if ($search !== '') { $sql .= ' AND (nama LIKE ? OR nomor_peserta LIKE ? OR asal_instansi LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
$sql .= " ORDER BY FIELD(kategori,'Kakang','Senduk'), nomor_peserta";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $candidates = $stmt->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<header class="page-head"><div class="container"><span class="eyebrow">Finalis pemilihan</span><h1 class="mt-2">Daftar Kontestan</h1><p>Kenali profil dan gagasan para calon Kakang dan Senduk Ponorogo.</p></div></header>
<section class="section-pad"><div class="container"><form class="panel-card mb-4" method="get"><div class="row g-2 align-items-end"><div class="col-md-5"><label class="form-label">Cari kontestan</label><input name="q" class="form-control" placeholder="Nama, nomor, atau instansi" value="<?= e($search) ?>"></div><div class="col-md-4"><label class="form-label">Kategori</label><select class="form-select" name="kategori"><option value="Semua">Semua kategori</option><option <?= $category==='Kakang'?'selected':'' ?>>Kakang</option><option <?= $category==='Senduk'?'selected':'' ?>>Senduk</option></select></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-green flex-fill">Terapkan</button><a class="btn btn-outline-secondary" href="<?= url('pages/kontestan.php') ?>">Reset</a></div></div></form>
<div class="row g-4">
  <?php if (!$candidates): ?>
    <div class="col-12"><div class="empty-state"><strong>Kontestan belum ditemukan.</strong>Coba kata kunci atau kategori lain.</div></div>
  <?php endif; ?>
  <?php foreach ($candidates as $c): ?>
    <div class="col-sm-6 col-lg-4">
      <div class="candidate-card">
        <img class="candidate-photo" src="<?= $c['foto'] ? url('uploads/' . rawurlencode($c['foto'])) : url('assets/img/placeholder.svg') ?>" alt="Foto <?= e($c['nama']) ?>">
        <div class="candidate-body">
          <span class="candidate-number"><?= e($c['nomor_peserta']) ?></span>
          <span class="category-pill <?= $c['kategori'] === 'Kakang' ? 'category-kakang' : 'category-senduk' ?>"><?= e($c['kategori']) ?></span>
          <h3><?= e($c['nama']) ?></h3>
          <p><?= e($c['asal_instansi'] ?: 'Asal instansi belum diisi') ?></p>
          <a class="btn btn-outline-dark btn-sm rounded-pill" href="<?= url('pages/detail-kontestan.php?id=' . $c['id']) ?>">Lihat Detail</a>
          <a class="btn btn-red btn-sm rounded-pill" href="<?= url(current_user() ? 'pages/voting.php' : 'auth/login.php') ?>">Vote</a>
          <?php if (!empty($c['instagram_url'])): ?>
            <a class="btn btn-outline-secondary btn-sm rounded-pill mt-2" href="<?= e($c['instagram_url']) ?>" target="_blank" rel="noopener noreferrer">Lihat foto di Instagram</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
</div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
