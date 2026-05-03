<?php 
include_once '../../config/auth.php';
requireUser();

include_once '../../config/koneksi.php';
include '../templates/header.php';

// Handle status messages
$message = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $msg = $_GET['msg'] ?? '';
    $msg = htmlspecialchars(urldecode($msg));

    if ($status == 'success') {
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        $message .= 'Aksi berhasil: ' . $msg;
        $message .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } elseif ($status == 'error') {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $message .= 'Terjadi kesalahan: ' . $msg;
        $message .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}

// Handle search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "WHERE stok > 0";

if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $where .= " AND (judul LIKE '%$search%' 
                OR pengarang LIKE '%$search%' 
                OR penerbit LIKE '%$search%' 
                OR genre LIKE '%$search%')";
}

// Get books data with cover information
$sql = "SELECT id, judul, isbn, pengarang, penerbit, tahun_terbit, genre, stok, cover 
        FROM buku $where 
        ORDER BY judul ASC";
$result = mysqli_query($koneksi, $sql);

$books = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
}

$total_books = count($books);

include '../templates/sidebar_user.php';
?>
<!-- Main Content -->
<main class="main-content">
    <div class="main-header">
        <h1 class="h2">Daftar Buku Tersedia</h1>
        <div class="search-bar">
        <form action="user_buku.php" method="GET">            
            <input type="text" name="search" placeholder="Cari buku..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"> <i class="fas fa-search"></i>
            </button>
            <?php if (!empty($search)): ?>
                <a href="user_buku.php" class="clear-search">Clear</a>
            <?php endif; ?>
        </form>
        </div>
    </div>

    <?= $message ?>

    <div class="total-books">
        <span class="badge">Total: <?= $total_books ?> Buku</span>
        <?php if (!empty($search)): ?>
            <span class="search-info">Hasil pencarian untuk: "<?= htmlspecialchars($search) ?>"</span>
        <?php endif; ?>
    </div>

    <div class="modern-book-list">
        <?php if (!empty($books)): ?>
            <div class="modern-book-grid">
                <?php foreach ($books as $book): ?>
                    <div class="modern-book-card">
                        <a href="detail_buku.php?id=<?= $book['id'] ?>" class="modern-book-cover">
                            <?php 
                            $cover_path = '../../uploads/buku/' . htmlspecialchars($book['cover']);
                            if (!empty($book['cover']) && file_exists($cover_path)): 
                            ?>
                                <img src="<?= $cover_path ?>" 
                                        alt="Cover <?= htmlspecialchars($book['judul']) ?>" 
                                        class="modern-book-img">
                            <?php else: ?>
                                <div class="modern-no-cover">
                                    <i class="fas fa-book"></i>
                                    <span>No Cover</span>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="modern-book-info">
                            <h3><?= htmlspecialchars($book['judul']) ?></h3>
                            <p class="author"><?= htmlspecialchars($book['pengarang']) ?></p>
                            <div class="modern-book-actions">
                                <a href="detail_buku.php?id=<?= $book['id'] ?>" class="modern-btn-detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <form action="proses_pinjam.php" method="POST">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <button type="submit" class="modern-btn-pinjam">
                                        <i class="fas fa-bookmark"></i> Pinjam
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3><?= empty($search) ? 'Tidak ada buku yang tersedia' : 'Tidak ada buku yang ditemukan' ?></h3>
                <p><?= empty($search) ? 'Silahkan coba lagi nanti' : 'Coba dengan kata kunci lain' ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>


<?php include '../templates/footer.php'; ?>