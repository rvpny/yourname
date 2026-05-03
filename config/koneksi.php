<?php

$host= "localhost";
$user= "root";
$pass= "";
$db= "perpustakaan_rava";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if(!$koneksi){
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "SELECT * FROM users"; 
$result = mysqli_query($koneksi, $query);

if(!$result){
    die("Query gagal: " . mysqli_error($koneksi));
}

function getPopularBooks($limit = 6) {
    global $koneksi;
    $query = "SELECT b.*, COUNT(p.id) as pinjam_count 
              FROM buku b
              LEFT JOIN peminjaman p ON b.id = p.id_buku
              WHERE b.stok > 0
              GROUP BY b.id
              ORDER BY pinjam_count DESC, b.stok DESC
              LIMIT $limit";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>