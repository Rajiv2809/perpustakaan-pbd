<?php
// pages/hapus.php — Hapus buku (Delete)
session_start();
require_once '../includes/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Ambil judul dulu untuk pesan konfirmasi
    $sel = $conn->prepare("SELECT judul FROM buku WHERE id = ?");
    $sel->bind_param('i', $id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();

    if ($row) {
        $del = $conn->prepare("DELETE FROM buku WHERE id = ?");
        $del->bind_param('i', $id);
        $del->execute();
        $_SESSION['success'] = "Buku \"{$row['judul']}\" berhasil dihapus.";
    }
}

header('Location: ../index.php');
exit;
