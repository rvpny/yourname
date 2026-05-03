<?php
session_start();
require_once '../../config/auth.php';
include_once '../../config/koneksi.php';

$query_active = "SELECT p.id, b.judul as title, b.pengarang as author, 
                p.tanggal_pinjam, p.tanggal_harus_kembali,
                DATEDIFF(p.tanggal_harus_kembali, CURDATE()) as days_left
                FROM peminjaman p
                JOIN buku b ON p.id_buku = b.id
                WHERE p.id_user = {$_SESSION['user_id']} 
                AND p.status = 'Dipinjam'
                ORDER BY p.tanggal_pinjam DESC
                LIMIT 3";
$active_loans = mysqli_query($koneksi, $query_active);

$query_history = "SELECT p.id, b.judul as title, b.pengarang as author, p.tanggal_pinjam, p.tanggal_kembali 
                 FROM peminjaman p
                 JOIN buku b ON p.id_buku = b.id
                 WHERE p.id_user = {$_SESSION['user_id']} AND p.status = 'Dikembalikan'
                 ORDER BY p.tanggal_kembali DESC
                 LIMIT 10";
$loan_history = mysqli_query($koneksi, $query_history);
?>


<?php 
include '../templates/header.php';
include '../templates/sidebar_user.php'; ?>

    <main class="main-content">
        <header class="main-header">
            <h1><i class="fas fa-book-open"></i> Peminjaman Buku</h1>
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

        <!-- Section: Sedang Dipinjam -->
        <section class="loan-section">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Sedang Dipinjam (Maksimal 3)</h2>
                <span class="badge"><?= mysqli_num_rows($active_loans) ?> buku</span>
            </div>

            <?php if (mysqli_num_rows($active_loans) > 0): ?>
            <div class="loan-grid">
                <?php while($loan = mysqli_fetch_assoc($active_loans)): ?>
                <div class="loan-card">
                    <div class="loan-info">
                        <h3><?= htmlspecialchars($loan['title']) ?></h3>
                        <p>Penulis: <?= htmlspecialchars($loan['author']) ?></p>
                        <div class="loan-dates">
                            <div>
                                <span>Pinjam:</span>
                                <strong><?= date('d M Y', strtotime($loan['tanggal_pinjam'])) ?></strong>
                            </div>
                            <div>
                                <span>Batas:</span>
                                <strong class="<?= (strtotime($loan['tanggal_harus_kembali']) < time()) ? 'text-danger' : '' ?>">
                                    <?= date('d M Y', strtotime($loan['tanggal_harus_kembali'])) ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="loan-actions">
                        <button class="btn btn-return" data-id="<?= $loan['id'] ?>">
                            <i class="fas fa-undo"></i> Kembalikan
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>Tidak ada buku yang sedang dipinjam</p>
            </div>
            <?php endif; ?>
        </section>

        <!-- Section: Riwayat Peminjaman -->
        <section class="loan-section">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> Riwayat Peminjaman</h2>
                <span class="badge"><?= mysqli_num_rows($loan_history) ?> buku</span>
            </div>

            <?php if (mysqli_num_rows($loan_history) > 0): ?>
            <div class="history-table">
                <table>
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($history = mysqli_fetch_assoc($loan_history)): 
                            $durasi = date_diff(
                                new DateTime($history['tanggal_pinjam']), 
                                new DateTime($history['tanggal_kembali'])
                            )->format('%a hari');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($history['title']) ?></td>
                            <td><?= htmlspecialchars($history['author']) ?></td>
                            <td><?= date('d M Y', strtotime($history['tanggal_pinjam'])) ?></td>
                            <td><?= date('d M Y', strtotime($history['tanggal_kembali'])) ?></td>
                            <td><?= $durasi ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>Belum ada riwayat peminjaman</p>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>

    <script src="../assets/js/user.js"></script>
    <script>
    document.querySelectorAll('.btn-return').forEach(btn => {
        btn.addEventListener('click', function() {
            const loanId = this.getAttribute('data-id');
            if (confirm('Apakah Anda yakin ingin mengembalikan buku ini?')) {
                window.location.href = `proses_kembali.php?id=${loanId}`;
            }
        });
    });
    </script>
<?php include '../templates/footer.php'; ?>