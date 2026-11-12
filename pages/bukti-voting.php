<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

if ((current_user()['role'] ?? '') !== 'pemilih') {
    redirect('admin/dashboard.php');
}

$userId = (int) current_user()['id'];
$sql = "SELECT
            v.id AS voting_id,
            v.kategori,
            v.created_at AS waktu_voting,
            p.id AS periode_id,
            p.nama_periode,
            p.tanggal_mulai,
            p.tanggal_selesai,
            k.nomor_peserta,
            k.nama AS nama_kontestan,
            k.foto,
            k.asal_instansi
        FROM voting v
        INNER JOIN kontestan k ON k.id = v.kontestan_id
        INNER JOIN periode_voting p ON p.id = v.periode_id
        WHERE v.user_id = ?
        ORDER BY p.id DESC, v.created_at ASC, v.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$votes = $stmt->fetchAll();

$votesByPeriod = [];
foreach ($votes as $vote) {
    $periodId = (int) $vote['periode_id'];
    if (!isset($votesByPeriod[$periodId])) {
        $votesByPeriod[$periodId] = [
            'nama_periode' => $vote['nama_periode'],
            'votes' => [],
        ];
    }
    $votesByPeriod[$periodId]['votes'][] = $vote;
}

$activePeriod = active_period($pdo);
$pendingCategory = [];
if ($activePeriod) {
    foreach (['Kakang', 'Senduk'] as $category) {
        if (!voted_category($pdo, $userId, (int) $activePeriod['id'], $category)) {
            $pendingCategory[] = $category;
        }
    }
}

$page_title = 'Bukti Voting Saya';
require __DIR__ . '/../includes/header.php';
?>
<header class="page-head">
  <div class="container">
    <span class="eyebrow">Arsip pilihanmu</span>
    <h1 class="mt-2">Bukti Voting Saya</h1>
    <p>Kartu ini menunjukkan pilihan yang sudah berhasil tersimpan di aplikasi.</p>
  </div>
</header>

<section class="section-pad">
  <div class="container">
    <?php if (!$votes): ?>
      <div class="panel-card text-center py-5">
        <div class="receipt-icon mx-auto mb-3">✓</div>
        <h2 class="h4 fw-bold">Belum ada bukti voting</h2>
        <p class="text-muted-custom mb-4">Setelah kamu memilih kontestan dan suara berhasil disimpan, kartu bukti voting akan muncul di halaman ini.</p>
        <?php if ($activePeriod && paid_for_period($pdo, $userId, (int) $activePeriod['id'])): ?>
          <a href="<?= url('pages/voting.php') ?>" class="btn btn-red rounded-pill px-4">Mulai Voting</a>
        <?php elseif ($activePeriod): ?>
          <a href="<?= url('pages/pembayaran.php') ?>" class="btn btn-gold rounded-pill px-4">Lanjut ke Pembayaran Simulasi</a>
        <?php else: ?>
          <span class="small-muted">Belum ada periode voting yang aktif.</span>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
          <span class="eyebrow" style="color:var(--red)">Dokumentasi digital</span>
          <h2 class="h3 fw-bold mb-1">Pilihan yang sudah tercatat</h2>
          <p class="small-muted mb-0">Total <?= count($votes) ?> suara tersimpan dari akun <?= e(current_user()['nama']) ?>.</p>
        </div>
        <button type="button" class="btn btn-green rounded-pill px-4 no-print" onclick="window.print()">Cetak / Simpan PDF</button>
      </div>

      <?php foreach ($votesByPeriod as $periodId => $periodData): ?>
        <div class="receipt-period mb-4">
          <div class="receipt-period-heading">
            <div>
              <span class="eyebrow">Periode pemilihan</span>
              <h3 class="h5 fw-bold mb-0"><?= e($periodData['nama_periode']) ?></h3>
            </div>
            <span class="status-pill status-on">TERCATAT DI SISTEM</span>
          </div>
          <div class="row g-3">
            <?php foreach ($periodData['votes'] as $vote):
                $receiptCode = 'KSP-' . date('Y', strtotime($vote['waktu_voting'])) . '-' . str_pad((string) $vote['voting_id'], 6, '0', STR_PAD_LEFT);
                $photoUrl = !empty($vote['foto']) ? url('uploads/' . rawurlencode($vote['foto'])) : url('assets/img/placeholder.svg');
            ?>
              <div class="col-lg-6">
                <article class="receipt-card">
                  <div class="receipt-topline">
                    <div class="receipt-brand"><span class="brand-mark">KS</span><span>KAKANG SENDUK<small>PONOROGO</small></span></div>
                    <span class="receipt-status"><span class="receipt-status-dot"></span> SUARA TERSIMPAN</span>
                  </div>
                  <div class="receipt-title-row">
                    <div>
                      <span class="receipt-label">BUKTI PARTISIPASI</span>
                      <h4><?= e(strtoupper($vote['kategori'])) ?></h4>
                    </div>
                    <div class="receipt-number"><small>NO. BUKTI</small><strong><?= e($receiptCode) ?></strong></div>
                  </div>
                  <div class="receipt-candidate">
                    <img src="<?= e($photoUrl) ?>" alt="Foto <?= e($vote['nama_kontestan']) ?>" onerror="this.onerror=null;this.src='<?= e(url('assets/img/placeholder.svg')) ?>'">
                    <div class="receipt-candidate-info">
                      <span class="small-muted">Kontestan pilihanmu</span>
                      <h3><?= e($vote['nama_kontestan']) ?></h3>
                      <span class="candidate-number"><?= e($vote['nomor_peserta']) ?></span>
                      <?php if (!empty($vote['asal_instansi'])): ?><p><?= e($vote['asal_instansi']) ?></p><?php endif; ?>
                    </div>
                  </div>
                  <div class="receipt-details">
                    <div><small>Nama pemilih</small><strong><?= e(current_user()['nama']) ?></strong></div>
                    <div><small>Tanggal & waktu</small><strong><?= e(date('d/m/Y H:i', strtotime($vote['waktu_voting']))) ?> WIB</strong></div>
                  </div>
                  <div class="receipt-bottom">
                    <span>Periode #<?= (int) $periodId ?></span>
                    <strong><span class="receipt-check">✓</span> TERVERIFIKASI DATABASE</strong>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if ($activePeriod && $pendingCategory): ?>
        <div class="gateway-box d-flex flex-wrap align-items-center justify-content-between gap-3 no-print">
          <div><strong>Masih ada kategori yang belum dipilih.</strong><div class="small-muted">Kamu belum memberikan suara untuk: <?= e(implode(' dan ', $pendingCategory)) ?>.</div></div>
          <a href="<?= url('pages/voting.php') ?>" class="btn btn-red rounded-pill px-4">Lanjutkan Voting</a>
        </div>
      <?php endif; ?>

      <p class="small-muted mt-4 mb-0">Catatan: kartu ini merupakan bukti digital dari data voting yang tersimpan di aplikasi pembelajaran ini, bukan dokumen resmi penyelenggara pemilihan.</p>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
