<?php
// index.php — Halaman daftar buku (Read)
session_start();
require_once 'includes/db.php';
require_once 'includes/layout.php';

// Search
$search = trim($_GET['q'] ?? '');
$where  = '';
$params = [];
$types  = '';

if ($search !== '') {
    $where  = "WHERE judul LIKE ? OR pengarang LIKE ? OR penerbit LIKE ?";
    $like   = "%{$search}%";
    $params = [$like, $like, $like];
    $types  = 'sss';
}

// Hitung total & statistik
$total_buku  = $conn->query("SELECT COUNT(*) AS c FROM buku")->fetch_assoc()['c'];
$total_stok  = $conn->query("SELECT SUM(stok) AS s FROM buku")->fetch_assoc()['s'] ?? 0;
$total_kat   = $conn->query("SELECT COUNT(DISTINCT kategori) AS k FROM buku")->fetch_assoc()['k'];

// Ambil data buku
$sql  = "SELECT * FROM buku $where ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$buku_list = $result->fetch_all(MYSQLI_ASSOC);

render_header('Daftar Buku');
?>

<?php $msg = flash('success'); if ($msg): ?>
  <div class="alert alert-success">✓ <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-row">
  <div class="stat-card">
    <div class="stat-num"><?= $total_buku ?></div>
    <div class="stat-label">Total Judul</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= $total_stok ?></div>
    <div class="stat-label">Total Stok</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= $total_kat ?></div>
    <div class="stat-label">Kategori</div>
  </div>
</div>

<div class="page-title">
  📖 Daftar Buku
  <?php if ($search): ?><span>pencarian: "<?= htmlspecialchars($search) ?>"</span><?php endif; ?>
</div>

<!-- Search -->
<form class="search-row" method="GET" action="index.php">
  <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
         placeholder="Cari judul, pengarang, atau penerbit…">
  <button type="submit">Cari</button>
  <?php if ($search): ?>
    <a href="index.php" class="btn-sm btn-secondary btn-reset">✕ Reset</a>
  <?php endif; ?>
</form>

<!-- Tabel -->
<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Judul</th>
        <th>Pengarang</th>
        <th>Penerbit</th>
        <th>Tahun</th>
        <th>Kategori</th>
        <th class="center">Stok</th>
        <th class="center">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($buku_list)): ?>
      <tr class="empty-row">
        <td colspan="8">📭 Tidak ada buku ditemukan.</td>
      </tr>
    <?php else: ?>
      <?php foreach ($buku_list as $i => $b): ?>
        <?php
          $stok  = (int)$b['stok'];
          $badge = $stok > 5 ? 'badge-green' : ($stok > 0 ? 'badge-yellow' : 'badge-red');
          $label = $stok > 5 ? 'Tersedia'    : ($stok > 0 ? 'Terbatas'     : 'Habis');
        ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($b['judul']) ?></strong></td>
          <td><?= htmlspecialchars($b['pengarang']) ?></td>
          <td><?= htmlspecialchars($b['penerbit']) ?></td>
          <td><?= $b['tahun_terbit'] ?></td>
          <td><?= htmlspecialchars($b['kategori']) ?></td>
          <td class="center">
            <span class="badge <?= $badge ?>"><?= $stok ?> &mdash; <?= $label ?></span>
          </td>
          <td class="center">
            <div class="action-group">
              <a class="btn-sm btn-detail" href="pages/detail.php?id=<?= $b['id'] ?>">Detail</a>
              <a class="btn-sm btn-edit"   href="pages/edit.php?id=<?= $b['id'] ?>">Edit</a>
              <a class="btn-sm btn-delete"
                 href="pages/hapus.php?id=<?= $b['id'] ?>"
                 onclick="return confirm('Hapus buku ini?')">Hapus</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php render_footer(); ?>
