<?php
require_once __DIR__ . '/../config/bootstrap.php';
if (current_user()) redirect('pages/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nama = trim($_POST['nama'] ?? ''); $email = strtolower(trim($_POST['email'] ?? '')); $nohp = trim($_POST['no_hp'] ?? ''); $password = $_POST['password'] ?? ''; $confirm = $_POST['password_confirm'] ?? '';
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $nohp === '' || strlen($password) < 8) $error = 'Lengkapi data dengan benar. Password minimal 8 karakter.';
    elseif ($password !== $confirm) $error = 'Konfirmasi password tidak sama.';
    else {
        try { $stmt = $pdo->prepare("INSERT INTO users (nama,email,no_hp,password,role) VALUES (?,?,?,?, 'pemilih')"); $stmt->execute([$nama,$email,$nohp,password_hash($password,PASSWORD_DEFAULT)]); flash('success','Registrasi berhasil. Silakan login.'); redirect('auth/login.php'); }
        catch (PDOException $ex) { $error = $ex->getCode() === '23000' ? 'Email sudah terdaftar.' : 'Registrasi gagal. Coba kembali.'; }
    }
}
$page_title = 'Registrasi Pemilih'; require __DIR__ . '/../includes/header.php';
?>
<section class="login-page"><div class="container"><div class="login-shell"><div class="row g-0"><div class="col-lg-5"><div class="login-art"><div><span class="eyebrow">Jadi bagian dari pemilihan</span><h1 class="mt-3">Daftar dan<br><span style="color:var(--gold)">Berikan Suaramu.</span></h1><p>Buat akun pemilih untuk mengakses proses voting dan melihat riwayat dukunganmu.</p></div><div class="small">Data akun disimpan dengan aman di database lokal.</div></div></div><div class="col-lg-7"><div class="login-form-wrap"><div class="w-100"><span class="eyebrow">Akun pemilih baru</span><h2 class="mt-2 mb-3">Registrasi</h2><?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?><div class="row g-3"><div class="col-md-6"><label class="form-label">Nama lengkap</label><input class="form-control" name="nama" required value="<?= e($_POST['nama'] ?? '') ?>"></div><div class="col-md-6"><label class="form-label">Nomor HP</label><input class="form-control" name="no_hp" required value="<?= e($_POST['no_hp'] ?? '') ?>"></div><div class="col-12"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></div><div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" minlength="8" required></div><div class="col-md-6"><label class="form-label">Ulangi password</label><input class="form-control" type="password" name="password_confirm" minlength="8" required></div></div><button class="btn btn-red btn-lg w-100 rounded-pill mt-4">Buat Akun</button></form><p class="small-muted mt-4 mb-0">Sudah punya akun? <a href="<?= url('auth/login.php') ?>" class="fw-bold">Masuk di sini</a></p></div></div></div></div></div></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
