<?php
/**
 * generate_hash.php — Jalankan sekali untuk mendapatkan hash password
 * Akses di browser: http://localhost/perpustakaan/generate_hash.php
 * HAPUS FILE INI setelah digunakan!
 */

$passwords = [
    'admin123',
    'petugas123',
];

echo '<pre style="font-family:monospace;padding:2rem">';
echo "Hash password (copy ke database.sql atau users.php):\n\n";
foreach ($passwords as $p) {
    $hash = password_hash($p, PASSWORD_BCRYPT);
    echo "'$p'  =>  $hash\n";
}
echo '</pre>';
echo '<p style="color:red;padding:1rem;font-family:sans-serif"><strong>⚠ Hapus file ini setelah selesai!</strong></p>';
