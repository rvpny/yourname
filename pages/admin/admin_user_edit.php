<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireAdmin();

$errors = [];
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

$user_id = $_GET['id'] ?? 0;
$user = null;

// Ambil data user yang akan diedit
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_user.php?error=user_not_found");
    exit;
}

$user = $result->fetch_assoc();

// Proses form edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];
    $change_password = !empty($_POST['password']);

    // Validasi
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } elseif (strlen($username) < 5) {
        $errors[] = "Username minimal 5 karakter";
    }

    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if ($change_password) {
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if (strlen($password) < 6) {
            $errors[] = "Password minimal 6 karakter";
        } elseif ($password !== $password_confirm) {
            $errors[] = "Konfirmasi password tidak sesuai";
        }
    }

    if (empty($errors)) {
        // Cek username dan email sudah ada atau belum (kecuali untuk user ini)
        $stmt = $koneksi->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username atau email sudah digunakan oleh user lain";
        } else {
            // Update database
            if ($change_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET username = ?, email = ?, nama_lengkap = ?, role = ?, password = ? WHERE id = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("sssssi", $username, $email, $nama_lengkap, $role, $hashed_password, $user_id);
            } else {
                $query = "UPDATE users SET username = ?, email = ?, nama_lengkap = ?, role = ? WHERE id = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("ssssi", $username, $email, $nama_lengkap, $role, $user_id);
            }

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data user berhasil diperbarui!";
                header("Location: admin_user.php");
                exit;
            } else {
                $errors[] = "Gagal memperbarui user: " . mysqli_error($koneksi);
            }
        }
    }
}

$page_title = "Edit User - Perpustakaan";
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
    .password-toggle {
        cursor: pointer;
        color: #495F86;
        font-size: 0.9rem;
    }
</style>
';

$_SESSION['user_success_message'] = "Pesan sukses user";
$_SESSION['user_error_message'] = "Pesan error user";

?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar_admin.php'; ?>

<main class="main-content">
    <header class="main-header">
        <h1><i class="fas fa-user-edit"></i> Edit User: <?= htmlspecialchars($user['username']) ?></h1>
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

    <form action="admin_user_edit.php?id=<?= $user_id ?>" method="post" class="user-form">
        <div class="form-row">
            <div class="form-group">
                <label for="username">Username*</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                       value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="role">Role*</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="ubah-password"> Ubah Password</label>
                <div class="form-row">
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control">
                </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <button type="reset" class="btn btn-reset">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
    </form>
</main>

<script>
$(document).ready(function() {
    // Sembunyikan notifikasi setelah 5 detik
    setTimeout(function() {
        $('#successAlert').fadeOut('slow');
        $('#errorAlert').fadeOut('slow');
    }, 5000);
    
    // Juga sembunyikan saat diklik
    $('#successAlert, #errorAlert').click(function() {
        $(this).fadeOut('slow');
    });
    
    // DataTable initialization
    $('#userTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json',
            emptyTable: "Tidak ada data user yang tersedia"
        },
    });
});
</script>

<?php include '../templates/footer.php'; ?>