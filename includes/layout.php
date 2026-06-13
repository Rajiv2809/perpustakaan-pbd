<?php
// includes/layout.php — Header & Footer helper
function render_header(string $title = 'Perpustakaan'): void { ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> — Perpustakaan</title>
  <link rel="stylesheet" href="/perpustakaan/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <a class="brand" href="/perpustakaan/index.php">📚 Perpustakaan</a>
  <a class="nav-btn" href="/perpustakaan/pages/tambah.php">+ Tambah Buku</a>
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
