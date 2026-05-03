<?php
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
require_once '../../config/database.php';
require_once '../templates/header.php';
require_once '../templates/sidebar_admin.php';
requireAdmin();

// Set variables for active navigation and page title
$active_page = 'loans';
$page_title = 'Manajemen Peminjaman - Admin';
$show_sidebar = true;

// Handle return confirmation
if (isset($_GET['return_id'])) {
    $loan_id = intval($_GET['return_id']);
    
    $update_query = "UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali = NOW() 
                    WHERE id = $loan_id";
    
    if (mysqli_query($koneksi, $update_query)) {
        $_SESSION['message'] = 'Pengembalian buku berhasil dikonfirmasi';
        $_SESSION['message_type'] = 'success';
        header("Location: admin_peminjaman.php");
        exit();
    } else {
        $_SESSION['message'] = 'Gagal mengkonfirmasi pengembalian: ' . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'danger';
    }
}

// Handle loan extension
if (isset($_POST['extend_loan'])) {
    $loan_id = intval($_POST['loan_id']);
    $new_date = mysqli_real_escape_string($koneksi, $_POST['new_date']);
    
    // Validate the new date
    if (!strtotime($new_date)) {
        $_SESSION['message'] = 'Format tanggal tidak valid';
        $_SESSION['message_type'] = 'danger';
        header("Location: admin_peminjaman.php");
        exit();
    }
    
    $update_query = "UPDATE peminjaman SET tanggal_harus_kembali = '$new_date' 
                    WHERE id = $loan_id";
    
    if (mysqli_query($koneksi, $update_query)) {
        $_SESSION['message'] = 'Peminjaman berhasil diperpanjang sampai ' . $new_date;
        $_SESSION['message_type'] = 'success';
        header("Location: admin_peminjaman.php");
        exit();
    } else {
        $_SESSION['message'] = 'Gagal memperpanjang peminjaman: ' . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'danger';
    }
}

// Query untuk peminjaman aktif
$query_active = "SELECT l.*, u.username as user_name, b.judul as book_title, b.cover as book_cover 
    FROM peminjaman l
    JOIN users u ON l.id_user = u.id
    JOIN buku b ON l.id_buku = b.id
    WHERE l.status = 'Dipinjam'
    ORDER BY l.tanggal_harus_kembali ASC";
$result_active = mysqli_query($koneksi, $query_active);
$active_loans = [];
while ($row = mysqli_fetch_assoc($result_active)) {
    $active_loans[] = $row;
}

// Query untuk riwayat peminjaman
$query_history = "SELECT l.*, u.username as user_name, b.judul as book_title 
    FROM peminjaman l
    JOIN users u ON l.id_user = u.id
    JOIN buku b ON l.id_buku = b.id
    WHERE l.status != 'Dipinjam'
    ORDER BY l.tanggal_kembali DESC";
$result_history = mysqli_query($koneksi, $query_history);
$loan_history = [];
while ($row = mysqli_fetch_assoc($result_history)) {
    $loan_history[] = $row;
}
?>

