<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireUser();

if (isset($_GET['id'])) {
    $id_peminjaman = (int)$_GET['id'];
    $id_user = $_SESSION['user_id'];
    
    // Ambil data peminjaman
    $query = "SELECT p.*, b.id as book_id 
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id
              WHERE p.id = $id_peminjaman AND p.id_user = $id_user";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['error'] = "Data peminjaman tidak ditemukan";
        header("Location: pinjaman_user.php");
        exit;
    }
    
    $peminjaman = mysqli_fetch_assoc($result);
    
    $status = 'Dikembalikan';
    $tgl_kembali = date('Y-m-d');
    
    // Update peminjaman
    $update_query = "UPDATE peminjaman SET 
                    status = '$status',
                    tanggal_kembali = '$tgl_kembali'
                    WHERE id = $id_peminjaman";
    
    if (mysqli_query($koneksi, $update_query)) {
        // Update stok buku
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id = {$peminjaman['id_buku']}");
        
        $_SESSION['success'] = "Buku berhasil dikembalikan";
    } else {
        $_SESSION['error'] = "Gagal mengembalikan buku: " . mysqli_error($koneksi);
    }
}

header("Location: pinjaman_user.php");
?>