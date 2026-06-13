<?php
// pages/tambah_user.php — Tambah user oleh admin
define('BASE_URL', '/perpustakaan/');
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/layout.php';
require_role('admin');

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old      = $_POST;
    $nama     = trim($_POST['nama']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']      ?? '';
    $role     = $_POST['role']          ?? 'petugas';

    if ($nama === '')    $errors['nama']     = 'Nama wajib diisi.';
    if ($username === '') $errors['username'] = 'Username wajib diisi.';
    if (!preg_match('/^[a-z0-9_]{3,50}$/', $username))
                          $errors['username'] = 'Username 3-50 karakter, huruf kecil/angka/underscore.';
    if (strlen($password) < 6) $errors['password'] = 'Password minimal 6 karakter.';
    if (!in_array($role, ['admin','petugas'])) $errors['role'] = 'Role tidak valid.';

    if (empty($errors['username'])) {
        $chk = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $chk->bind_param('s', $username);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $errors['username'] = 'Username sudah digunakan.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $ins  = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
        $ins->bind_param('ssss', $nama, $username, $hash, $role);
        $ins->execute();
        $_SESSION['success'] = "User \"$username\" berhasil ditambahkan.";
        header('Location: users.php');
        exit;
    }
}

render_header('Tambah User');
?>

<div class="page-title">➕ Tambah User Baru</div>
<?php if ($errors): ?><div class="alert alert-error">⚠ Mohon periksa isian di bawah.</div><?php endif; ?>

<div class="form-card">
  <form method="POST" action="tambah_user.php">
    <div class="form-grid">
      <div class="form-group full">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama"
               value="<?= htmlspecialchars($old['nama'] ?? '') ?>" placeholder="Nama lengkap user">
        <?php if (isset($errors['nama'])): ?><small style="color:var(--danger)"><?= $errors['nama'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($old['username'] ?? '') ?>" placeholder="huruf kecil/angka/underscore">
        <?php if (isset($errors['username'])): ?><small style="color:var(--danger)"><?= $errors['username'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role">
          <option value="petugas" <?= ($old['role'] ?? '') !== 'admin' ? 'selected' : '' ?>>Petugas</option>
          <option value="admin"   <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <?php if (isset($errors['role'])): ?><small style="color:var(--danger)"><?= $errors['role'] ?></small><?php endif; ?>
      </div>
      <div class="form-group full">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter">
        <?php if (isset($errors['password'])): ?><small style="color:var(--danger)"><?= $errors['password'] ?></small><?php endif; ?>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Simpan User</button>
      <a href="users.php" class="btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php render_footer(); ?>
