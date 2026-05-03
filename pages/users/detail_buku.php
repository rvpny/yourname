<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireUser();

// Ambil ID buku dari URL
$id_buku = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query data buku
$query = "SELECT * FROM buku WHERE id = $id_buku";
$result = mysqli_query($koneksi, $query);
$buku = mysqli_fetch_assoc($result);

// Jika buku tidak ditemukan
if (!$buku) {
    header("Location: ../list.php?error=book_not_found");
    exit;
}

$username = $_SESSION['username'];

$judul = htmlspecialchars($buku['judul']);
$pengarang = htmlspecialchars($buku['pengarang']);
$deskripsi = nl2br(htmlspecialchars($buku['deskripsi'] ?? '-'));
$tahun_terbit = $buku['tahun_terbit'] ?? '-';
$penerbit = htmlspecialchars($buku['penerbit'] ?? '-');
$isbn = htmlspecialchars($buku['isbn'] ?? '-');
$cover_path = '../../uploads/buku/' . htmlspecialchars($buku['cover']);
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar_user.php'; ?>

<main class="main-content">
    <div class="detail-page">
        <div class="main-header">
            <h1 class="h2">Detail Buku</h1>
            <div class="search-container">
                <form action="user_buku.php" method="GET">
                    <input type="text" name="search" placeholder="Cari buku..." class="search-input">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- DETAIL BUKU -->
        <div class="book-detail-container">
            <!-- Bagian Cover Buku -->
            <div class="book-cover-detail">
                <?php if (!empty($buku['cover']) && file_exists($cover_path)): ?>
                    <img src="<?= $cover_path ?>" alt="Cover <?= $judul ?>">
                <?php else: ?>
                    <div class="no-cover-detail">
                        <i class="fas fa-book-open"></i>
                        <span>Cover tidak tersedia</span>
                    </div>
                <?php endif; ?>
            </div>
        
            <!-- Bagian Informasi Buku -->
            <div class="book-info-section">
                <h1><?= $judul ?></h1>
                
                <div class="meta-section">
                    <div class="meta-item">
                        <span class="meta-label">Pengarang:</span>
                        <span class="meta-value"><?= $pengarang ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Penerbit:</span>
                        <span class="meta-value"><?= $penerbit ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Tahun Terbit:</span>
                        <span class="meta-value"><?= $tahun_terbit ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">ISBN:</span>
                        <span class="meta-value"><?= $isbn ?></span>
                    </div>
                </div>

                <div class="book-description">
                    <h3>Deskripsi</h3>
                    <p><?= $deskripsi ?></p>
                </div>

                <div class="book-actions">
                    <button class="btn-pinjam" id="pinjamBuku">
                        <i class="fas fa-bookmark"></i> Pinjam Buku
                    </button>
                </div>        
            </div>
        </div>

        <!-- Rekomendasi Buku Lain -->
        <section class="related-books">
            <h2><i class="fas fa-book"></i> Buku Lainnya</h2>
            <div class="related-book-grid">
                <?php
                    $query_related = "SELECT * FROM buku WHERE id != $id_buku ORDER BY RAND() LIMIT 6";
                    $result_related = mysqli_query($koneksi, $query_related);
                    while($related = mysqli_fetch_assoc($result_related)):
                ?>
                <a href="detail_buku.php?id=<?= $related['id'] ?>" class="related-book-item">
                    <div class="related-book-cover">
                        <?php if (!empty($related['cover'])): ?>
                            <img src="/skul/praktikum_akhir/perpustakaan/uploads/buku/<?= htmlspecialchars($related['cover']) ?>" alt="<?= htmlspecialchars($related['judul']) ?>">
                        <?php else: ?>
                            <div class="related-no-cover">
                                <i class="fas fa-book"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="related-book-info">
                        <h3><?= htmlspecialchars($related['judul']) ?></h3>
                        <p><?= htmlspecialchars($related['pengarang']) ?></p>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <!-- Modal Pinjam Buku -->
    <div class="modal" id="pinjamModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Form Peminjaman</h2>
            <form action="proses_pinjam.php" method="post">
                <input type="hidden" name="id_buku" value="<?= $id_buku ?>">
                
                <div class="form-group">
                    <label>Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali" 
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                           value="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                           required>
                    <small>Maksimal 7 hari</small>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i> Konfirmasi Peminjaman
                </button>
            </form>
        </div>
    </div>
</main>

<?php include '../templates/footer.php'; ?>

<script>
document.getElementById('pinjamBuku').addEventListener('click', function() {
    document.getElementById('pinjamModal').classList.add('active');
});

document.querySelector('.close-modal').addEventListener('click', function() {
    document.getElementById('pinjamModal').classList.remove('active');
});

const today = new Date().toISOString().split('T')[0];
document.querySelector('input[name="tgl_pinjam"]').min = today;
</script>