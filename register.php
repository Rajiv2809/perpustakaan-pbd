<?php
// register.php — Halaman daftar akun baru
define('BASE_URL', '/perpustakaan/');
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old      = $_POST;
    $nama     = trim($_POST['nama']      ?? '');
    $username = trim($_POST['username']  ?? '');
    $password = $_POST['password']       ?? '';
    $confirm  = $_POST['confirm']        ?? '';

    if ($nama === '')                    $errors['nama']     = 'Nama wajib diisi.';
    if (strlen($nama) > 100)             $errors['nama']     = 'Nama maksimal 100 karakter.';
    if ($username === '')                $errors['username'] = 'Username wajib diisi.';
    if (!preg_match('/^[a-z0-9_]{3,50}$/', $username))
                                         $errors['username'] = 'Username 3-50 karakter, huruf kecil/angka/underscore.';
    if (strlen($password) < 6)           $errors['password'] = 'Password minimal 6 karakter.';
    if ($password !== $confirm)          $errors['confirm']  = 'Konfirmasi password tidak cocok.';

    // Cek username duplikat
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
        $ins  = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'petugas')");
        $ins->bind_param('sss', $nama, $username, $hash);
        $ins->execute();

        $_SESSION['reg_success'] = "Akun berhasil dibuat! Silakan login.";
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun — Perpustakaan</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--bg); }
    .login-wrap { width:100%; max-width:420px; padding:1rem; }
    .login-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:2.5rem 2rem; box-shadow:0 4px 20px rgba(0,0,0,.08); }
    .login-logo  { text-align:center; font-size:2rem; margin-bottom:.4rem; }
    .login-title { text-align:center; font-size:1.25rem; font-weight:800; color:var(--ink); margin-bottom:.3rem; }
    .login-sub   { text-align:center; font-size:.85rem; color:var(--ink-muted); margin-bottom:1.6rem; }
    .form-group  { margin-bottom:1rem; }
    .login-card label { display:block; font-size:.82rem; font-weight:600; color:var(--ink-muted); text-transform:uppercase; letter-spacing:.4px; margin-bottom:.35rem; }
    .login-card input { width:100%; }
    .field-hint  { font-size:.78rem; color:var(--ink-muted); margin-top:.3rem; }
    .field-err   { font-size:.8rem; color:var(--danger); margin-top:.25rem; }
    .btn-login   { width:100%; padding:.75rem; font-size:1rem; margin-top:.4rem; background:var(--accent); color:#fff; border:none; border-radius:var(--radius); font-weight:700; cursor:pointer; transition:opacity .15s; }
    .btn-login:hover { opacity:.88; }
    .login-footer { text-align:center; margin-top:1.25rem; font-size:.85rem; color:var(--ink-muted); }
    .login-footer a { color:var(--accent); font-weight:600; text-decoration:none; }
    .role-note { background:var(--warn-lt); border:1px solid #e8d5a3; border-radius:var(--radius); padding:.75rem 1rem; margin-top:1.1rem; font-size:.82rem; color:var(--warn); }
  </style>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">📝</div>
    <div class="login-title">Daftar Akun</div>
    <div class="login-sub">Buat akun baru sebagai Petugas</div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error" style="margin-bottom:1.2rem">⚠ Mohon periksa isian di bawah.</div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama"
               value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
               placeholder="Contoh: Budi Santoso" autofocus>
        <?php if (isset($errors['nama'])): ?>
          <div class="field-err">⚠ <?= $errors['nama'] ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($old['username'] ?? '') ?>"
               placeholder="Contoh: budi_santoso">
        <div class="field-hint">Huruf kecil, angka, underscore. 3–50 karakter.</div>
        <?php if (isset($errors['username'])): ?>
          <div class="field-err">⚠ <?= $errors['username'] ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter">
        <?php if (isset($errors['password'])): ?>
          <div class="field-err">⚠ <?= $errors['password'] ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="confirm">Konfirmasi Password</label>
        <input type="password" id="confirm" name="confirm" placeholder="Ulangi password">
        <?php if (isset($errors['confirm'])): ?>
          <div class="field-err">⚠ <?= $errors['confirm'] ?></div>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn-login">Daftar Sekarang</button>
    </form>

    <div class="login-footer">
      Sudah punya akun? <a href="login.php">Masuk di sini</a>
    </div>

    <div class="role-note">
      ℹ️ Akun yang didaftar otomatis mendapat role <strong>Petugas</strong>. Hubungi admin untuk upgrade ke Admin.
    </div>
  </div>
</div>
</body>
</html>
