<?php
require_once __DIR__ . '/../config/bootstrap.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('pages/voting.php');
verify_csrf();
if (current_user()['role'] !== 'pemilih') redirect('admin/dashboard.php');
$period = active_period($pdo); if (!$period) { flash('error','Periode voting tidak aktif.'); redirect('pages/voting.php'); }
$userId=(int)current_user()['id']; $candidateId=(int)($_POST['kontestan_id']??0);
if (!paid_for_period($pdo,$userId,(int)$period['id'])) { flash('error','Selesaikan pembayaran simulasi terlebih dahulu.'); redirect('pages/pembayaran.php'); }
$stmt=$pdo->prepare('SELECT id,kategori,nama FROM kontestan WHERE id=?'); $stmt->execute([$candidateId]); $candidate=$stmt->fetch();
if(!$candidate){ flash('error','Kontestan tidak ditemukan.'); redirect('pages/voting.php'); }
try {
    $pdo->beginTransaction();
    // Kunci baris periode untuk menjaga validasi voting tetap konsisten pada proses bersamaan.
    $lock=$pdo->prepare('SELECT id FROM periode_voting WHERE id=? AND status=\'Aktif\' AND NOW() BETWEEN tanggal_mulai AND tanggal_selesai FOR UPDATE');
    $lock->execute([(int)$period['id']]);
    if(!$lock->fetch()) { $pdo->rollBack(); flash('error','Periode voting sudah ditutup.'); redirect('pages/voting.php'); }
    $ins=$pdo->prepare('INSERT INTO voting (user_id,kontestan_id,periode_id,kategori) VALUES (?,?,?,?)');
    $ins->execute([$userId,$candidateId,(int)$period['id'],$candidate['kategori']]);
    $pdo->commit(); flash('success','Suara untuk '.$candidate['nama'].' berhasil disimpan. Kartu bukti voting kamu sudah tersedia.'); redirect('pages/bukti-voting.php');
} catch(PDOException $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    if($e->getCode()==='23000') flash('error','Kamu sudah memilih pada kategori ini. Suara ganda ditolak.'); else flash('error','Voting gagal disimpan.');
    redirect('pages/voting.php');
} catch(Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); flash('error','Terjadi kesalahan saat menyimpan suara.'); redirect('pages/voting.php'); }
