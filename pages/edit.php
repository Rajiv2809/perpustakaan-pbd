<?php
// pages/edit.php — Edit buku (Update)
session_start();
require_once '../includes/db.php';
require_once '../includes/layout.php';

$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    header('Location: ../index.php');
    exit;
}

$errors        = [];
$kategori_list = ['Fiksi','Non-Fiksi','Sains','Teknologi','Sejarah','Biografi','Lainnya'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul']       ?? '');
    $pengarang = trim($_POST['pengarang']   ?? '');
    $penerbit  = trim($_POST['penerbit']    ?? '');
    $tahun     = (int)($_POST['tahun_terbit'] ?? 0);
    $kategori  = $_POST['kategori']          ?? '';
    $stok      = (int)($_POST['stok']        ?? 0);

    if ($judul === '')     $errors['judul']     = 'Judul wajib diisi.';
    if ($pengarang === '') $errors['pengarang'] = 'Pengarang wajib diisi.';
    if ($penerbit === '')  $errors['penerbit']  = 'Penerbit wajib diisi.';
    if ($tahun < 1800 || $tahun > (int)date('Y'))
                           $errors['tahun']     = 'Tahun tidak valid.';
    if (!in_array($kategori, $kategori_list))
                           $errors['kategori']  = 'Pilih kategori yang valid.';
    if ($stok < 0)         $errors['stok']      = 'Stok tidak boleh negatif.';

    if (empty($errors)) {
        $upd = $conn->prepare(
            "UPDATE buku SET judul=?, pengarang=?, penerbit=?, tahun_terbit=?, kategori=?, stok=?
             WHERE id=?"
        );
        $upd->bind_param('sssissi', $judul, $pengarang, $penerbit, $tahun, $kategori, $stok, $id);
        $upd->execute();

        $_SESSION['success'] = "Buku \"$judul\" berhasil diperbarui.";
        header('Location: ../index.php');
        exit;
    }

    // Isi ulang dari POST jika ada error
    $buku = array_merge($buku, $_POST);
}

render_header('Edit: ' . $buku['judul']);
?>

<div class="page-title">✏️ Edit Buku</div>

<?php if ($errors): ?>
  <div class="alert alert-error">⚠ Mohon periksa isian di bawah.</div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="edit.php">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-grid">

      <div class="form-group full">
        <label for="judul">Judul Buku</label>
        <input type="text" id="judul" name="judul"
               value="<?= htmlspecialchars($buku['judul']) ?>">
        <?php if (isset($errors['judul'])): ?>
          <small style="color:var(--danger)"><?= $errors['judul'] ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="pengarang">Pengarang</label>
        <input type="text" id="pengarang" name="pengarang"
               value="<?= htmlspecialchars($buku['pengarang']) ?>">
        <?php if (isset($errors['pengarang'])): ?>
          <small style="color:var(--danger)"><?= $errors['pengarang'] ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="penerbit">Penerbit</label>
        <input type="text" id="penerbit" name="penerbit"
               value="<?= htmlspecialchars($buku['penerbit']) ?>">
        <?php if (isset($errors['penerbit'])): ?>
          <small style="color:var(--danger)"><?= $errors['penerbit'] ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="tahun_terbit">Tahun Terbit</label>
        <input type="number" id="tahun_terbit" name="tahun_terbit"
               value="<?= htmlspecialchars($buku['tahun_terbit']) ?>"
               min="1800" max="<?= date('Y') ?>">
        <?php if (isset($errors['tahun'])): ?>
          <small style="color:var(--danger)"><?= $errors['tahun'] ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategori_list as $kat): ?>
            <option value="<?= $kat ?>" <?= $buku['kategori'] === $kat ? 'selected' : '' ?>>
              <?= $kat ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['kategori'])): ?>
          <small style="color:var(--danger)"><?= $errors['kategori'] ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok"
               value="<?= htmlspecialchars($buku['stok']) ?>"
               min="0">
        <?php if (isset($errors['stok'])): ?>
          <small style="color:var(--danger)"><?= $errors['stok'] ?></small>
        <?php endif; ?>
      </div>

    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
      <a href="../index.php" class="btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php render_footer(); ?>
