<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$chapter_id = $_GET['id'] ?? 0;
$success = '';
$error = '';

// Get chapter data
$chapter_query = mysqli_query($koneksi, "
    SELECT c.*, k.judul as komik_judul, k.slug as komik_slug 
    FROM chapter c 
    JOIN komik k ON c.komik_id = k.id 
    WHERE c.id = $chapter_id
");
$chapter = mysqli_fetch_assoc($chapter_query);

if(!$chapter) {
    die("Chapter tidak ditemukan!");
}

// Handle update chapter
if(isset($_POST['update_chapter'])) {
    $chapter_number = $_POST['chapter_number'];
    $judul_chapter = mysqli_real_escape_string($koneksi, $_POST['judul_chapter']);
    
    $query = "UPDATE chapter SET 
              chapter_number = '$chapter_number', 
              judul_chapter = '$judul_chapter' 
              WHERE id = $chapter_id";
    
    if(mysqli_query($koneksi, $query)) {
        $success = "Chapter berhasil diupdate!";
        // Refresh chapter data
        $chapter_query = mysqli_query($koneksi, "
            SELECT c.*, k.judul as komik_judul, k.slug as komik_slug 
            FROM chapter c 
            JOIN komik k ON c.komik_id = k.id 
            WHERE c.id = $chapter_id
        ");
        $chapter = mysqli_fetch_assoc($chapter_query);
    } else {
        $error = "Gagal update chapter: " . mysqli_error($koneksi);
    }
}

// Get chapter pages
$pages_query = mysqli_query($koneksi, "
    SELECT * FROM chapter_pages 
    WHERE chapter_id = $chapter_id 
    ORDER BY page_number ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Chapter - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-edit"></i> Edit Chapter
            </span>
            <div>
                <a href="chapter.php" class="btn btn-secondary btn-sm me-2">Kembali</a>
                <a href="dashboard.php" class="btn btn-primary btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="fas fa-edit"></i> Edit Chapter: <?php echo $chapter['komik_judul']; ?></h3>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Edit Chapter Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Chapter</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Nomor Chapter</label>
                                <input type="number" name="chapter_number" class="form-control" 
                                       step="0.1" value="<?php echo $chapter['chapter_number']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Judul Chapter</label>
                                <input type="text" name="judul_chapter" class="form-control" 
                                       value="<?php echo $chapter['judul_chapter']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" value="<?php echo $chapter['slug']; ?>" readonly>
                                <small class="text-muted">Slug tidak bisa diubah</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Halaman</label>
                                <input type="text" class="form-control" value="<?php echo $chapter['total_pages']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Views</label>
                                <input type="text" class="form-control" value="<?php echo $chapter['views']; ?>" readonly>
                            </div>
                            <button type="submit" name="update_chapter" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Chapter
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Chapter Pages -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-images"></i> Daftar Halaman (<?php echo $chapter['total_pages']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($pages_query) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>File</th>
                                            <th>Path</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($page = mysqli_fetch_assoc($pages_query)): ?>
                                        <tr>
                                            <td><?php echo $page['page_number']; ?></td>
                                            <td>
                                                <img src="../assets/images/pages/<?php echo $page['image_path']; ?>" 
                                                     style="width: 50px; height: 70px; object-fit: cover;" 
                                                     class="rounded"
                                                     onerror="this.src='../assets/images/pages/default-page.jpg'">
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo $page['image_path']; ?></small>
                                            </td>
                                            <td>
                                                <a href="../assets/images/pages/<?php echo $page['image_path']; ?>" 
                                                   target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-image fa-3x mb-3"></i>
                                <p>Belum ada halaman untuk chapter ini</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="upload.php?chapter=<?php echo $chapter_id; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-upload"></i> Upload Halaman Baru
                            </a>
                            <small class="text-muted d-block mt-1">
                                Upload halaman baru akan menggantikan halaman yang sudah ada
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-body text-center">
                <h5 class="mb-3">Quick Actions</h5>
                <div class="btn-group">
                    <a href="../baca.php?chapter=<?php echo $chapter['slug']; ?>" 
                       target="_blank" class="btn btn-success">
                        <i class="fas fa-eye"></i> Preview Chapter
                    </a>
                    <a href="chapter.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <a href="petunjuk.php" class="btn btn-info">
                        <i class="fas fa-question-circle"></i> Bantuan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>