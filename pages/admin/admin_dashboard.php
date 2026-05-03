<?php
session_start();
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
requireAdmin(); 

$query_buku = "SELECT COUNT(*) as total FROM buku";
$query_user = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$query_peminjaman = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Dipinjam'";

$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, $query_buku))['total'];
$total_user = mysqli_fetch_assoc(mysqli_query($koneksi, $query_user))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($koneksi, $query_peminjaman))['total'];

$page_title = "Dashboard Admin - Perpustakaan Bilgi Evi";
$active_page = "dashboard";

$username = $_SESSION['username'];

function waktu_lalu($timestamp) {
    $selisih = time() - strtotime($timestamp);
    
    if ($selisih < 60) {
        return 'beberapa detik yang lalu';
    } elseif ($selisih < 3600) {
        $menit = floor($selisih / 60);
        return $menit . ' menit yang lalu';
    } elseif ($selisih < 86400) {
        $jam = floor($selisih / 3600);
        return $jam . ' jam yang lalu';
    } elseif ($selisih < 2592000) {
        $hari = floor($selisih / 86400);
        return $hari . ' hari yang lalu';
    } else {
        return date('d M Y', strtotime($timestamp));
    }
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1>Dashboard Admin</h1>
                <div class="admin-actions">
                    <button class="btn btn-primary" onclick="location.href='admin_buku_tambah.php?action=add'">
                        <i class="fas fa-plus"></i> Tambah Buku
                    </button>
                </div>
            </header>

            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-book"></i> Total Buku</h3>
                    </div>
                    <div class="card-body">
                        <p class="count"><?= $total_buku; ?></p>
                        <a href="admin_buku.php" class="btn">Kelola Buku</a>
                    </div>
                </div>

                <!-- Card: Total User -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Total User</h3>
                    </div>
                    <div class="card-body">
                        <p class="count"><?= $total_user; ?></p>
                        <a href="admin_user.php" class="btn">Kelola User</a>
                    </div>
                </div>

                <!-- Card: Peminjaman Aktif -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-book-open"></i> Peminjaman Aktif</h3>
                    </div>
                    <div class="card-body">
                        <p class="count"><?= $total_peminjaman; ?></p>
                        <a href="admin_peminjaman.php" class="btn">Lihat Detail</a>
                    </div>
                </div>
            </div>

            <!-- Section: Aktivitas Terkini -->
            <section class="section">
                <h2><i class="fas fa-clock"></i> Aktivitas Terkini</h2>
                <div class="activity-list">
                    <?php
                    $query_aktivitas = "SELECT * FROM aktivitas ORDER BY waktu DESC LIMIT 5";
                    $result_aktivitas = mysqli_query($koneksi, $query_aktivitas);

                    while ($aktivitas = mysqli_fetch_assoc($result_aktivitas)):
                        $icon = '';
                        $jenis = '';

                        if(strpos($aktivitas['aktivitas'], 'buku') !== false) {
                            $icon = 'fa-book';
                            $jenis = 'Buku baru';
                        } elseif(strpos($aktivitas['aktivitas'], 'user') !== false) {
                            $icon = 'fa-user-plus';
                            $jenis = 'User baru';
                        } elseif(strpos($aktivitas['aktivitas'], 'peminjaman') !== false) {
                            $icon = 'fa-exchange-alt';
                            $jenis = 'Peminjaman';
                        }
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong><?= $jenis ?></strong><?= $aktivitas['aktivitas'] ?></p>
                            <small><?= waktu_lalu($aktivitas['waktu']) ?></small>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
  
<?php include '../templates/footer.php'; ?>