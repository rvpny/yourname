<?php
session_start();

$popular_books = [];
if (file_exists('./config/koneksi.php')) {
    require_once './config/koneksi.php';
    $popular_books = getPopularBooks(6);
}

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Hapus pesan setelah ditampilkan
    echo "<script>alert('$message');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Bilgi Evi</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body class="landing">
    <!-- Main Header -->
    <nav class="main-header">
        <div class="header-container">   
            <div class="logo">
                <a href="index.php" style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: #495F86; text-decoration: none;">BILGI EVI</a>
            </div>       
            <ul class="navigation-menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="#about"> Kami</a></li>
                <li><a href="<?= isset($_SESSION['login']) ? 
            ($_SESSION['role'] === 'admin' ? './pages/admin/admin_dashboard.php' : './pages/users/user_dashboard.php') : 
            './pages/login.php' ?>" 
            class="<?= !isset($_SESSION['login']) ? 'with-login-check' : '' ?>">Perpustakaan</a></li> 
            </ul>
            <div class="header-actions">
                <?php if(isset($_SESSION['login'])): ?>
                    <span class="user-greeting" style="margin-right: 15px;">
                        Halo, <?= htmlspecialchars($_SESSION['username']) ?>
                        <small>(<?= ucfirst($_SESSION['role']) ?>)</small>
                    </span>
                    <a href="./pages/logout.php" class="register-button">Logout</a>
                <?php else: ?>
                    <a href="./pages/login.php" class="login-button">Login</a>
                    <a href="./pages/register.php" class="register-button">Daftar</a>
                <?php endif; ?>
            </div>
             <button class="mobile-menu-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-content">
            <h1 id="welcome-text">Selamat Datang</h1>
            <p id="welcome-tagline">Temukan dunia baru dalam setiap halaman</p>
        </div>

        <!-- Slider Buku -->
        <div class="swiper-container book-slider">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide"><img src="./assets/image/bg/bg-1.png" alt="Cover Buku 1"></div>
                <!-- Slide 2 -->
                <div class="swiper-slide"><img src="./assets/image/bg/bg-2.png" alt="Cover Buku 2"></div>
                <!-- Slide 3 -->
                <div class="swiper-slide"><img src="./assets/image/bg/bg-3.png" alt="Cover Buku 3"></div>
                <!-- Slide 4 -->
                <div class="swiper-slide"><img src="./assets/image/bg/bg-4.png" alt="Cover Buku 4"></div>
                <!-- Slide 5 -->
                <div class="swiper-slide"><img src="./assets/image/bg/bg-5.png" alt="Cover Buku 5"></div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </header>

    <!-- About -->
    <section id="about" class="about-section">
    <div class="container" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 32px 24px; margin-bottom: 40px;">
        <h2 style="color:#495F86ff; font-size: 2.2rem; margin-bottom: 16px; text-align:center;">
            <i class="fas fa-book-open" style="margin-right:8px;"></i> Tentang Kami
        </h2>
        <p style="font-size: 1.1rem; color: #333; line-height: 1.8; text-align:justify;">
            <strong>Perpustakaan Bilgi Evi</strong> adalah pusat literasi dan pengetahuan yang berdedikasi untuk mendukung minat baca masyarakat. Kami menyediakan ribuan koleksi buku dari berbagai genre, mulai dari fiksi, non-fiksi, hingga referensi akademik yang dapat diakses oleh semua kalangan.<br><br>
            Dengan suasana yang nyaman dan fasilitas modern, kami berkomitmen menjadi tempat belajar, berdiskusi, dan berinovasi. Selain layanan peminjaman buku, kami juga rutin mengadakan kegiatan seperti bedah buku, kelas literasi digital, dan pelatihan keterampilan untuk mendukung pengembangan diri anggota.<br><br>
            Mari bergabung bersama kami, temukan inspirasi baru, dan jadikan membaca sebagai bagian dari gaya hidup Anda!
        </p>
    </div>
</section>

