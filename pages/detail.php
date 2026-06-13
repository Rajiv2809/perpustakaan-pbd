<?php
// pages/detail.php — Detail buku (Read single)
session_start();
require_once '../includes/db.php';
require_once '../includes/layout.php';

$id   = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    $_SESSION['success'] = '';
    header('Location: ../index.php');
    exit;
}

render_header('Detail: ' . $buku['judul']);
?>

<div class="page-title">📘 Detail Buku</div>

<div class="detail-card">

  <div class="detail-row">
    <div class="detail-label">Judul</div>
    <div class="detail-value"><strong><?= htmlspecialchars($buku['judul']) ?></strong></div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Pengarang</div>
    <div class="detail-value"><?= htmlspecialchars($buku['pengarang']) ?></div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Penerbit</div>
    <div class="detail-value"><?= htmlspecialchars($buku['penerbit']) ?></div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Tahun Terbit</div>
    <div class="detail-value"><?= $buku['tahun_terbit'] ?></div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Kategori</div>
    <div class="detail-value"><?= htmlspecialchars($buku['kategori']) ?></div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Stok</div>
    <div class="detail-value">
      <?php
        $stok  = (int)$buku['stok'];
        $badge = $stok > 5 ? 'badge-green' : ($stok > 0 ? 'badge-yellow' : 'badge-red');
        $label = $stok > 5 ? 'Tersedia'    : ($stok > 0 ? 'Terbatas'     : 'Habis');
      ?>
      <span class="badge <?= $badge ?>"><?= $stok ?> — <?= $label ?></span>
    </div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Ditambahkan</div>
    <div class="detail-value"><?= date('d M Y, H:i', strtotime($buku['created_at'])) ?></div>
  </div>

  <div class="detail-row">
    <div class="detail-label">Diupdate</div>
    <div class="detail-value"><?= date('d M Y, H:i', strtotime($buku['updated_at'])) ?></div>
  </div>

</div>

<div class="form-actions" style="margin-top:1.5rem">
  <a class="btn-primary" href="edit.php?id=<?= $buku['id'] ?>">✏️ Edit</a>
  <a class="btn-secondary" href="../index.php">← Kembali</a>
</div>

<?php render_footer(); ?>
