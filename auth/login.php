<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (current_user()) {
    redirect(current_user()['role'] === 'admin' ? 'admin/dashboard.php' : 'pages/index.php');
}

$error = '';
$success = flash('success');
$notice = flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Masukkan email dan password dengan benar.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nama, email, no_hp, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            unset($user['password']);
            $_SESSION['user'] = $user;
            flash('success', 'Selamat datang, ' . $user['nama'] . '!');
            redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'pages/index.php');
        }

        $error = 'Email atau password tidak sesuai. Periksa kembali data akunmu.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101414">
    <title>Masuk Akun | Kakang Senduk Ponorogo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
    <style>
        body.login-body{min-height:100vh;background:#101414;overflow-x:hidden}
        .login-screen{min-height:100vh;display:grid;grid-template-columns:minmax(0,1.12fr) minmax(380px,.88fr);position:relative;background:linear-gradient(115deg,rgba(7,13,12,.84),rgba(7,13,12,.68)),var(--hero-bg),linear-gradient(135deg,#123d32,#087f83);background-size:cover;background-position:center}
        .login-screen:before{content:"";position:absolute;inset:0;pointer-events:none;background:radial-gradient(circle at 16% 18%,rgba(255,212,59,.16),transparent 26%),linear-gradient(135deg,rgba(181,31,43,.18),transparent 48%,rgba(8,127,131,.22))}
        .login-visual{position:relative;z-index:1;padding:clamp(28px,6vw,84px);display:flex;flex-direction:column;justify-content:space-between;color:#fff;min-height:100vh}
        .login-brand{display:inline-flex;align-items:center;gap:13px;color:#fff;text-decoration:none;font-weight:950;letter-spacing:.13em;font-size:.9rem}
        .login-brand-mark{display:grid;place-items:center;width:48px;height:48px;background:var(--red);border:2px solid rgba(255,255,255,.23);border-radius:15px;color:#fff;font-size:1rem;box-shadow:0 8px 24px rgba(0,0,0,.2)}
        .login-brand small{display:block;color:var(--gold);font-size:.62rem;letter-spacing:.28em;margin-top:3px}
        .login-copy{max-width:680px;padding:48px 0}
        .login-copy .eyebrow{color:var(--gold)}
        .login-copy h1{font-size:clamp(3rem,6.6vw,6.4rem);font-weight:1000;letter-spacing:-.07em;line-height:.93;margin:22px 0}
        .login-copy h1 span{color:var(--gold)}
        .login-copy p{color:#e3e9e5;font-size:1.08rem;line-height:1.8;max-width:540px}
        .login-tags{display:flex;gap:10px;flex-wrap:wrap;margin-top:28px}
        .login-tag{border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.08);color:#f7f7ef;border-radius:50px;padding:8px 13px;font-size:.78rem;font-weight:700;backdrop-filter:blur(8px)}
        .login-visual-foot{color:#d7dfda;font-size:.8rem}
        .login-side{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;padding:32px;background:rgba(10,18,16,.3);backdrop-filter:blur(5px)}
        .login-card{width:min(100%,470px);background:#fff;border-radius:28px;padding:clamp(26px,4vw,48px);box-shadow:0 28px 90px rgba(0,0,0,.32);border:1px solid rgba(255,255,255,.55)}
        .login-card-kicker{color:var(--red);font-size:.72rem;font-weight:900;letter-spacing:.18em;text-transform:uppercase}
        .login-card h2{font-size:2.15rem;letter-spacing:-.05em;font-weight:950;color:#101414;margin:8px 0}
        .login-subtitle{color:#69736d;font-size:.92rem;margin-bottom:28px}
        .login-card .form-label{font-size:.84rem;font-weight:800;color:#27312c;margin-bottom:8px}
        .login-card .form-control{min-height:51px;border-radius:13px;padding:12px 14px;border:1px solid #dfe5df}
        .login-card .form-control:focus{border-color:var(--turq);box-shadow:0 0 0 .22rem rgba(8,127,131,.12)}
        .password-wrap{position:relative}
        .password-wrap .form-control{padding-right:78px}
        .password-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:#68736c;font-size:.76rem;font-weight:800;padding:8px}
        .login-submit{min-height:52px;border-radius:14px;background:var(--red);border:0;color:#fff;font-weight:900;letter-spacing:.02em;box-shadow:0 10px 22px rgba(181,31,43,.18)}
        .login-submit:hover{background:#941824;color:#fff;transform:translateY(-1px)}
        .login-bottom{font-size:.87rem;color:#68736c}
        .login-bottom a{color:var(--red);font-weight:900;text-decoration:none}
        .demo-account{background:#f5f8f3;border:1px solid #e3eadf;border-radius:15px;padding:14px 16px;margin-top:24px}
        .demo-account-title{font-size:.72rem;color:#52625a;font-weight:900;letter-spacing:.1em;text-transform:uppercase;margin-bottom:9px}
        .demo-row{display:flex;justify-content:space-between;gap:10px;font-size:.78rem;padding:4px 0;color:#4e5953}
        .demo-row strong{color:#17211c}
        .login-home{position:absolute;left:clamp(28px,6vw,84px);bottom:28px;color:#fff;text-decoration:none;font-size:.82rem;font-weight:750;z-index:3}
        .login-home:hover{color:var(--gold)}
        @media(max-width:991px){.login-screen{grid-template-columns:1fr}.login-visual{min-height:auto;padding:30px 25px 10px}.login-copy{padding:45px 0 28px}.login-copy h1{font-size:clamp(2.8rem,12vw,4.7rem)}.login-visual-foot{display:none}.login-side{padding:20px 16px 70px;background:transparent}.login-home{left:25px;bottom:22px}}
        @media(max-width:480px){.login-brand-mark{width:42px;height:42px}.login-card{border-radius:22px;padding:25px 21px}.login-card h2{font-size:1.85rem}.demo-row{font-size:.72rem}}
    </style>
</head>
<body class="login-body">
<div class="login-screen">
    <section class="login-visual" aria-label="Informasi pemilihan">
        <a class="login-brand" href="<?= url('pages/index.php') ?>">
            <span class="login-brand-mark">KS</span>
            <span>KAKANG SENDUK<small>PONOROGO · JAWA TIMUR</small></span>
        </a>
        <div class="login-copy">
            <span class="eyebrow">Pesona budaya · Bangga Ponorogo</span>
            <h1>SATU SUARA,<br><span>SATU KEBANGGAAN.</span></h1>
            <p>Masuk ke ruang pemilihan digital. Kenali para kontestan, dukung pilihanmu, dan ikut merayakan budaya serta potensi wisata Ponorogo.</p>
            <div class="login-tags"><span class="login-tag">✦ Budaya Ponorogo</span><span class="login-tag">✓ Voting transparan</span><span class="login-tag">◆ Kakang &amp; Senduk</span></div>
        </div>
        <div class="login-visual-foot">Sistem Informasi Pemilihan Duta Wisata Daerah · Proyek Pembelajaran</div>
    </section>

    <section class="login-side" aria-label="Form login">
        <div class="login-card">
            <div class="login-card-kicker">Selamat datang kembali</div>
            <h2>Masuk Akun</h2>
            <p class="login-subtitle">Masukkan email dan password untuk melanjutkan.</p>

            <?php if ($success): ?><div class="alert alert-success py-2 small"><?= e($success) ?></div><?php endif; ?>
            <?php if ($notice): ?><div class="alert alert-info py-2 small"><?= e($notice) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger py-2 small" role="alert"><?= e($error) ?></div><?php endif; ?>

            <form method="post" action="<?= url('auth/login.php') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat email</label>
                    <input id="email" class="form-control" type="email" name="email" placeholder="nama@email.com" autocomplete="username" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrap">
                        <input id="password" class="form-control" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" id="togglePassword" aria-label="Tampilkan password">Lihat</button>
                    </div>
                </div>
                <button type="submit" class="btn login-submit w-100 mt-4">MASUK SEKARANG <span aria-hidden="true">→</span></button>
            </form>

            <div class="text-center login-bottom mt-4">Belum punya akun? <a href="<?= url('auth/register.php') ?>">Daftar sebagai pemilih</a></div>

            <div class="demo-account">
                <div class="demo-account-title">Akun demo untuk mencoba</div>
                <div class="demo-row"><strong>Administrator</strong><span>admin@ponorogo.local</span></div>
                <div class="demo-row"><span>Password</span><strong>admin123</strong></div>
                <hr class="my-2" style="border-color:#dce5db">
                <div class="demo-row"><strong>Pemilih</strong><span>pemilih@ponorogo.local</span></div>
                <div class="demo-row"><span>Password</span><strong>pemilih123</strong></div>
            </div>
        </div>
    </section>
    <a class="login-home" href="<?= url('pages/index.php') ?>">← Kembali ke beranda</a>
</div>
<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
togglePassword.addEventListener('click', () => {
    const show = passwordInput.type === 'password';
    passwordInput.type = show ? 'text' : 'password';
    togglePassword.textContent = show ? 'Sembunyi' : 'Lihat';
    togglePassword.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
});
</script>
</body>
</html>
