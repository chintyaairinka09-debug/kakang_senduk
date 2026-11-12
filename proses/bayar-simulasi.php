<?php
require_once __DIR__ . '/../config/bootstrap.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('pages/pembayaran.php');
verify_csrf();
$paymentId = (int)($_POST['payment_id'] ?? 0); $period = active_period($pdo);
if (!$period) { flash('error','Periode voting sudah tidak aktif.'); redirect('pages/index.php'); }
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND user_id = ? AND periode_id = ? AND status = 'pending' FOR UPDATE");
    $stmt->execute([$paymentId,(int)current_user()['id'],(int)$period['id']]); $payment = $stmt->fetch();
    if (!$payment) { $pdo->rollBack(); flash('error','Transaksi tidak ditemukan atau sudah diproses.'); redirect('pages/pembayaran.php'); }
    $upd = $pdo->prepare("UPDATE payments SET status='paid', paid_at=NOW() WHERE id=?"); $upd->execute([$paymentId]);
    $pdo->commit(); flash('success','Pembayaran simulasi Rp3.000 berhasil dicatat. Silakan pilih Kakang dan Senduk.'); redirect('pages/voting.php');
} catch (Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); flash('error','Gagal menyimpan simulasi pembayaran.'); redirect('pages/pembayaran.php'); }
