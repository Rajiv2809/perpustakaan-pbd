<?php
// pages/detail.php — Detail buku
define('BASE_URL', '/perpustakaan/');
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/layout.php';
require_login();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) { header('Location: ' . BASE_URL . 'index.php'); exit; }

render_header('Detail: ' . $buku['judul']);
?>

<div class="page-title">📘 Detail Buku</div>
<div class="detail-card">
  <div class="detail-row"><div class="detail-label">Judul</div><div class="detail-value"><strong><?= htmlspecialchars($buku['judul']) ?></strong></div></div>
  <div class="detail-row"><div class="detail-label">Pengarang</div><div class="detail-value"><?= htmlspecialchars($buku['pengarang']) ?></div></div>
  <div class="detail-row"><div class="detail-label">Penerbit</div><div class="detail-value"><?= htmlspecialchars($buku['penerbit']) ?></div></div>
  <div class="detail-row"><div class="detail-label">Tahun Terbit</div><div class="detail-value"><?= $buku['tahun_terbit'] ?></div></div>
  <div class="detail-row"><div class="detail-label">Kategori</div><div class="detail-value"><?= htmlspecialchars($buku['kategori']) ?></div></div>
  <div class="detail-row">
    <div class="detail-label">Stok</div>
    <div class="detail-value">
      <?php $s=$buku['stok']; $b=$s>5?'badge-green':($s>0?'badge-yellow':'badge-red'); $l=$s>5?'Tersedia':($s>0?'Terbatas':'Habis'); ?>
      <span class="badge <?= $b ?>"><?= $s ?> — <?= $l ?></span>
    </div>
  </div>
  <div class="detail-row"><div class="detail-label">Ditambahkan</div><div class="detail-value"><?= date('d M Y, H:i', strtotime($buku['created_at'])) ?></div></div>
  <div class="detail-row"><div class="detail-label">Diupdate</div><div class="detail-value"><?= date('d M Y, H:i', strtotime($buku['updated_at'])) ?></div></div>
</div>
<div class="form-actions" style="margin-top:1.5rem">
  <a class="btn-primary" href="edit.php?id=<?= $buku['id'] ?>">✏️ Edit</a>
  <a class="btn-secondary" href="<?= BASE_URL ?>index.php">← Kembali</a>
</div>

<?php render_footer(); ?>
