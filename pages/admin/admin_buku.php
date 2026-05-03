<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/auth.php';
requireAdmin();

$query = "SELECT * FROM buku";
$result = mysqli_query($koneksi, $query);

$books = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
}

$page_title = "Manajemen Buku - Perpustakaan Bilgi Evi";
$active_page = "buku";
$additional_css = '
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .dataTables_wrapper {
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        }
        .book-cover {
            width: 50px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        .btn-edit {
            background: var(--vista-blue);
        }
        .btn-delete {
            background: #ff758c;
        }
        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .text-center {
            text-align: center;
        }
    </style>
';
$additional_js = '
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
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
        <h1><i class="fas fa-book"></i> Manajemen Buku</h1>
        <div class="admin-actions">
            <a href="admin_buku_tambah.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Buku
            </a>
        </div>
    </header>
    
    <?php
    if (!empty($book_success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($book_success_message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($book_error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($book_error_message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="content-section">
        <table id="booksTable" class="display">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>ISBN</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Genre</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($books)): ?>
                    <?php $no = 1; foreach ($books as $book): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="book-cover-container">
                                <?php if (!empty($book['cover'])): ?>
                                    <img src="../../uploads/buku/<?= htmlspecialchars($book['cover']) ?>" class="book-cover" alt="Cover <?= htmlspecialchars($book['judul']) ?>" loading="lazy" style="max-width: 60px; max-height: 80px;">
                                <?php else: ?>
                                    <div class="book-cover-placeholder">
                                        <i class="fas fa-book"></i>
                                        <span>No Cover</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($book['judul']) ?></td>
                        <td><?= htmlspecialchars($book['isbn'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($book['pengarang'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($book['penerbit'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($book['tahun_terbit'] ?? '-') ?></td>
                        <td>
                            <?php
                            $genre_display = htmlspecialchars($book['genre']);
                            ?>
                            <span class="badge badge-info"><?= $genre_display ?></span>
                        </td>
                        <td><?= htmlspecialchars($book['stok'] ?? '0') ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="admin_buku_edit.php?id=<?= $book['id'] ?>" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="admin_buku_hapus.php?id=<?= $book['id'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus buku ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Saat ini tidak ada buku yang tersedia untuk dipinjam.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
</main>

<script>
$(document).ready(function() {
    // Handle close button
    $(".alert .close").click(function() {
        $(this).closest(".alert").fadeOut("slow", function() {
            $(this).remove();
        });
    });
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $(".alert").fadeOut("slow", function() {
            $(this).remove();
        });
    }, 5000);
});
</script>

<?php include '../templates/footer.php'; ?>