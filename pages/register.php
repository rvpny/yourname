<?php
session_start();

$errors = [];
$success = false;
// Pesan error handling
$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'exists':
            $error_message = '<div class="alert-danger">Username/email sudah terdaftar</div>';
            break;
        case 'mismatch':
            $error_message = '<div class="alert-danger">Password tidak cocok</div>';
            break;
        case 'empty':
            $error_message = '<div class="alert-danger">Semua kolom wajib diisi</div>';
            break;
        default:
            $error_message = '<div class="alert-info">Silakan isi form pendaftaran</div>';
            break;
    }
}
if ($error_message) {
    $errors[] = $error_message;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Perpustakaan Bilgi Evi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <!-- Bagian Gambar Buku -->
        <div class="book-side">
            <div class="book-overlay"></div>
            <div class="book-content">
                <h1>Bergabung dengan Bilgi Evi</h1>
                <p>Dapatkan akses ke ribuan koleksi buku kami</p>
                <div class="book-grid">
                    <div class="book"></div>
                    <div class="book"></div>
                    <div class="book"></div>
                    <div class="book"></div>
                </div>
            </div>
        </div>
        
        <!-- Bagian Form Register -->
        <div class="form-side">
            <div class="form-container">
                <a href="../index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
                
                <h2>Buat Akun Baru</h2>
                <p class="subtitle">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                
                <?php if ($success): ?>
                    <div class="alert success">
                        Registrasi berhasil! Silakan login.
                    </div>
                <?php endif; ?>

                <?php echo $error_message; ?>
                
                <form action="../config/proses_register.php" method="post">
                    <div class="input-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan email">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Buat username">
                        <i class="fas fa-at input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Buat password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    
                    <div class="input-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    
                    <button type="submit" class="login-btn" name="register" id="registerBtn">
                        <i class="fas fa-user-plus"></i> Daftar
                    </button>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>