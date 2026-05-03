<?php
$current_file = basename($_SERVER['PHP_SELF']);

$active_page = '';
if (strpos($current_file, 'dashboard') !== false) {
    $active_page = 'dashboard';
} elseif (strpos($current_file, 'buku') !== false || strpos($current_file, 'detail') !== false) {
    $active_page = 'buku';
} elseif (strpos($current_file, 'pinjaman') !== false) {
    $active_page = 'pinjaman';
}
?>

<nav class="sidebar-nav">
    <ul>
        <li>
            <a href="../../index.php"><i class="fa fa-home"></i>Kembali ke Beranda</a>
        </li>
        <li class="<?= (isset($active_page) && $active_page === 'dashboard') || !isset($active_page) ? 'active' : '' ?>">
            <a href="user_dashboard.php"><i class="fas fa-user-circle"></i> Dashboard</a>
        </li>
        <li class="<?= (isset($active_page) && $active_page === 'buku') || !isset($active_page) ? 'active' : '' ?>">
            <a href="user_buku.php"><i class="fas fa-book"></i> Daftar Buku</a>
        </li>
        <li class="<?= (isset($active_page) && $active_page === 'pinjaman') || !isset($active_page) ? 'active' : '' ?>">
            <a href="pinjaman_user.php"><i class="fas fa-book-open"></i> Peminjaman</a>
        </li>
        <li>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</nav>