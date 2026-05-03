<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireAdmin();

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

$user_id = $_GET['id'] ?? 0;

if ($user_id > 0) {
    $stmt = $koneksi->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_admins = $row['admin_count'];

    $stmt = $koneksi->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['role'] === 'admin' && $total_admins <= 1) {
        $_SESSION['error_message'] = "Tidak dapat menghapus admin terakhir!";
        header("Location: admin_user.php");
        exit;
    }

    $cekPeminjaman = $koneksi->prepare("SELECT COUNT(*) as jumlah FROM peminjaman WHERE id_user = ?");
    $cekPeminjaman->bind_param("i", $user_id);
    $cekPeminjaman->execute();
    $resultPeminjaman = $cekPeminjaman->get_result();
    $rowPeminjaman = $resultPeminjaman->fetch_assoc();

    if ($rowPeminjaman['jumlah'] > 0) {
        $_SESSION['error_message'] = "Tidak dapat menghapus user karena masih memiliki data peminjaman!";
    } else {
        $stmt = $koneksi->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus user: " . mysqli_error($koneksi);
        }
    }
}

header("Location: admin_user.php");
exit;
?>