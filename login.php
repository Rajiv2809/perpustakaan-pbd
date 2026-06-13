<?php
// login.php — Halaman login
define('BASE_URL', '/perpustakaan/');
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']      ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nama']  = $user['nama'];
            $_SESSION['user_uname'] = $user['username'];
            $_SESSION['user_role']  = $user['role'];
            header('Location: ' . BASE_URL . 'index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Perpustakaan</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: var(--bg);
      font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .login-wrap { width: 100%; max-width: 400px; padding: 1.25rem; }

    .login-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 2.5rem 2rem 2rem;
      box-shadow: 0 4px 24px rgba(0,0,0,.07);
    }

    /* Logo */
    .logo-ring {
      width: 60px; height: 60px;
      border-radius: 50%;
      background: var(--accent-lt);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1.1rem;
      font-size: 1.7rem;
    }

    .login-title {
      text-align: center;
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--ink);
      margin-bottom: .25rem;
    }

    .login-sub {
      text-align: center;
      font-size: .85rem;
      color: var(--ink-muted);
      margin-bottom: 2rem;
    }

    /* Error */
    .login-error {
      display: flex;
      align-items: center;
      gap: .5rem;
      background: var(--danger-lt);
      border: 1px solid #f5c6c2;
      border-radius: 8px;
      padding: .7rem .9rem;
      margin-bottom: 1.25rem;
      font-size: .88rem;
      color: var(--danger);
    }
    .login-error .err-icon { font-size: 1rem; flex-shrink: 0; }

    /* Form groups */
    .fg { margin-bottom: 1.1rem; }
    .fg label {
      display: block;
      font-size: .78rem;
      font-weight: 600;
      color: var(--ink-muted);
      text-transform: uppercase;
      letter-spacing: .45px;
      margin-bottom: .4rem;
    }
    .inp-wrap { position: relative; display: flex; align-items: center; }
    .inp-icon {
      position: absolute;
      left: 11px;
      color: var(--ink-muted);
      font-size: 1.05rem;
      pointer-events: none;
    }
    .fg input {
      width: 100%;
      padding: .65rem .9rem .65rem 2.3rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: .93rem;
      background: var(--surface);
      color: var(--ink);
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .fg input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(43,95,63,.12);
    }
    .toggle-pw {
      position: absolute; right: 10px;
      background: none; border: none;
      cursor: pointer; color: var(--ink-muted);
      padding: 0; font-size: 1rem;
      display: flex; align-items: center;
    }
    .toggle-pw:hover { color: var(--ink); }

    /* Buttons */
    .btn-login {
      width: 100%;
      padding: .72rem;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: .95rem;
      font-weight: 600;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: .4rem;
      transition: background .15s;
      margin-top: .5rem;
    }
    .btn-login:hover { background: #224d32; }

    .divider {
      display: flex; align-items: center; gap: 10px;
      margin: 1.35rem 0;
    }
    .divider hr { flex: 1; border: none; border-top: 1px solid var(--border); }
    .divider span { font-size: .78rem; color: var(--ink-muted); white-space: nowrap; }

    .btn-register {
      width: 100%;
      padding: .7rem;
      background: transparent;
      color: var(--accent);
      border: 1.5px solid var(--accent);
      border-radius: 8px;
      font-size: .93rem;
      font-weight: 600;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      display: flex; align-items: center; justify-content: center; gap: .4rem;
      transition: background .15s;
    }
    .btn-register:hover { background: var(--accent-lt); }

    /* Hint box */
    .hint-box {
      background: var(--accent-lt);
      border: 1px solid #b7d4c3;
      border-radius: 8px;
      padding: .85rem 1rem;
      margin-top: 1.3rem;
    }
    .hint-box .hint-title {
      font-size: .8rem;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: .35rem;
    }
    .hint-box p {
      font-size: .8rem;
      color: var(--accent);
      line-height: 1.85;
      margin: 0;
    }
    .hint-box code {
      background: rgba(43,95,63,.15);
      padding: 1px 5px;
      border-radius: 4px;
      font-size: .78rem;
      font-family: monospace;
    }
  </style>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">

    <div class="logo-ring">📚</div>
    <div class="login-title">Perpustakaan</div>
    <div class="login-sub">Sistem Manajemen Buku</div>

    <?php if ($error): ?>
    <div class="login-error">
      <span class="err-icon">⚠</span>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="on">
      <div class="fg">
        <label for="username">Username</label>
        <div class="inp-wrap">
          <span class="inp-icon">👤</span>
          <input type="text" id="username" name="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 placeholder="Masukkan username"
                 autocomplete="username" autofocus>
        </div>
      </div>

      <div class="fg">
        <label for="password">Password</label>
        <div class="inp-wrap">
          <span class="inp-icon">🔒</span>
          <input type="password" id="password" name="password"
                 placeholder="Masukkan password"
                 autocomplete="current-password">
          <button type="button" class="toggle-pw" id="togglePw" aria-label="Tampilkan password">
            <span id="eyeIcon">👁</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">
        Masuk <span>→</span>
      </button>
    </form>

    <div class="divider">
      <hr><span>atau daftar akun baru</span><hr>
    </div>

    <a href="register.php" class="btn-register">
      ✎ Daftar Sekarang
    </a>

    <div class="hint-box">
      <div class="hint-title">🔑 Akun Default</div>
      <p>
        Admin &nbsp;&nbsp;— <code>admin</code> / <code>admin123</code><br>
        Petugas — <code>petugas</code> / <code>petugas123</code>
      </p>
    </div>

  </div>
</div>

<script>
  const togglePw = document.getElementById('togglePw');
  const pwInput  = document.getElementById('password');
  const eyeIcon  = document.getElementById('eyeIcon');
  togglePw.addEventListener('click', () => {
    const show   = pwInput.type === 'password';
    pwInput.type = show ? 'text' : 'password';
    eyeIcon.textContent = show ? '🙈' : '👁';
  });
</script>
</body>
</html>
