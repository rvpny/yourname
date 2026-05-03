<?php
include_once '../config/koneksi.php';
include_once '../config/auth.php'; // Untuk memulai session

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Jika bukan POST, redirect ke halaman login
    header("Location: ../pages/login.php");
    exit;
}

// Ambil data dari form
$username = $_POST["username"] ?? '';
$password = $_POST["password"] ?? '';

// Validasi dasar: username dan password tidak boleh kosong
if (empty($username) || empty($password)) {
    header("Location: ../pages/login.php?error=empty");
    exit;
}

// Escape input untuk keamanan (meskipun kita akan pakai prepared statement)
$username = mysqli_real_escape_string($koneksi, $username);

// Cari user berdasarkan username menggunakan prepared statement
$sql = "SELECT id, username, password, role FROM users WHERE username = ? OR email = ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        // User ditemukan, ambil datanya
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user["password"])) {
            // Password cocok, login berhasil
            // Simpan data user ke session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_role"] = $user["role"];
            $_SESSION['login'] = true;
            $_SESSION['role'] = $user['role'];

            $_SESSION['login_success'] = true;

            // Redirect ke dashboard sesuai role
            if ($user["role"] === "admin") {
                header("Location: ../pages/admin/admin_dashboard.php");
            } else {
                header("Location: ../pages/users/user_dashboard.php");
            }
            exit;
        } else {
            // Password salah
            header("Location: ../pages/login.php?error=credentials");
            exit;
        }
    } else {
        // User tidak ditemukan
        header("Location: ../pages/login.php?error=credentials");
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: ../pages/login.php?error=dberror");
    exit;
}

mysqli_close($koneksi);

?>
