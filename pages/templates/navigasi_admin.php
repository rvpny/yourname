<nav class="sidebar-nav">
    <ul>
        <li>
            <a href="../../index.php"><i class="fa fa-home"></i>Kembali ke Beranda</a>
        </li>
        <li class="<?= (basename($_SERVER['SCRIPT_NAME']) == 'admin_dashboard.php' ? 'active' : '') ?>">
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?= (basename($_SERVER['SCRIPT_NAME']) == 'admin_buku.php' ? 'active' : '') ?>">
            <a href="admin_buku.php"><i class="fas fa-book"></i> Manajemen Buku</a>
        </li>
        <li class="<?= (basename($_SERVER['SCRIPT_NAME']) == 'admin_user.php' ? 'active' : '') ?>">
            <a href="admin_user.php"><i class="fas fa-users"></i> Manajemen User</a>
        </li>
        <li class="<?= (basename($_SERVER['SCRIPT_NAME']) == 'admin_peminjaman.php' ? 'active' : '') ?>">
            <a href="admin_peminjaman.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        </li>
        <li>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</nav>