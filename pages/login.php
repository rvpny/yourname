<?php
session_start();
include_once '../config/auth.php';
include_once '../config/koneksi.php';

$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'unauthorized':
            $error_message = '<div class="alert-warning">Anda harus login untuk mengakses halaman tersebut.</div>';
            break;
        case 'forbidden':
            $error_message = '<div class=" alert-danger">Anda tidak memiliki hak akses ke halaman tersebut.</div>';
            break;
        case 'credentials':
            $error_message = '<div class=" alert-danger"><i class="fas fa-exclamation-triangle"></i>Username atau password salah.</div>';
            break;
        case 'empty':
            $error_message = '<div class=" alert-danger"><i class="fas fa-exclamation-triangle"></i>Username dan password wajib diisi.</div>';
            break;
        case 'dberror':
             $error_message = '<div class="alert-danger">Terjadi masalah pada database. Silakan coba lagi nanti.</div>';
             break;
        case 'method':
            $error_message = '<div class="alert-danger">Metode request tidak valid</div>';
            break;
        default:
            $error_message = '<div class="alert-warning"><i class="fas fa-exclamation-triangle"></i>Silakan login untuk melanjutkan.</div>';
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Perpustakaan Bilgi Evi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-page">
    <div class="login-container">
        <div class="book-side">
            <div class="book-overlay"></div>
            <div class="book-content">
                <h1>Selamat Datang di Bilgi Evi</h1>
                <p>Jelajahi ribuan koleksi buku kami.</p>
                <div class="book-grid">                
                    <div class="book"><i class="fas fa-book-reader"></i></div>
                    <div class="book"><i class="fas fa-journal-whills"></i></div>
                    <div class="book"><i class="fas fa-atlas"></i></div>
                    <div class="book"><i class="fas fa-scroll"></i></div>
                </div>
            </div>
        </div>

        <!-- Form Login -->
        <div class="form-side">
            <div class="form-container">
                <a href="../index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
                
                <h2>Masuk ke Akun Anda</h2>
                <p class="subtitle">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                
                <?php echo $error_message; ?>
                
                <form action="../config/proses_login.php" method="post" id="loginForm">
                    <div class="input-group">
                        <label for="username">Username atau Email</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan username/email" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    
                    <button type="submit" class="login-btn" id="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    
                    <div class="divider">atau</div>
                    
                    <a href="reset.php" class="forgot-link">Lupa password?</a>
                </form>
            </div>
        </div>
    </div>
<script src="../assets/js/main.js"></script>
</body>
</html>