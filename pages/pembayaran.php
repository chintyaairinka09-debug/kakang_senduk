<?php
require_once __DIR__ . '/../config/bootstrap.php'; require_login();
if (current_user()['role'] !== 'pemilih') redirect('admin/dashboard.php');
$period = active_period($pdo);
if (!$period) { flash('error','Saat ini tidak ada periode voting yang aktif.'); redirect('pages/index.php'); }
$userId = (int)current_user()['id'];
$stmt = $pdo->prepare('SELECT * FROM payments WHERE user_id = ? AND periode_id = ? LIMIT 1'); $stmt->execute([$userId,$period['id']]); $payment = $stmt->fetch();
if (!$payment) {
    $ref = 'MOCK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    $stmt = $pdo->prepare("INSERT INTO payments (user_id,periode_id,amount,provider,reference,status) VALUES (?,?,?,'MOCK_GATEWAY',?,'pending')");
    $stmt->execute([$userId,$period['id'],VOTING_FEE,$ref]);
    $stmt = $pdo->prepare('SELECT * FROM payments WHERE user_id = ? AND periode_id = ?'); $stmt->execute([$userId,$period['id']]); $payment = $stmt->fetch();
}
if ($payment['status'] === 'paid') { flash('success','Pembayaran simulasi sudah tercatat. Kamu bisa melanjutkan voting.'); redirect('pages/voting.php'); }
$page_title = 'Pembayaran Voting'; require __DIR__ . '/../includes/header.php';
?>
<header class="page-head"><div class="container"><span class="eyebrow">Tahap sebelum voting</span><h1 class="mt-2">Konfirmasi Pembayaran</h1><p>Pembayaran simulasi untuk membuka akses voting pada periode aktif.</p></div></header>
<section class="section-pad"><div class="container"><div class="row justify-content-center"><div class="col-lg-8"><div class="panel-card"><div class="d-flex justify-content-between align-items-start flex-wrap gap-3"><div><span class="gateway-mark">◈ MOCK GATEWAY</span><h2 class="fw-bold mt-3 mb-1">Biaya partisipasi voting</h2><p class="text-muted-custom mb-0"><?= e($period['nama_periode']) ?></p></div><div class="text-end"><div class="small-muted">Total simulasi</div><div class="display-6 fw-bold"><?= rupiah($payment['amount']) ?></div></div></div><hr class="my-4"><div class="gateway-box"><div class="fw-bold text-success mb-1">MODE DEMO / TIDAK ADA TRANSAKSI UANG SUNGGUHAN</div><p class="mb-2">Tombol di bawah hanya mengubah status transaksi menjadi <strong>paid</strong> dan menyimpan waktu pembayaran ke database. Tidak terhubung ke bank, e-wallet, QRIS, atau payment gateway sungguhan.</p><div class="small-muted">Nomor referensi: <strong><?= e($payment['reference']) ?></strong></div><div class="small-muted">Penyedia: <?= e($payment['provider']) ?> · Status: <span class="text-warning fw-bold">PENDING</span></div></div><form method="post" action="<?= url('proses/bayar-simulasi.php') ?>" class="mt-4" data-confirm="Konfirmasi pembayaran simulasi Rp3.000? Tidak ada uang sungguhan yang dipotong."><?= csrf_field() ?><input type="hidden" name="payment_id" value="<?= (int)$payment['id'] ?>"><button class="btn btn-red btn-lg rounded-pill px-4">Konfirmasi Bayar <?= rupiah($payment['amount']) ?> (Simulasi)</button> <a class="btn btn-outline-secondary btn-lg rounded-pill px-4" href="<?= url('pages/index.php') ?>">Nanti dulu</a></form></div></div></div></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
