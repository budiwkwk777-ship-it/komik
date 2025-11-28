<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Handle tambah komik
if(isset($_POST['tambah_komik'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $sinopsis = mysqli_real_escape_string($koneksi, $_POST['sinopsis']);
    $genre = mysqli_real_escape_string($koneksi, $_POST['genre']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    
    // Generate slug
    $slug = strtolower(str_replace(' ', '-', $judul));
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    
    // Handle cover upload
    $cover_name = '';
    if(isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $cover_name = $slug . '.' . $ext;
        $upload_path = '../assets/images/covers/' . $cover_name;
        
        if(!move_uploaded_file($_FILES['cover']['tmp_name'], $upload_path)) {
            $cover_name = '';
        }
    }
    
    $query = "INSERT INTO komik (judul, slug, penulis, status, sinopsis, genre, kategori, cover) 
              VALUES ('$judul', '$slug', '$penulis', '$status', '$sinopsis', '$genre', '$kategori', '$cover_name')";
    
    if(mysqli_query($koneksi, $query)) {
        $success = "Komik berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan komik: " . mysqli_error($koneksi);
    }
}

// Handle hapus komik
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM komik WHERE id = $id");
    $success = "Komik berhasil dihapus!";
}

// Ambil semua komik
$komik_list = mysqli_query($koneksi, "SELECT * FROM komik ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komik - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .category-badge {
            font-size: 0.7em;
        }
        .status-badge {
            font-size: 0.7em;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-book"></i> Kelola Komik
            </span>
            <div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">Dashboard</a>
                <a href="upload-chapter.php" class="btn btn-primary btn-sm me-2">Upload Chapter</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="fas fa-book"></i> Management Komik</h3>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Form Tambah Komik -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Komik Baru</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Judul Komik</label>
                                <input type="text" name="judul" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Penulis</label>
                                <input type="text" name="penulis" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Hiatus">Hiatus</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="Manga">Manga (Jepang)</option>
                                    <option value="Manhwa">Manhwa (Korea)</option>
                                    <option value="Manhua">Manhua (China)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Genre</label>
                                <input type="text" name="genre" class="form-control" 
                                       placeholder="Action, Adventure, Fantasy" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Cover Image</label>
                                <input type="file" name="cover" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sinopsis</label>
                        <textarea name="sinopsis" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <button type="submit" name="tambah_komik" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Komik
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Komik -->
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Komik</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Status</th>
                                <th>Chapter</th>
                                <th>Views</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($komik = mysqli_fetch_assoc($komik_list)): 
                                $chapter_count = mysqli_fetch_assoc(mysqli_query($koneksi, 
                                    "SELECT COUNT(*) as total FROM chapter WHERE komik_id = {$komik['id']}"))['total'];
                            ?>
                            <tr>
                                <td>
                                    <img src="../assets/images/covers/<?php echo $komik['cover'] ?: 'default.jpg'; ?>" 
                                         style="width: 50px; height: 70px; object-fit: cover;" 
                                         class="rounded"
                                         onerror="this.src='../assets/images/covers/default.jpg'">
                                </td>
                                <td>
                                    <strong><?php echo $komik['judul']; ?></strong><br>
                                    <small class="text-muted"><?php echo $komik['genre']; ?></small>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        if($komik['kategori'] == 'Manga') echo 'bg-danger';
                                        elseif($komik['kategori'] == 'Manhwa') echo 'bg-success';
                                        else echo 'bg-warning';
                                        ?>
                                        category-badge">
                                        <?php echo $komik['kategori']; ?>
                                    </span>
                                </td>
                                <td><?php echo $komik['penulis']; ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        if($komik['status'] == 'Ongoing') echo 'bg-warning';
                                        elseif($komik['status'] == 'Completed') echo 'bg-success';
                                        else echo 'bg-secondary';
                                        ?>
                                        status-badge">
                                        <?php echo $komik['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $chapter_count; ?> chapter</td>
                                <td><?php echo $komik['views']; ?> views</td>
                                <td>
                                    <a href="chapter.php?komik=<?php echo $komik['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?hapus=<?php echo $komik['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus komik ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>