<!-- Popular Books Section -->
<main class="content-section">
    <section class="popular-books">
        <div class="section-header">
             <h2>Buku Populer</h2>
             <a href="<?= isset($_SESSION['login']) ? ($_SESSION['role'] === 'admin' ? './pages/admin/admin_buku.php' : './pages/users/user_buku.php') : './pages/login.php' ?>" class="view-all-button">
                Lihat Semua Buku <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="book-grid">
        <?php
        require_once './config/koneksi.php';

        $query_popular = "SELECT b.*, 
                        (SELECT COUNT(*) FROM peminjaman p WHERE p.id_buku = b.id) as pinjam_count
                        FROM buku b
                        LEFT JOIN peminjaman p ON b.id = p.id_buku
                        WHERE b.stok > 0 
                        GROUP BY b.id
                        ORDER BY pinjam_count DESC, b.stok DESC 
                        LIMIT 6";
        $result_popular = mysqli_query($koneksi, $query_popular);

        if(mysqli_num_rows($result_popular) > 0):
            while($book = mysqli_fetch_assoc($result_popular)):

                $detail_link = isset($_SESSION['login']) 
                    ? ($_SESSION['role'] === 'admin' 
                        ? './pages/admin/admin_buku_edit.php?id='.$book['id'] 
                        : './pages/users/detail_buku.php?id='.$book['id'])
                    : 'javascript:void(0);';
                
                $link_class = !isset($_SESSION['login']) ? 'require-login' : '';
        ?>

        <div class="book-item">
            <?php if (isset($_SESSION['login'])): ?>
                <a href="<?= $detail_link ?>">
                    <?php if (!empty($book['cover'])): ?>
                        <img src="./uploads/buku/<?= htmlspecialchars($book['cover']) ?>" 
                            alt="Cover <?= htmlspecialchars($book['judul']) ?>">
                    <?php else: ?>
                        <div class="book-cover-placeholder">
                            <i class="fas fa-book"></i>
                        </div>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="./pages/login.php?redirect=detail_buku&id=<?= $book['id'] ?>" 
                style="position:relative;"
                title="Login dulu untuk melihat detail buku">
                    <?php if (!empty($book['cover'])): ?>
                        <img loading="lazy" src="./uploads/buku/<?= htmlspecialchars($book['cover']) ?>" 
                            alt="Cover <?= htmlspecialchars($book['judul']) ?> karya <?= htmlspecialchars($book['pengarang']) ?>">
                    <?php else: ?>
                        <div class="book-cover-placeholder">
                            <i class="fas fa-book"></i>
                        </div>
                    <?php endif; ?>
                    <div class="login-overlay">
                        <span>Login dulu</span>
                    </div>
                </a>
            <?php endif; ?>
            <h3 title="<?= htmlspecialchars($book['judul']) ?>">
                    <?= htmlspecialchars(mb_strimwidth($book['judul'], 0, 30, '...')) ?>
                </h3>
                <p><?= htmlspecialchars($book['pengarang']) ?></p>
                
                <?php if($book['stok'] <= 0): ?>
                    <div class="out-of-stock">Stok Habis</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>Tidak ada buku populer saat ini</h3>
            </div>
        <?php endif; ?>
        </div>
    </section>
</main>

    <!-- Footer Section -->
    <footer>
        <div class="footer-contact">
            <strong>Kontak:</strong><br>
            Email: <a href="mailto:ravaprayoga9@gmail.com">ravaprayoga9@gmail.com</a><br>
            Telepon: <a href="tel:085866330781">0858-6633-0781</a>
        </div>
        <p style="margin-top:12px;">&copy; 2025 Perpustakaan Bilgi Evi. Semua Hak Cipta Dilindungi.</p>
    </footer>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
            icon: 'success',
            title: 'Logout Berhasil',
            text: 'Anda telah keluar dari sistem'
            });
            
            history.replaceState({}, document.title, window.location.pathname);
        });
        <?php endif; ?>
    <script>
    document.querySelectorAll('a.with-login-check').forEach(link => {
        link.addEventListener('click', function(e) {
            if(!<?= isset($_SESSION['login']) ? 'true' : 'false' ?>) {
                e.preventDefault();
                Swal.fire({
                    title: 'Login Required',
                    text: 'Anda perlu login untuk mengakses perpustakaan',
                    icon: 'info',
                    confirmButtonText: 'Login Now',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = './pages/login.php';
                    }
                });
            }
        });
    });
    </script>
    </script>

</body>
</html>
