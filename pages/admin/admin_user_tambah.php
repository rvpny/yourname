<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireAdmin();

$errors = [];
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

$page_title = "Tambah User - Perpustakaan";
$active_page = "users";
$additional_css = '
<style>
    .user-form {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.05);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    .form-row .form-group {
        flex: 1;
    }
    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }
        
    .alert-danger {
        background: #ffebee;
        border-left: 4px solid #ff4444;
        padding: 15px;
        margin-bottom: 20px;
        color: #ff4444;
    }
    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }
    .btn-primary, btn-secondary{
        background: linear-gradient(135deg, #9DB2CA, #495F86);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-reset {
        background: #f8f9fa;
        color: #333;
        border: 1px solid #ddd;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
</style>
';

// Proses form tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $email = trim($_POST['email']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];

    // Validasi
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } elseif (strlen($username) < 5) {
        $errors[] = "Username minimal 5 karakter";
    }

    if (empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    } elseif ($password !== $password_confirm) {
        $errors[] = "Konfirmasi password tidak sesuai";
    }

    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (empty($errors)) {
        // Cek username dan email sudah ada atau belum
        $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username atau email sudah terdaftar";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan ke database
            $stmt = $koneksi->prepare("INSERT INTO users (username, password, email, nama_lengkap, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $hashed_password, $email, $nama_lengkap, $role);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User berhasil ditambahkan!";
                header("Location: admin_user.php");
                exit;
            } else {
                $errors[] = "Gagal menambahkan user: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar_admin.php'; ?>

<main class="main-content">
    <header class="main-header">
        <h1><i class="fas fa-user-plus"></i> Tambah User Baru</h1>
        <a href="admin_user.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </header>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="admin_user_tambah.php" method="post" class="user-form">
        <div class="form-row">
            <div class="form-group">
                <label for="username">Username*</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password*</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Konfirmasi Password*</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control">
            </div>
            <div class="form-group">
                <label for="role">Role*</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="user" selected>User</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <button type="reset" class="btn btn-reset">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
    </form>
</main>

<?php include '../templates/footer.php'; ?>