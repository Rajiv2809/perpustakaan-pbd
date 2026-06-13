<?php
// pages/reset_password.php — Reset password user (Admin only)
define('BASE_URL', '/perpustakaan/');
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/layout.php';
require_role('admin');

$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) { header('Location: users.php'); exit; }

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm']      ?? '';

    if (strlen($new_pass) < 6) $errors['new_password'] = 'Password minimal 6 karakter.';
    if ($new_pass !== $confirm) $errors['confirm']      = 'Konfirmasi tidak cocok.';

    if (empty($errors)) {
        $hash = password_hash($new_pass, PASSWORD_BCRYPT);
        $upd  = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->bind_param('si', $hash, $id);
        $upd->execute();
        $_SESSION['success'] = "Password untuk \"{$user['username']}\" berhasil direset.";
        header('Location: users.php');
        exit;
    }
}

render_header('Reset Password');
?>

<div class="page-title">🔑 Reset Password</div>
<p style="color:var(--ink-muted);margin-bottom:1.5rem">
  Mengubah password untuk user: <strong><?= htmlspecialchars($user['nama']) ?></strong>
  (<code><?= htmlspecialchars($user['username']) ?></code>)
</p>

<?php if ($errors): ?><div class="alert alert-error">⚠ Mohon periksa isian.</div><?php endif; ?>

<div class="form-card">
  <form method="POST" action="reset_password.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-grid">
      <div class="form-group">
        <label for="new_password">Password Baru</label>
        <input type="password" id="new_password" name="new_password" placeholder="Minimal 6 karakter">
        <?php if (isset($errors['new_password'])): ?><small style="color:var(--danger)"><?= $errors['new_password'] ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label for="confirm">Konfirmasi Password</label>
        <input type="password" id="confirm" name="confirm" placeholder="Ulangi password baru">
        <?php if (isset($errors['confirm'])): ?><small style="color:var(--danger)"><?= $errors['confirm'] ?></small><?php endif; ?>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">🔑 Reset Password</button>
      <a href="users.php" class="btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php render_footer(); ?>
