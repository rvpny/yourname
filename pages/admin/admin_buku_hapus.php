<?php

include '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    //ambil nama file foto
    $result = mysqli_query($koneksi, "SELECT cover FROM buku WHERE id='$id'");

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $foto = $row['foto'];

        //Hapus file foto kalo ada
        if (!empty($foto) && file_exists("../../assets/image/buku/" . $foto)) {
            unlink("../assets/iamge/buku/" . $foto);
        }

        $delete = mysqli_query($koneksi, "DELETE FROM buku WHERE id='$id'");

        if ($delete) {
            $_SESSION['success_message'] = "Buku berhasil dihapus";
            header("Location: admin_buku.php");
            exit();
        }else {
            die("GAGAL menghapus data:" . mysqli_error($koneksi));
        }
    } else {
        die("Data tidak ditemukan");
    }
}

$_SESSION['book_success_message'] = "Pesan sukses buku";
$_SESSION['book_error_message'] = "Pesan error buku";

// Redirect
header("Location: admin_buku.php");
exit;
?>