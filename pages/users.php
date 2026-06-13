<?php
// pages/users.php — Manajemen User (Admin only)
define('BASE_URL', '/perpustakaan/');
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/layout.php';
require_role('admin');  // Hanya admin

$success = flash('success');
$error   = flash('error');

// Ambil semua user
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Handle hapus user (POST untuk keamanan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $uid = (int)($_POST['uid'] ?? 0);

    if ($_POST['action'] === 'delete' && $uid > 0) {
        // Tidak bisa hapus diri sendiri
        if ($uid === (int)$_SESSION['user_id']) {
            $_SESSION['error'] = 'Tidak bisa menghapus akun yang sedang aktif.';
        } else {
            $sel = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $sel->bind_param('i', $uid);
            $sel->execute();
            $row = $sel->get_result()->fetch_assoc();
            if ($row) {
                $del = $conn->prepare("DELETE FROM users WHERE id = ?");
                $del->bind_param('i', $uid);
                $del->execute();
                $_SESSION['success'] = "User \"{$row['username']}\" berhasil dihapus.";
            }
        }
        header('Location: users.php');
        exit;
    }

    if ($_POST['action'] === 'toggle_role' && $uid > 0) {
        if ($uid === (int)$_SESSION['user_id']) {
            $_SESSION['error'] = 'Tidak bisa mengubah role akun aktif.';
        } else {
            $sel = $conn->prepare("SELECT role FROM users WHERE id = ?");
            $sel->bind_param('i', $uid);
            $sel->execute();
            $row = $sel->get_result()->fetch_assoc();
            if ($row) {
                $new_role = $row['role'] === 'admin' ? 'petugas' : 'admin';
                $upd = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                $upd->bind_param('si', $new_role, $uid);
                $upd->execute();
                $_SESSION['success'] = "Role berhasil diubah menjadi $new_role.";
            }
        }
        header('Location: users.php');
        exit;
    }
}

render_header('Manajemen User');
?>

<div class="page-title">👥 Manajemen User</div>

<?php if ($success): ?><div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div style="margin-bottom:1.25rem;display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
  <a class="btn-primary" href="tambah_user.php">+ Tambah User</a>
  <a class="btn-secondary" href="<?= BASE_URL ?>index.php">← Kembali</a>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Nama</th>
        <th>Username</th>
        <th class="center">Role</th>
        <th>Bergabung</th>
        <th class="center">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($users)): ?>
      <tr class="empty-row"><td colspan="6">Tidak ada user.</td></tr>
    <?php else: ?>
      <?php foreach ($users as $i => $u): ?>
        <?php $is_me = ((int)$u['id'] === (int)$_SESSION['user_id']); ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:.65rem">
              <div class="avatar"><?= mb_strtoupper(mb_substr($u['nama'], 0, 1)) ?></div>
              <div>
                <?= htmlspecialchars($u['nama']) ?>
                <?php if ($is_me): ?><span class="badge badge-green" style="font-size:.7rem">Kamu</span><?php endif; ?>
              </div>
            </div>
          </td>
          <td><code><?= htmlspecialchars($u['username']) ?></code></td>
          <td class="center">
            <span class="badge <?= $u['role'] === 'admin' ? 'badge-yellow' : 'badge-green' ?>">
              <?= ucfirst($u['role']) ?>
            </span>
          </td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td class="center">
            <?php if (!$is_me): ?>
              <div class="action-group" style="justify-content:center">
                <!-- Toggle Role -->
                <form method="POST" action="users.php" style="display:inline">
                  <input type="hidden" name="action" value="toggle_role">
                  <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                  <button type="submit" class="btn-sm btn-edit"
                    onclick="return confirm('Ubah role user ini?')">
                    ⇄ Role
                  </button>
                </form>
                <!-- Reset Password -->
                <a class="btn-sm btn-detail" href="reset_password.php?id=<?= $u['id'] ?>">🔑 Reset</a>
                <!-- Hapus -->
                <form method="POST" action="users.php" style="display:inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                  <button type="submit" class="btn-sm btn-delete"
                    onclick="return confirm('Hapus user <?= htmlspecialchars($u['username']) ?>?')">
                    Hapus
                  </button>
                </form>
              </div>
            <?php else: ?>
              <span style="color:var(--ink-muted);font-size:.82rem">—</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php render_footer(); ?>
