<?php
session_start();
require_once '../config/koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $confirm_password = mysqli_real_escape_string($koneksi, $_POST['confirm_password']);

    // Validasi data
    $errors = [];

    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email) || empty($confirm_password)) {
        $errors[] = "Semua kolom wajib diisi";
    }
    
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }
    
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }

    // Cek username/email sudah ada
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($koneksi, $check_query);
    $sql = "SELECT id, username, password, role FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Username atau email sudah terdaftar";
    }

    // Jika tidak ada error, proses registrasi
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // Default role
        
        $insert_query = "INSERT INTO users (username, password, nama_lengkap, email, role) 
                         VALUES ('$username', '$hashed_password', '$nama_lengkap', '$email', '$role')";
        
        if (mysqli_query($koneksi, $insert_query)) {
            $_SESSION['registration_success'] = true;
            header("Location: ../pages/login.php");
            exit();
        } else {
            $errors[] = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    }
    
    // Jika ada error, simpan di session dan redirect kembali
    $_SESSION['registration_errors'] = $errors;
    header("Location: ../pages/register.php");
    exit();
}
?>