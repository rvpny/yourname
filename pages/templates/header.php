<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Perpustakaan Bilgi Evi') ?></title>
    <link rel="stylesheet" href="/skul/praktikum_akhir/perpustakaan/assets/css/<?= $role ?? 'admin' ?>.css">
    <link rel="stylesheet" href="/skul/praktikum_akhir/perpustakaan/assets/css/<?= $role ?? 'user' ?>.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= $role ?? 'user' ?>-page">
    <div class="dashboard-container">
        <?php if (isset($show_sidebar) && $show_sidebar): ?>
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>BILGI EVI</h3>
                <p><?= isset($username) ? 'Selamat datang, ' . htmlspecialchars($username) : '&nbsp;' ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <?php if ($role === 'admin'): ?>
                        <li class="<?= $active_page === 'dashboard' ? 'active' : '' ?>">
                            <a href="../admin/admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="<?= $active_page === 'books' ? 'active' : '' ?>">
                            <a href="../admin/admin_buku.php"><i class="fas fa-book"></i> Manajemen Buku</a>
                        </li>
                        <li class="<?= $active_page === 'users' ? 'active' : '' ?>">
                            <a href="../admin/admin_users.php"><i class="fas fa-users"></i> Manajemen User</a>
                        </li>
                        <li class="<?= $active_page === 'loans' ? 'active' : '' ?>">
                            <a href="../admin/admin_peminjaman.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
                        </li>
                    <?php else: ?>
                        <li class="<?= $active_page === 'dashboard' ? 'active' : '' ?>">
                            <a href="../users/user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                        </li>
                        <li class="<?= $active_page === 'books' ? 'active' : '' ?>">
                            <a href="../users/user_buku.php"><i class="fas fa-book"></i> Daftar Buku</a>
                        </li>
                        <li class="<?= $active_page === 'loans' ? 'active' : '' ?>">
                            <a href="../users/user_pinjaman.php"><i class="fas fa-book-open"></i> Pinjaman</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>
        <?php endif; ?>