<?php
// includes/db.php — Konfigurasi koneksi database

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaan');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#c0392b;">
            <strong>Koneksi database gagal:</strong> ' . $conn->connect_error . '
         </div>');
}

$conn->set_charset('utf8mb4');
