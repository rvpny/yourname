<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireUser();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = (int)$_POST['id_buku'];
    $id_user = $_SESSION['user_id'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Validasi
    if (empty($id_buku)) {
        $_SESSION['error'] = "Buku tidak valid";
        header("Location: detail_buku.php?id=$id_buku");
        exit;
    }

    $cek_pinjam = mysqli_query($koneksi, "SELECT id FROM peminjaman 
    WHERE id_user = $id_user 
    AND id_buku = $id_buku 
    AND status = 'Dipinjam'");
    if (mysqli_num_rows($cek_pinjam) > 0) {
    $_SESSION['error'] = "Anda sudah meminjam buku ini dan belum dikembalikan";
    header("Location: detail_buku.php?id=$id_buku");
    exit;
    }

    // Cek stok buku
    $cek_stok = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id = $id_buku");
    $stok = mysqli_fetch_assoc($cek_stok)['stok'];

    if ($stok < 1) {
        $_SESSION['error'] = "Buku sedang tidak tersedia";
        header("Location: detail_buku.php?id=$id_buku");
        exit;
    }

    $cek_jumlah = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE id_user = $id_user AND status = 'Dipinjam'");
    $jumlah = mysqli_fetch_assoc($cek_jumlah)['total'];

    if ($jumlah >= 3) {
        $_SESSION['error'] = "Anda sudah meminjam 3 buku. Kembalikan buku terlebih dahulu untuk meminjam lagi.";
        header("Location: detail_buku.php?id=$id_buku");
        exit;
    }

    $tanggal_pinjam = date('Y-m-d');
    $tanggal_harus_kembali = date('Y-m-d', strtotime('+7 days'));
    // Proses peminjaman
    $query = "INSERT INTO peminjaman 
              (id_buku, id_user, tanggal_pinjam, tanggal_harus_kembali, status) 
              VALUES ($id_buku, $id_user, '$tanggal_pinjam', '$tanggal_harus_kembali', 'Dipinjam')";

    if (mysqli_query($koneksi, $query)) {
        mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");
        
        $_SESSION['success'] = "Buku berhasil dipinjam";
        header("Location: pinjaman_user.php");
    } else {
        $_SESSION['error'] = "Gagal meminjam buku: " . mysqli_error($koneksi);
        header("Location: detail_buku.php?id=$id_buku");
    }
} else {
    header("Location: user_dashboard.php");
}
?>