<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $instagramUrl = trim($_POST['instagram_url'] ?? '');
        if ($instagramUrl !== '' && !preg_match('~^https://www\.instagram\.com/p/[A-Za-z0-9_-]+/?(?:\?.*)?$~i', $instagramUrl)) {
            throw new RuntimeException('Tautan Instagram harus berupa tautan postingan yang valid.');
        }

        $foto = upload_photo('foto');
        $stmt = $pdo->prepare(
            'INSERT INTO kontestan
             (nomor_peserta,nama,kategori,foto,instagram_url,asal_instansi,tempat_lahir,tanggal_lahir,visi,misi,prestasi,deskripsi)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            trim($_POST['nomor_peserta'] ?? ''),
            trim($_POST['nama'] ?? ''),
            $_POST['kategori'] ?? 'Kakang',
            $foto,
            $instagramUrl ?: null,
            trim($_POST['asal_instansi'] ?? ''),
            trim($_POST['tempat_lahir'] ?? ''),
            ($_POST['tanggal_lahir'] ?? '') ?: null,
            trim($_POST['visi'] ?? ''),
            trim($_POST['misi'] ?? ''),
            trim($_POST['prestasi'] ?? ''),
            trim($_POST['deskripsi'] ?? ''),
        ]);
        flash('success', 'Kontestan berhasil ditambahkan.');
        redirect('admin/kontestan.php');
    } catch (Throwable $e) {
        $error = $e instanceof PDOException && $e->getCode() === '23000'
            ? 'Nomor peserta sudah digunakan dalam kategori tersebut.'
            : $e->getMessage();
    }
}

$page_title = 'Tambah Kontestan';
require __DIR__ . '/../includes/header.php';
?>
<header class="page-head">
  <div class="container">
    <span class="eyebrow">Administrator</span>
    <h1 class="mt-2">Tambah Kontestan</h1>
    <p>Masukkan informasi profil calon Kakang atau Senduk.</p>
  </div>
</header>
<section class="section-pad">
  <div class="container">
    <div class="panel-card">
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if ($error): ?>
          <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="nomor-peserta" class="form-label">Nomor peserta</label>
            <input id="nomor-peserta" name="nomor_peserta" class="form-control" maxlength="10" required>
          </div>
          <div class="col-md-4">
            <label for="nama-kontestan" class="form-label">Nama lengkap</label>
            <input id="nama-kontestan" name="nama" class="form-control" maxlength="100" required>
          </div>
          <div class="col-md-4">
            <label for="kategori" class="form-label">Kategori</label>
            <select id="kategori" name="kategori" class="form-select" required>
              <option value="Kakang">Kakang</option>
              <option value="Senduk">Senduk</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="asal-instansi" class="form-label">Asal sekolah/instansi</label>
            <input id="asal-instansi" name="asal_instansi" class="form-control">
          </div>
          <div class="col-md-3">
            <label for="tempat-lahir" class="form-label">Tempat lahir</label>
            <input id="tempat-lahir" name="tempat_lahir" class="form-control">
          </div>
          <div class="col-md-3">
            <label for="tanggal-lahir" class="form-label">Tanggal lahir</label>
            <input id="tanggal-lahir" type="date" name="tanggal_lahir" class="form-control">
          </div>
          <div class="col-md-6">
            <label for="foto" class="form-label">Foto (JPG/PNG/WEBP, maks. 3 MB)</label>
            <input id="foto" type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp">
          </div>
          <div class="col-md-6">
            <label for="instagram-url" class="form-label">Tautan foto Instagram (opsional)</label>
            <input id="instagram-url" type="url" name="instagram_url" class="form-control" maxlength="255" placeholder="https://www.instagram.com/p/...">
            <div class="small-muted mt-1">Tautan postingan asli ditampilkan untuk membuka foto di Instagram.</div>
          </div>
          <div class="col-md-6">
            <label for="prestasi" class="form-label">Prestasi</label>
            <textarea id="prestasi" name="prestasi" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-md-6">
            <label for="visi" class="form-label">Visi</label>
            <textarea id="visi" name="visi" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-md-6">
            <label for="misi" class="form-label">Misi</label>
            <textarea id="misi" name="misi" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-12">
            <label for="deskripsi" class="form-label">Deskripsi profil</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"></textarea>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-red rounded-pill px-4">Simpan Kontestan</button>
          <a class="btn btn-outline-secondary rounded-pill" href="<?= url('admin/kontestan.php') ?>">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
