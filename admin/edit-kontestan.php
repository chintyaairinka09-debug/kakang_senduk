<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM kontestan WHERE id = ?');
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    flash('error', 'Kontestan tidak ditemukan.');
    redirect('admin/kontestan.php');
}

$error = '';
$form = [
    'nomor_peserta' => $c['nomor_peserta'],
    'nama' => $c['nama'],
    'kategori' => $c['kategori'],
    'instagram_url' => $c['instagram_url'] ?? '',
    'asal_instansi' => $c['asal_instansi'] ?? '',
    'tempat_lahir' => $c['tempat_lahir'] ?? '',
    'tanggal_lahir' => $c['tanggal_lahir'] ?? '',
    'visi' => $c['visi'] ?? '',
    'misi' => $c['misi'] ?? '',
    'prestasi' => $c['prestasi'] ?? '',
    'deskripsi' => $c['deskripsi'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    foreach ($form as $field => $value) {
        $form[$field] = trim($_POST[$field] ?? '');
    }

    if ($form['nomor_peserta'] === '' || $form['nama'] === '') {
        $error = 'Nomor peserta dan nama lengkap wajib diisi.';
    } elseif (!in_array($form['kategori'], ['Kakang', 'Senduk'], true)) {
        $error = 'Pilih kategori kontestan yang valid.';
    } elseif ($form['instagram_url'] !== '' && !preg_match('~^https://www\.instagram\.com/p/[A-Za-z0-9_-]+/?(?:\?.*)?$~i', $form['instagram_url'])) {
        $error = 'Tautan Instagram harus berupa tautan postingan yang valid.';
    } elseif ($form['tanggal_lahir'] !== '') {
        $date = DateTime::createFromFormat('!Y-m-d', $form['tanggal_lahir']);
        if (!$date || $date->format('Y-m-d') !== $form['tanggal_lahir']) {
            $error = 'Tanggal lahir tidak valid.';
        }
    }

    if ($error === '') {
        $duplicate = $pdo->prepare(
            'SELECT id FROM kontestan WHERE nomor_peserta = ? AND kategori = ? AND id <> ? LIMIT 1'
        );
        $duplicate->execute([$form['nomor_peserta'], $form['kategori'], $id]);

        if ($duplicate->fetchColumn()) {
            $error = 'Nomor peserta sudah digunakan dalam kategori tersebut.';
        }
    }

    if ($error === '') {
        try {
            $foto = upload_photo('foto', $c['foto']);
            $update = $pdo->prepare(
                'UPDATE kontestan
                 SET nomor_peserta = ?, nama = ?, kategori = ?, foto = ?, instagram_url = ?, asal_instansi = ?,
                     tempat_lahir = ?, tanggal_lahir = ?, visi = ?, misi = ?, prestasi = ?, deskripsi = ?
                 WHERE id = ?'
            );
            $update->execute([
                $form['nomor_peserta'],
                $form['nama'],
                $form['kategori'],
                $foto,
                $form['instagram_url'] ?: null,
                $form['asal_instansi'],
                $form['tempat_lahir'],
                $form['tanggal_lahir'] ?: null,
                $form['visi'],
                $form['misi'],
                $form['prestasi'],
                $form['deskripsi'],
                $id,
            ]);

            flash('success', 'Semua data kontestan berhasil diperbarui.');
            redirect('admin/kontestan.php');
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Data kontestan gagal disimpan. Periksa kembali data lalu coba lagi.';
        }
    }
}

$page_title = 'Edit Kontestan';
require __DIR__ . '/../includes/header.php';
?>
<header class="page-head">
  <div class="container">
    <span class="eyebrow">Administrator</span>
    <h1 class="mt-2">Edit Kontestan</h1>
    <p>Ubah semua informasi profil kontestan di bawah ini.</p>
  </div>
</header>
<section class="section-pad">
  <div class="container">
    <div class="panel-card">
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $id ?>">
        <?php if ($error): ?>
          <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <h2 class="h5 fw-bold mb-3">Data utama</h2>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="nomor-peserta" class="form-label">Nomor peserta</label>
            <input id="nomor-peserta" name="nomor_peserta" class="form-control" maxlength="10" required value="<?= e($form['nomor_peserta']) ?>">
          </div>
          <div class="col-md-4">
            <label for="nama-kontestan" class="form-label">Nama lengkap</label>
            <input id="nama-kontestan" name="nama" class="form-control" maxlength="100" required value="<?= e($form['nama']) ?>">
          </div>
          <div class="col-md-4">
            <label for="kategori" class="form-label">Kategori</label>
            <select id="kategori" name="kategori" class="form-select" required>
              <option value="Kakang" <?= $form['kategori'] === 'Kakang' ? 'selected' : '' ?>>Kakang</option>
              <option value="Senduk" <?= $form['kategori'] === 'Senduk' ? 'selected' : '' ?>>Senduk</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="asal-instansi" class="form-label">Asal sekolah/instansi</label>
            <input id="asal-instansi" name="asal_instansi" class="form-control" maxlength="150" value="<?= e($form['asal_instansi']) ?>">
          </div>
          <div class="col-md-3">
            <label for="tempat-lahir" class="form-label">Tempat lahir</label>
            <input id="tempat-lahir" name="tempat_lahir" class="form-control" maxlength="100" value="<?= e($form['tempat_lahir']) ?>">
          </div>
          <div class="col-md-3">
            <label for="tanggal-lahir" class="form-label">Tanggal lahir</label>
            <input id="tanggal-lahir" type="date" name="tanggal_lahir" class="form-control" value="<?= e($form['tanggal_lahir']) ?>">
          </div>
          <div class="col-md-6">
            <label for="foto" class="form-label">Ganti foto (opsional)</label>
            <input id="foto" type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="small-muted mt-1">Kosongkan jika tidak ingin mengganti foto. Format JPG/PNG/WEBP, maksimal 3 MB.</div>
            <div class="small-muted mt-2">Foto saat ini:</div>
            <img class="admin-candidate-preview mt-1" src="<?= $c['foto'] ? url('uploads/' . rawurlencode($c['foto'])) : url('assets/img/placeholder.svg') ?>" alt="Foto saat ini <?= e($c['nama']) ?>">
          </div>
          <div class="col-md-6">
            <label for="instagram-url" class="form-label">Tautan foto Instagram (opsional)</label>
            <input id="instagram-url" type="url" name="instagram_url" class="form-control" maxlength="255" placeholder="https://www.instagram.com/p/..." value="<?= e($form['instagram_url']) ?>">
            <div class="small-muted mt-1">Tautan ini ditampilkan agar pengunjung bisa membuka postingan asli.</div>
          </div>
        </div>

        <h2 class="h5 fw-bold mt-4 mb-3">Profil kontestan</h2>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="prestasi" class="form-label">Prestasi</label>
            <textarea id="prestasi" name="prestasi" class="form-control" rows="3"><?= e($form['prestasi']) ?></textarea>
          </div>
          <div class="col-md-6">
            <label for="visi" class="form-label">Visi</label>
            <textarea id="visi" name="visi" class="form-control" rows="3"><?= e($form['visi']) ?></textarea>
          </div>
          <div class="col-12">
            <label for="misi" class="form-label">Misi</label>
            <textarea id="misi" name="misi" class="form-control" rows="3"><?= e($form['misi']) ?></textarea>
          </div>
          <div class="col-12">
            <label for="deskripsi" class="form-label">Deskripsi profil</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"><?= e($form['deskripsi']) ?></textarea>
          </div>
        </div>

        <div class="mt-4">
          <button class="btn btn-red rounded-pill px-4">Simpan Semua Perubahan</button>
          <a class="btn btn-outline-secondary rounded-pill" href="<?= url('admin/kontestan.php') ?>">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
