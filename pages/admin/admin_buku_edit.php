<?php
session_start();
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
requireAdmin();


$page_title = "Edit Buku - Admin Perpustakaan";
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
        .current-cover {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            display: inline-block;
        }
        .current-cover img {
            border: 1px solid #ddd;
            border-radius: 3px;
        }
    </style>
';

$book_id = $_GET['id'] ?? 0;
$book = null;
$errors = [];

$stmt = $koneksi->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_buku.php?error=book_not_found");
    exit;
}

$book = $result->fetch_assoc();

// Proses form edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok', 'genre'];
    $error = false;
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Kolom " . ucfirst($field) . " harus diisi!";
            $error = true;
            break;
        }
    }
    if (!$error) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isbn = mysqli_real_escape_string($koneksi, $_POST['isbn'] ?? '');
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $genre = mysqli_real_escape_string($koneksi, $_POST['genre']);
    $stok = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $current_cover = $book['cover'];
    }

    // Handle upload cover baru
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['cover']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../../uploads/buku/';
            $file_ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $filename = 'book_' . time() . '.' . $file_ext;
            $new_cover_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $new_cover_path)) {
                // Hapus cover lama jika ada dan bukan default
                if ($current_cover && !str_contains($current_cover, 'default_cover')) {
                    @unlink('../../' . $current_cover);
                }
                $current_cover = '../../uploads/buku/' . $filename;
            } else {
                $errors[] = "Gagal mengupload cover buku";
            }
        } else {
            $errors[] = "Format file tidak didukung (hanya JPEG, PNG, GIF)";
        }
    }

    // Jika tidak ada error, update database
    if (empty($errors)) {
        $query = "UPDATE buku SET 
                  judul = ?, 
                  isbn = ?,
                  pengarang = ?, 
                  penerbit = ?, 
                  tahun_terbit = ?, 
                  genre = ?, 
                  stok = ?, 
                  deskripsi = ?, 
                  cover = ?
                  WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssisssi", $judul, $isbn, $pengarang, $penerbit, $tahun, $genre, $stok, $deskripsi, $current_cover, $book_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Buku berhasil diperbarui!";
            header("Location: admin_buku.php");
            exit;
        } else {
            $errors[] = "Gagal memperbarui buku: " . mysqli_error($koneksi);
        }
    }
}
?>

<?php include '../templates/header.php';
  include '../templates/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1><i class="fas fa-edit"></i> Edit Buku: <?= htmlspecialchars($book['judul']) ?></h1>
                <a href="admin_buku.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="admin_buku_edit.php?id=<?= $book_id ?>" method="post" enctype="multipart/form-data" class="book-form" id="bookForm" onsubmit="validatForm()">
                <div class="form-group">
                    <label for="judul">Judul Buku*</label>
                    <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($book['judul']) ?>" required class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="pengarang">Pengarang*</label>
                        <input type="text" id="pengarang" name="pengarang" value="<?= htmlspecialchars($book['pengarang']) ?>" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="penerbit">Penerbit</label>
                        <input type="text" id="penerbit" name="penerbit" value="<?= htmlspecialchars($book['penerbit']) ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" name="isbn" id="isbn" class="form-control" value="<?= htmlspecialchars($book['isbn']) ?>">  
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" value="<?= htmlspecialchars($book['tahun_terbit']) ?>" min="1000" max="<?= date('Y') ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <select id="genre" name="genre" class="form-control">
                            <option value="Fiksi" <?= $book['genre'] === 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                            <option value="Non-Fiksi" <?= $book['genre'] === 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                            <option value="Islami" <?= $book['genre'] === 'Islami' ? 'selected' : '' ?>>Islami</option>
                            <option value="Sejarah" <?= $book['genre'] === 'Sejarah' ? 'selected' : '' ?>>Sejarah</option>
                            <option value="Pemikiran" <?= $book['genre'] === 'Pemikiran' ? 'selected' : '' ?>>Pemikiran</option>
                            <option value="Sains" <?= ($book['genre'] ?? '') === 'Sains' ? 'selected' : '' ?>>Sains</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($book['stok']) ?>" min="0" required class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control"><?= htmlspecialchars($book['deskripsi']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="cover">Cover Buku</label>
                    <?php if ($book['cover']): ?>
                        <div class="current-cover">
                            <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Current Cover" style="max-width: 200px; display: block; margin-bottom: 10px;">
                            <small>Cover saat ini</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="cover" name="cover" accept="image/*" class="form-control">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah cover</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="admin_buku.php" class="btn btn-reset">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </main>
   
        <?php include '../templates/footer.php'; ?>

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