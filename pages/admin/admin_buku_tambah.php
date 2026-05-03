<?php
session_start();
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
requireAdmin();

$page_title = "Tambah Buku - Admin Perpustakaan";
$active_page = "buku";
$additional_css = '
    <style>
        .book-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-reset {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
            
        .isbn-input {
            letter-spacing: 1px;
            font-family: monospace;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
    </style>
';

$errors = [];

// Proses form tambah buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok', 'genre'];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Kolom " . ucfirst(str_replace('_', ' ', $field)) . " harus diisi!";
        }
    }
    
    if (empty($errors)) {
        $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
        $isbn = mysqli_real_escape_string($koneksi, $_POST['isbn'] ?? '');
        $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
        $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
        $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
        $genre = mysqli_real_escape_string($koneksi, $_POST['genre']);
        $stok = (int)$_POST['stok'];
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?? '');
    
    // Validasi ISBN jika diisi
    if (!empty($isbn) && !validateISBN($isbn)) {
        $errors[] = "Format ISBN tidak valid. Gunakan ISBN-10 atau ISBN-13";
    }

    // Upload cover buku
    $cover_path = '';
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['cover']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../../uploads/buku/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $filename = 'book_' . time() . '.' . $file_ext;
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_path)) {
                $cover_path = $filename; 
            } else {
                $errors[] = "Gagal mengupload cover buku. Pastikan folder bisa ditulisi.";
                error_log("Upload failed. Target path: " . realpath($upload_dir));
            }
        } else {
            $errors[] = "Format file tidak didukung (hanya JPEG, PNG, JPG)";
        }
    }

    function validateISBN($isbn) {
        // Hapus semua tanda hubung
        $isbn = str_replace('-', '', $isbn);
        
        // ISBN-10 atau ISBN-13
        if (strlen($isbn) == 10) {
            return preg_match('/^\d{9}[\dX]$/i', $isbn);
        } elseif (strlen($isbn) == 13) {
            return preg_match('/^\d{13}$/', $isbn);
        }
        
        return false;
    }
    
    // Penggunaan:
    if (!empty($isbn) && !validateISBN($isbn)) {
        $errors[] = "Format ISBN tidak valid. Gunakan ISBN-10 atau ISBN-13";
    }

    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $query = "INSERT INTO buku (judul, isbn, pengarang, penerbit, tahun_terbit, genre, stok, deskripsi, cover) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssisss", $judul, $isbn, $pengarang, $penerbit, $tahun, $genre, $stok, $deskripsi, $cover_path);
        
        if ($stmt->execute()) {
            $_SESSION['book_success_message'] = "Buku berhasil ditambahkan!";
            header("Location: admin_buku.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan buku: " . mysqli_error($koneksi);
        }
    }
}
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar_admin.php'; ?>


        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1><i class="fas fa-plus-circle"></i> Tambah Buku Baru</h1>
                <a href="admin_buku.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="admin_buku_tambah.php" method="post" enctype="multipart/form-data" class="book-form" id="bookForm" onsubmit="validateForm()">
                <div class="form-group">
                    <label for="judul">Judul Buku*</label>
                    <input type="text" id="judul" name="judul" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" name="isbn" id="isbn" class="form-control" placeholder="Contoh: 978-3-16-148410-0">
                    <small class="text-muted">(Opsional)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="pengarang">Pengarang</label>
                        <input type="text" id="pengarang" name="pengarang" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="penerbit">Penerbit</label>
                        <input type="text" id="penerbit" name="penerbit" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" min="1000" max="<?= date('Y') ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <select id="genre" name="genre" class="form-control" required>
                            <option value="">Pilih Genre</option>
                            <option value="Fiksi" <?= ($book['genre'] ?? '') === 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                            <option value="Non-Fiksi" <?= ($book['genre'] ?? '') === 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                            <option value="Islami" <?= ($book['genre'] ?? '') === 'Islami' ? 'selected' : '' ?>>Islami</option>
                            <option value="Sejarah" <?= ($book['genre'] ?? '') === 'Sejarah' ? 'selected' : '' ?>>Sejarah</option>
                            <option value="Pemikiran" <?= ($book['genre'] ?? '') === 'Pemikiran' ? 'selected' : '' ?>>Pemikiran</option>
                            <option value="Sains" <?= ($book['genre'] ?? '') === 'Sains' ? 'selected' : '' ?>>Sains</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" id="stok" name="stok" min="0" value="1" required class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="cover">Cover Buku</label>
                    <input type="file" id="cover" name="cover" accept="image/*" class="form-control">
                    <small class="text-muted">Format: JPG, PNG, JPEG (Maks. 2MB)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Buku
                    </button>
                    <button type="reset" class="btn btn-reset">
                        <i class="fas fa-undo"></i> Reset Form
                    </button>
                </div>
            </form>
        </main>



<script>
function validateForm() {
    const required = ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok'];
    let valid = true;
    
    required.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return valid;
}

// Disable tombol submit jika form tidak valid
document.getElementById('bookForm').addEventListener('input', function() {
    const required = ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok'];
    const isValid = required.every(field => 
        document.getElementById(field).value.trim() !== ''
    );
    document.getElementById('submitBtn').disabled = !isValid;
});
</script>
    
<?php include '../templates/footer.php'; ?>