<main class="main-content">
    <div class="main-header">
        <h1><i class="fas fa-exchange-alt"></i> Manajemen Peminjaman</h1>
        <div class="admin-actions">
            <button class="btn" id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </div>

    <!-- Notification Section -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['message'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Active Loans Section -->
    <section class="loan-section">
        <div class="section-header">
            <h2><i class="fas fa-clock"></i> Peminjaman Aktif</h2>
            <span class="badge"><?= count($active_loans) ?> Aktif</span>
        </div>

        <?php if (!empty($active_loans)): ?>
            <div class="loan-grid">
                <?php foreach ($active_loans as $loan): 
                    $is_overdue = strtotime($loan['tanggal_harus_kembali']) < time();
                    $days_left = floor((strtotime($loan['tanggal_harus_kembali']) - time()) / (60 * 60 * 24));
                ?>
                    <div class="loan-card <?= $is_overdue ? 'overdue' : '' ?>">
                        <div class="loan-info">
                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                                <?php if (!empty($loan['book_cover'])): ?>
                                    <img src="../../uploads/buku/<?= htmlspecialchars($loan['book_cover']) ?>" alt="Book Cover" class="book-cover">
                                <?php else: ?>
                                    <div class="book-cover no-cover"><i class="fas fa-book"></i></div>
                                <?php endif; ?>
                                <div>
                                    <h3><?= htmlspecialchars($loan['book_title']) ?></h3>
                                    <p><i class="fas fa-user"></i> <?= htmlspecialchars($loan['user_name']) ?></p>
                                    <p><i class="fas fa-calendar-alt"></i> Pinjam: <?= date('d M Y', strtotime($loan['tanggal_pinjam'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="loan-dates">
                                <div>
                                    <span>Batas Pengembalian</span>
                                    <strong><?= date('d M Y', strtotime($loan['tanggal_harus_kembali'])) ?></strong>
                                </div>
                                <div>
                                    <span>Sisa Waktu</span>
                                    <strong class="<?= $is_overdue ? 'text-danger' : '' ?>">
                                        <?= $is_overdue ? 'Terlambat '.abs($days_left).' hari' : $days_left.' hari lagi' ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="loan-actions">
                            <a href="?return_id=<?= $loan['id'] ?>" class="btn-return" onclick="return confirm('Apakah Anda yakin ingin mengkonfirmasi pengembalian buku ini?')">
                                <i class="fas fa-check-circle"></i> Konfirmasi Pengembalian
                            </a>                            
                            <button class="btn-extend" onclick="extendLoan(<?= $loan['id'] ?>, '<?= ($loan['tanggal_harus_kembali']) ?>')">
                                <i class="fas fa-calendar-plus"></i> Perpanjang
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>Tidak ada peminjaman aktif saat ini</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Loan History Section -->
    <section class="loan-section">
        <div class="section-header">
            <h2><i class="fas fa-history"></i> Riwayat Peminjaman</h2>
            <span class="badge"><?= count($loan_history) ?> Riwayat</span>
        </div>
        
        <div class="history-table">
            <table id="loanHistoryTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($loan_history)): ?>
                        <?php foreach ($loan_history as $history): 
                            $returned_date = !empty($history['tanggal_kembali']) ? date('d M Y', strtotime($history['tanggal_kembali'])) : '-';
                            $status = $history['status'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($history['user_name']) ?></td>
                                <td><?= htmlspecialchars($history['book_title']) ?></td>
                                <td><?= date('d M Y', strtotime($history['tanggal_pinjam'])) ?></td>
                                <td><?= date('d M Y', strtotime($history['tanggal_harus_kembali'])) ?></td>
                                <td><?= $returned_date ?></td>
                                <td>
                                    <span class="badge <?= $status == 'Dikembalikan' ? 'badge-success' : ($status == 'Terlambat' ? 'badge-warning' : 'badge-info') ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada riwayat peminjaman</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
function extendLoan(loanId, currentDueDate) {
    const defaultDate = new Date(currentDueDate);
    defaultDate.setDate(defaultDate.getDate() + 7);
    const defaultDateStr = defaultDate.toISOString().split('T')[0];
    
    const newDate = prompt('Masukkan tanggal baru perpanjangan (YYYY-MM-DD):', defaultDateStr);    
    if (newDate) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(newDate)) {
            alert('Format tanggal harus YYYY-MM-DD');
            return;
        }
        
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const loanIdInput = document.createElement('input');
        loanIdInput.type = 'hidden';
        loanIdInput.name = 'loan_id';
        loanIdInput.value = loanId;
        
        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'new_date';
        dateInput.value = newDate;
        
        const submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'extend_loan';
        submitInput.value = '1';
        
        form.appendChild(loanIdInput);
        form.appendChild(dateInput);
        form.appendChild(submitInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
require_once '../templates/footer.php';
?>