<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
require_once '../../config/database.php';
requireUser();

echo '<script>';
if (isset($_SESSION['login_success'])) {
    echo 'const loginSuccess = true;';
    echo 'const username = "' . htmlspecialchars($_SESSION['username']) . '";';
    unset($_SESSION['login_success']);
} else {
    echo 'const loginSuccess = false;';
}
echo '</script>';

$id_user = $_SESSION['user_id'];
$query_rekomendasi = "SELECT * FROM buku 
    WHERE stok > 0 
    AND id NOT IN (
        SELECT id_buku FROM peminjaman 
        WHERE id_user = $id_user AND status = 'Dipinjam'
    )
    ORDER BY RAND() LIMIT 3";
$result_rekomendasi = mysqli_query($koneksi, $query_rekomendasi);

$query_buku_terbaru = "SELECT * FROM buku ORDER BY id DESC LIMIT 5";
$result_buku_terbaru = mysqli_query($koneksi, $query_buku_terbaru);

$username = $_SESSION['username'];

$query_active = "SELECT COUNT(*) as total FROM peminjaman 
                WHERE id_user = {$_SESSION['user_id']} 
                AND status = 'Dipinjam'";
$result_active = mysqli_query($koneksi, $query_active);
$active_count = mysqli_fetch_assoc($result_active)['total'];

// Hitung total buku yang pernah dipinjam
$query_history = "SELECT COUNT(*) as total FROM peminjaman 
                 WHERE id_user = {$_SESSION['user_id']} 
                 AND status = 'Dikembalikan'";
$result_history = mysqli_query($koneksi, $query_history);
$history_count = mysqli_fetch_assoc($result_history)['total'];
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar_user.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1>Dashboard User</h1>
                <div class="search-bar">
                    <form action="user_buku.php" method="get">
                        <input type="text" name="search" placeholder="Cari buku...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search)): ?>
                            <a href="user_buku.php" class="clear-search">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </header>

            <div class="content-grid">
                <!-- Card: Buku Sedang Dipinjam -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-book-open"></i> Sedang Dipinjam</h3>
                    </div>
                    <div class="card-body">
                        <p class="count"><?= $active_count; ?></p>
                        <a href="pinjaman_user.php" class="btn">Lihat Detail</a>
                    </div>
                </div>

                <!-- Card: Total Buku Dibaca -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bookmark"></i> Total Dibaca</h3>
                    </div>
                    <div class="card-body">
                        <p class="count"><?= $history_count ?></p>
                        <a href="pinjaman_user.php" class="btn">Lihat Riwayat</a>
                    </div>
                </div>

                <!-- Card: Rekomendasi Buku -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-star"></i> Rekomendasi</h3>
                    </div>
                    <div class="card-body">
                        <ul class="book-list">
                            <li><a href="detail_buku.php?id=10">Materi Dasar Islam</a></li>
                            <li><a href="detail_buku.php?id=9">Udah Putusin Aja!</a></li>
                            <li><a href="detail_buku.php?id=13">Negeri Para Bedebah</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section: Buku Terbaru -->
            <section class="section">
                <h2><i class="fas fa-clock"></i> Buku Terbaru</h2>
                <div class="book-grid">
                    <?php while($buku = mysqli_fetch_assoc($result_buku_terbaru)): ?>
                    <div class="book-item">
                        <div class="book-cover">
                            <?php if (!empty($buku['cover'])): ?>
                                <img src="http://localhost/skul/praktikum_akhir/perpustakaan/uploads/buku/<?= htmlspecialchars($buku['cover']) ?>" alt="Cover <?= htmlspecialchars($buku['judul']) ?>">
                            <?php else: ?>
                                <div class="no-cover">
                                    <i class="fas fa-book"></i>
                                    <small>Cover tidak tersedia</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3><?= htmlspecialchars($buku['judul']) ?></h3>
                            <p><?= htmlspecialchars($buku['pengarang']) ?></p>
                            <a href="detail_buku.php?id=<?= $buku['id'] ?>" class="btn">Detail Buku</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/user.js"></script>

<?php include '../templates/footer.php'; ?>