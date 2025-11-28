<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Handle hapus chapter
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM chapter WHERE id = $id");
    $success = "Chapter berhasil dihapus!";
}

// Ambil semua chapter dengan info komik
$chapter_list = mysqli_query($koneksi, "
    SELECT c.*, k.judul as komik_judul, k.slug as komik_slug 
    FROM chapter c 
    JOIN komik k ON c.komik_id = k.id 
    ORDER BY c.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Chapter - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-file-alt"></i> Kelola Chapter
            </span>
            <div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">Dashboard</a>
                <a href="upload-chapter.php" class="btn btn-primary btn-sm me-2">Upload Chapter</a>
                <a href="komik.php" class="btn btn-info btn-sm me-2">Kelola Komik</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="fas fa-file-alt"></i> Management Chapter</h3>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Chapter</h5>
                <a href="upload-chapter.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Chapter
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Komik</th>
                                <th>Chapter</th>
                                <th>Judul</th>
                                <th>Halaman</th>
                                <th>Views</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($chapter = mysqli_fetch_assoc($chapter_list)): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $chapter['komik_judul']; ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $chapter['chapter_number']; ?></span>
                                </td>
                                <td><?php echo $chapter['judul_chapter']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $chapter['total_pages'] > 0 ? 'success' : 'warning'; ?>">
                                        <?php echo $chapter['total_pages'] ?: 0; ?> halaman
                                    </span>
                                </td>
                                <td><?php echo $chapter['views']; ?></td>
                                <td><?php echo date('d M Y', strtotime($chapter['created_at'])); ?></td>
                                <td>
                                    <a href="../baca.php?chapter=<?php echo $chapter['slug']; ?>" 
                                       target="_blank" class="btn btn-sm btn-success">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit-chapter.php?id=<?php echo $chapter['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="upload.php?chapter=<?php echo $chapter['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-upload"></i>
                                    </a>
                                    <a href="?hapus=<?php echo $chapter['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus chapter ini?')">
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