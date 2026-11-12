<?php
require_once __DIR__ . '/../config/bootstrap.php';
$page_title = 'Beranda';
$period = active_period($pdo);
$counts = $pdo->query("SELECT kategori, COUNT(*) total FROM kontestan GROUP BY kategori")->fetchAll(PDO::FETCH_KEY_PAIR);
$featured = $pdo->query('SELECT * FROM kontestan ORDER BY kategori, nomor_peserta LIMIT 3')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="hero">
  <div class="container"><div class="hero-content">
    <span class="eyebrow">Pesona budaya · Bangga Ponorogo</span>
    <h1>SUARAKAN PILIHANMU.<br><span>DUKUNG BUDAYA KITA.</span></h1>
    <p>Kenali para finalis Kakang dan Senduk Ponorogo, lihat gagasan mereka, lalu berikan dukunganmu melalui sistem voting digital yang mudah dan transparan.</p>
    <div class="hero-actions"><a class="btn btn-gold btn-lg rounded-pill px-4" href="<?= url('pages/kontestan.php') ?>">Lihat Kontestan</a><a class="btn btn-outline-light btn-lg rounded-pill px-4" href="<?= url(current_user() ? 'pages/voting.php' : 'auth/login.php') ?>">Voting Sekarang</a><a class="btn btn-outline-warning btn-lg rounded-pill px-4" href="<?= url('pages/klasemen.php') ?>">Lihat Klasemen</a></div>
    <div class="hero-meta"><div><strong><?= (int)($counts['Kakang'] ?? 0) ?></strong>Kontestan Kakang</div><div><strong><?= (int)($counts['Senduk'] ?? 0) ?></strong>Kontestan Senduk</div><div><strong><?= $period ? 'AKTIF' : 'TUTUP' ?></strong>Status voting</div></div>
  </div></div>
</section>
<section class="section-pad"><div class="container">
  <div class="section-heading"><span class="kicker">Kenapa ikut memilih?</span><h2>Dukunganmu berarti untuk Ponorogo.</h2><p>Pelajari profil peserta, dukung kandidat pilihan, dan pantau perolehan suara melalui satu aplikasi.</p></div>
  <div class="row g-4"><div class="col-md-4"><div class="feature-card"><div class="feature-icon">✦</div><h3>Kenali Kontestan</h3><p>Baca profil, visi, misi, dan prestasi setiap calon Kakang maupun Senduk.</p></div></div><div class="col-md-4"><div class="feature-card"><div class="feature-icon">✓</div><h3>Voting Terarah</h3><p>Login, selesaikan pembayaran simulasi Rp3.000, lalu pilih satu kontestan di setiap kategori.</p></div></div><div class="col-md-4"><div class="feature-card"><div class="feature-icon">▤</div><h3>Klasemen Terbuka</h3><p>Lihat peringkat dan jumlah suara kontestan yang diperbarui dari database.</p></div></div></div>
</div></section>
<section class="section-pad pt-0"><div class="container"><div class="d-flex justify-content-between align-items-end flex-wrap gap-3 section-heading"><div><span class="kicker">Pilihan peserta</span><h2 class="mb-0">Kenali para kontestan</h2></div><a class="btn btn-green rounded-pill px-4" href="<?= url('pages/kontestan.php') ?>">Semua Kontestan</a></div>
<div class="row g-4"><?php foreach ($featured as $c): ?><div class="col-md-4"><div class="candidate-card"><img class="candidate-photo" src="<?= $c['foto'] ? url('uploads/' . rawurlencode($c['foto'])) : url('assets/img/placeholder.svg') ?>" alt="Foto <?= e($c['nama']) ?>"><div class="candidate-body"><span class="candidate-number"><?= e($c['nomor_peserta']) ?></span> <span class="category-pill <?= $c['kategori']==='Kakang'?'category-kakang':'category-senduk' ?>"><?= e($c['kategori']) ?></span><h3><?= e($c['nama']) ?></h3><p><?= e($c['asal_instansi']) ?></p><a class="btn btn-outline-dark btn-sm rounded-pill" href="<?= url('pages/detail-kontestan.php?id=' . $c['id']) ?>">Lihat Profil</a></div></div></div><?php endforeach; ?></div>
</div></section>
<section class="pb-5"><div class="container"><div class="admin-cover"><span class="eyebrow">Informasi periode</span><h2 class="fw-black mt-2"><?= e($period['nama_periode'] ?? 'Periode voting belum tersedia') ?></h2><p class="mb-3"><?php if ($period): ?>Voting dibuka <?= date('d M Y H:i', strtotime($period['tanggal_mulai'])) ?> sampai <?= date('d M Y H:i', strtotime($period['tanggal_selesai'])) ?> WIB.<?php else: ?>Silakan cek kembali setelah administrator mengaktifkan periode voting.<?php endif; ?></p><a class="btn btn-gold rounded-pill px-4" href="<?= url('pages/klasemen.php') ?>">Pantau Klasemen</a></div></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
