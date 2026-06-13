<?php
// pages/tambah.php — Tambah buku (Create)
define('BASE_URL', '/perpustakaan/');
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/layout.php';
require_login();

$errors = [];
$old    = [];
$kategori_list = ['Fiksi','Non-Fiksi','Sains','Teknologi','Sejarah','Biografi','Lainnya'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $judul     = trim($_POST['judul']        ?? '');
    $pengarang = trim($_POST['pengarang']    ?? '');
    $penerbit  = trim($_POST['penerbit']     ?? '');
    $tahun     = (int)($_POST['tahun_terbit'] ?? 0);
    $kategori  = $_POST['kategori']           ?? '';
    $stok      = (int)($_POST['stok']         ?? 0);

    if ($judul === '')     $errors['judul']     = 'Judul wajib diisi.';
    if ($pengarang === '') $errors['pengarang'] = 'Pengarang wajib diisi.';
    if ($penerbit === '')  $errors['penerbit']  = 'Penerbit wajib diisi.';
    if ($tahun < 1800 || $tahun > (int)date('Y'))
                           $errors['tahun']     = 'Tahun tidak valid.';
    if (!in_array($kategori, $kategori_list))
                           $errors['kategori']  = 'Pilih kategori yang valid.';
    if ($stok < 0)         $errors['stok']      = 'Stok tidak boleh negatif.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, kategori, stok) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssisi', $judul, $pengarang, $penerbit, $tahun, $kategori, $stok);
        $stmt->execute();

        $_SESSION['success'] = "Buku \"$judul\" berhasil ditambahkan.";
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

render_header('Tambah Buku');
?>

<div class="page-title">➕ Tambah Buku Baru</div>

<?php if ($errors): ?>
  <div class="alert alert-error">⚠ Mohon periksa isian di bawah.</div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="tambah.php">
    <div class="form-grid">
      <div class="form-group full">
        <label for="judul">Judul Buku</label>
        <input type="text" id="judul" name="judul"
               value="<?= htmlspecialchars($old['judul'] ?? '') ?>" placeholder="Masukkan judul buku">
        <?php if (isset($errors['judul'])): ?>
          <small style="color:var(--danger)"><?= $errors['judul'] ?></small>
        <?php endif; ?>
      </div>
      <div class="form-group">
        <label for="pengarang">Pengarang</label>
        <input type="text" id="pengarang" name="pengarang"
               value="<?= htmlspecialchars($old['pengarang'] ?? '') ?>" placeholder="Nama penulis">
        <?php if (isset($errors['pengarang'])): ?><small style="color:var(--danger)"><?= $errors['pengarang'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="penerbit">Penerbit</label>
        <input type="text" id="penerbit" name="penerbit"
               value="<?= htmlspecialchars($old['penerbit'] ?? '') ?>" placeholder="Nama penerbit">
        <?php if (isset($errors['penerbit'])): ?><small style="color:var(--danger)"><?= $errors['penerbit'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="tahun_terbit">Tahun Terbit</label>
        <input type="number" id="tahun_terbit" name="tahun_terbit"
               value="<?= htmlspecialchars($old['tahun_terbit'] ?? date('Y')) ?>"
               min="1800" max="<?= date('Y') ?>">
        <?php if (isset($errors['tahun'])): ?><small style="color:var(--danger)"><?= $errors['tahun'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategori_list as $kat): ?>
            <option value="<?= $kat ?>" <?= ($old['kategori'] ?? '') === $kat ? 'selected' : '' ?>><?= $kat ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['kategori'])): ?><small style="color:var(--danger)"><?= $errors['kategori'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok"
               value="<?= htmlspecialchars($old['stok'] ?? '0') ?>" min="0">
        <?php if (isset($errors['stok'])): ?><small style="color:var(--danger)"><?= $errors['stok'] ?></small><?php endif; ?>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Simpan Buku</button>
      <a href="<?= BASE_URL ?>index.php" class="btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php render_footer(); ?>
