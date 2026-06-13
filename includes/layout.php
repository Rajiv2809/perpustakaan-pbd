<?php
// includes/layout.php — Header & Footer helper
function render_header(string $title = 'Perpustakaan'): void {
    $user = current_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> — Perpustakaan</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <a class="brand" href="<?= BASE_URL ?>index.php">📚 Perpustakaan</a>
  <div class="nav-right">
    <a class="nav-btn nav-btn-outline" href="<?= BASE_URL ?>pages/tambah.php">+ Tambah Buku</a>
    <?php if ($user['role'] === 'admin'): ?>
      <a class="nav-btn nav-btn-outline" href="<?= BASE_URL ?>pages/users.php">👥 Kelola User</a>
    <?php endif; ?>
    <div class="nav-user">
      <span class="nav-user-info">
        👤 <?= htmlspecialchars($user['nama']) ?>
        <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
      </span>
      <a class="nav-btn nav-btn-logout" href="<?= BASE_URL ?>logout.php"
         onclick="return confirm('Yakin ingin keluar?')">Keluar</a>
    </div>
  </div>
</nav>
<main class="container">
<?php }

function render_footer(): void { ?>
</main>
<footer>
  <p>© <?= date('Y') ?> Sistem Manajemen Perpustakaan &mdash; PHP Native</p>
</footer>
</body>
</html>
<?php }

function flash(string $key): string {
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return '';
}
