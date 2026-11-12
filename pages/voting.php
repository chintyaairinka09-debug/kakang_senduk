<?php
require_once __DIR__ . '/../config/bootstrap.php'; require_login();
if (current_user()['role'] !== 'pemilih') redirect('admin/dashboard.php');
$period = active_period($pdo);
if (!$period) { $page_title='Voting ditutup'; require __DIR__.'/../includes/header.php'; echo '<div class="container section-pad"><div class="empty-state"><strong>Periode voting belum aktif.</strong>Voting hanya tersedia pada jadwal yang telah ditentukan.</div></div>'; require __DIR__.'/../includes/footer.php'; exit; }
if (!paid_for_period($pdo,(int)current_user()['id'],(int)$period['id'])) redirect('pages/pembayaran.php');
$userId=(int)current_user()['id'];
$alreadyK=voted_category($pdo,$userId,(int)$period['id'],'Kakang'); $alreadyS=voted_category($pdo,$userId,(int)$period['id'],'Senduk');
$kCandidates=$pdo->query("SELECT * FROM kontestan WHERE kategori='Kakang' ORDER BY nomor_peserta")->fetchAll();
$sCandidates=$pdo->query("SELECT * FROM kontestan WHERE kategori='Senduk' ORDER BY nomor_peserta")->fetchAll();
$page_title='Voting'; require __DIR__ . '/../includes/header.php';
?>
<header class="page-head"><div class="container"><span class="eyebrow">Suara kamu menentukan</span><h1 class="mt-2">Halaman Voting</h1><p><?= e($period['nama_periode']) ?> · Satu pilihan untuk setiap kategori.</p></div></header>
<section class="section-pad"><div class="container"><div class="gateway-box mb-4"><strong class="text-success">Pembayaran simulasi tercatat.</strong><span class="small-muted"> Biaya Rp3.000 telah disimpan sebagai transaksi demo. Tidak ada uang sungguhan yang dipotong.</span></div>
<?php foreach ([['Kakang',$kCandidates,$alreadyK],['Senduk',$sCandidates,$alreadyS]] as [$cat,$items,$done]): ?>
<div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3"><div><span class="eyebrow"><?= e($cat) ?></span><h2 class="fw-bold mb-0">Pilih <?= e($cat) ?></h2></div><?php if($done): ?><span class="status-pill status-on">SUDAH MEMILIH</span><?php else: ?><span class="small-muted">Pilih satu kontestan</span><?php endif; ?></div>
<div class="row g-3 mb-5"><?php foreach($items as $c): ?><div class="col-md-6 col-xl-4"><div class="vote-option <?= $done?'opacity-75':'' ?>"><div class="d-flex gap-3 align-items-center"><img src="<?= $c['foto']?url('uploads/'.rawurlencode($c['foto'])):url('assets/img/placeholder.svg') ?>" alt="<?= e($c['nama']) ?>"><div><span class="candidate-number"><?= e($c['nomor_peserta']) ?></span><h5 class="fw-bold mt-2 mb-1"><?= e($c['nama']) ?></h5><span class="small-muted"><?= e($c['asal_instansi']) ?></span></div></div><div class="mt-3 d-flex justify-content-between align-items-center"><a class="small fw-bold" href="<?= url('pages/detail-kontestan.php?id='.$c['id']) ?>">Lihat profil</a><?php if(!$done): ?><form method="post" action="<?= url('proses/simpan-voting.php') ?>" data-confirm="Kamu yakin memilih <?= e($c['nama']) ?>? Suara tidak dapat diubah."><?= csrf_field() ?><input type="hidden" name="kontestan_id" value="<?= (int)$c['id'] ?>"><button class="btn btn-red btn-sm rounded-pill px-3">Pilih <?= e($cat) ?></button></form><?php else: ?><span class="small-muted">Pilihan terkunci</span><?php endif; ?></div></div></div><?php endforeach; ?></div>
<?php endforeach; ?>
<div class="panel-card"><div class="d-flex flex-wrap align-items-center justify-content-between gap-3"><div><h5 class="fw-bold">Status pilihanmu</h5><div class="d-flex flex-wrap gap-3"><span class="status-pill <?= $alreadyK?'status-on':'status-off' ?>">Kakang: <?= $alreadyK?'Sudah memilih':'Belum memilih' ?></span><span class="status-pill <?= $alreadyS?'status-on':'status-off' ?>">Senduk: <?= $alreadyS?'Sudah memilih':'Belum memilih' ?></span></div></div><?php if($alreadyK || $alreadyS): ?><a href="<?= url('pages/bukti-voting.php') ?>" class="btn btn-green rounded-pill px-4">Lihat Bukti Voting</a><?php endif; ?></div></div>
</div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
