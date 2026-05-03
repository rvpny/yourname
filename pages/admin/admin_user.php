<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireAdmin();

$query = "SELECT * FROM users";
$result = mysqli_query($koneksi, $query);

$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

$user_success_message = $_SESSION['user_success_message'] ?? null;
$user_error_message = $_SESSION['user_error_message'] ?? null;
clearNotificationSession();

$page_title = "Manajemen User - Perpustakaan Bilgi Evi";
$active_page = "users";
$additional_css = '
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    .dataTables_wrapper {
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.05);
    }
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
    }
    .btn-delete {
        background: #ff758c;
    }
    .btn-action:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    
    .alert {
        position: relative;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.375rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .alert-success {
        color: #0f5132;
        background-color: #d1e7dd;
        border-color: #badbcc;
    }
    
    .alert-danger {
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }
    
    .alert-dismissible {
        padding-right: 3.5rem;
    }
    
    .close {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1rem 1.5rem;
        color: inherit;
        background: transparent;
        border: 0;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        opacity: 0.5;
        cursor: pointer;
    }
    
    .close:hover {
        opacity: 1;
    }
    
    .fade {
        transition: opacity 0.15s linear;
    }
    
    .show {
        opacity: 1;
    }
</style>
';
$additional_js = '
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    
<script>
$(document).ready(function() {
    $("#userTable").DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json",
            emptyTable: "Tidak ada data user yang tersedia"
        },
    });
    
    // Fungsi untuk menutup alert
    $(".alert .close").click(function() {
        $(this).closest(".alert").fadeOut("slow", function() {
            $(this).remove();
        });
    });
    
    // Auto-hide alert setelah 5 detik
    setTimeout(function() {
        $(".alert").fadeOut("slow", function() {
            $(this).remove();
        });
    }, 5000);
});
</script>
';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit;
}
?>

<?php
 include '../templates/header.php';
 include '../templates/sidebar_admin.php';
?>

<main class="main-content">
    <header class="main-header">
        <h1><i class="fas fa-user"></i> Manajemen User</h1>
        <div class="admin-actions">
            <a href="admin_user_tambah.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        </div>
    </header>

    <?php
     if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" id="successAlert" role="alert">
            <?= htmlspecialchars($success_message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" id="errorAlert" role="alert">
            <?= htmlspecialchars($error_message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- main content -->
        <div class="content-section">
            <table id="userTable" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!empty($users)): ?>
                    <?php $no = 1; foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']?? '-') ?></td>
                            <td><?= htmlspecialchars($user['email']?? '-') ?></td>
                            <td><?= htmlspecialchars($user['nama_lengkap']?? '-') ?></td>
                            <td><?= htmlspecialchars($user['role']?? '-') ?></td>
                            <td><?= htmlspecialchars($user['created_at']?? '-') ?></td>
                            <td>
                                <div class="action-buttons">
                                <a href="admin_user_edit.php?id=<?= $user['id'] ?>" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i></a>
                                <a href="admin_user_hapus.php?id=<?= $user['id'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus user ini?');">
                                    <i class="fas fa-trash"></i></a>
                                </div>
                            </td>    
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada user yang mendaftar.</td>
                            </tr>
                        <?php endif; ?>
                </tbody>
            </table>
        </div>
</main>

<script>
    $(document).ready(function() {
    $('#userTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json',
            emptyTable: "Tidak ada data user yang tersedia"
        },
    });
});

setTimeout(function() {
        $('#successAlert').fadeOut('slow');
        $('#errorAlert').fadeOut('slow');
    }, 5000);
    
    $('#successAlert, #errorAlert').click(function() {
        $(this).fadeOut('slow');
    });
    
    $('#userTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json',
            emptyTable: "Tidak ada data user yang tersedia"
        },
    });
</script>
<?php include '../templates/footer.php'; ?>