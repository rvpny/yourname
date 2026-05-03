<?php
include_once '../../config/koneksi.php';

echo "<pre>";
print_r($_POST);
echo "</pre>";

$judul      = $_POST['judul'];
$isbn       = $_POST['isbn'];
$pengarang  = $_POST['pengarang'];
$penerbit   = $_POST['penerbit'];
$tahun      = $_POST['tahun_terbit'];
$genre      = ($_POST['genre']);
$stok       = $_POST['stok'];
$deskripsi  = $_POST['deskripsi'];
$cover_path = $nama_file_cover; 

// Query INSERT
$query = "INSERT INTO buku (judul, isbn, pengarang, penerbit, tahun_terbit, genre, stok, deskripsi, cover) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ssssssiss", $judul, $isbn, $pengarang, $penerbit, $tahun, $genre, $stok, $deskripsi, $cover_path);

if (mysqli_stmt_execute($stmt)) {
    // Sukses
    header("Location: admin_buku.php?success=1");
    exit;
} else {
    // Gagal
    header("Location: admin_buku_tambah.php?error=db");
    exit;
}
    function validateISBN($isbn) {
        // Hapus semua tanda hubung
        $isbn = str_replace('-', '', $isbn);
        
        // ISBN-10 atau ISBN-13
        if (strlen($isbn) == 10) {
            return preg_match('/^\d{9}[\dX]$/i', $isbn);
        } elseif (strlen($isbn) == 13) {
            return preg_match('/^\d{13}$/', $isbn);
        }
        
        return false;
    }
    
    // Penggunaan:
    if (!empty($isbn) && !validateISBN($isbn)) {
        $errors[] = "Format ISBN tidak valid. Gunakan ISBN-10 atau ISBN-13";
    }
?>