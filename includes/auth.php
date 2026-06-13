<?php
// includes/auth.php — Helper autentikasi

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Pastikan user sudah login. Jika belum, redirect ke halaman login.
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

/**
 * Pastikan user memiliki role tertentu.
 */
function require_role(string $role): void {
    require_login();
    if ($_SESSION['user_role'] !== $role) {
        $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk ' . $role . '.';
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

/**
 * Cek apakah user sudah login (return bool).
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Ambil data user yang sedang login.
 */
function current_user(): array {
    return [
        'id'       => $_SESSION['user_id']   ?? null,
        'nama'     => $_SESSION['user_nama']  ?? '',
        'username' => $_SESSION['user_uname'] ?? '',
        'role'     => $_SESSION['user_role']  ?? '',
    ];
}
