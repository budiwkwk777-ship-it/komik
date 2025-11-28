<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Get komik with images info
$komik_with_images = mysqli_query($koneksi, "
    SELECT k.*, 
           (SELECT COUNT(*) FROM chapter c WHERE c.komik_id = k.id) as total_chapters,
           (SELECT COUNT(*) FROM chapter c2 WHERE c2.komik_id = k.id AND c2.images IS NOT NULL AND c2.images != '[]') as chapters_with_images
    FROM komik k 
    ORDER BY k.judul
");

// Get statistics
$total_komik = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM komik"));
$total_chapters = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM chapter"))['total'];
$chapters_with_images = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM chapter WHERE total_pages > 0"))['total'];
$komik_with_cover = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komik WHERE cover IS NOT NULL AND cover != ''"))['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Images - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .progress {
            height: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-images"></i> Manage Images
            </span>
            <div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="upload.php" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-upload"></i> Upload
                </a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="fas fa-images"></i> Image Management</h3>
        <p class="text-muted">Kelola gambar cover dan halaman komik</p>
        
        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-book fa-2x text-primary mb-2"></i>
                        <h5><?php echo $total_komik; ?></h5>
                        <small class="text-muted">Total Komik</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                        <h5><?php echo $total_chapters; ?></h5>
                        <small class="text-muted">Total Chapter</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-image fa-2x text-warning mb-2"></i>
                        <h5><?php echo $komik_with_cover; ?></h5>
                        <small class="text-muted">Komik dengan Cover</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-images fa-2x text-info mb-2"></i>
                        <h5><?php echo $chapters_with_images; ?></h5>
                        <small class="text-muted">Chapter dengan Halaman</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <?php while($komik = mysqli_fetch_assoc($komik_with_images)): 
                $progress = ($komik['total_chapters'] > 0) ? ($komik['chapters_with_images'] / $komik['total_chapters'] * 100) : 0;
                $progress_class = $progress == 100 ? 'bg-success' : ($progress > 0 ? 'bg-warning' : 'bg-danger');
            ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <img src="../assets/images/covers/<?php echo $komik['cover'] ?: 'default.jpg'; ?>" 
                                     class="img-fluid rounded" 
                                     style="height: 120px; object-fit: cover; width: 100%;"
                                     onerror="this.src='../assets/images/covers/default.jpg'">
                            </div>
                            <div class="col-8">
                                <h6><?php echo $komik['judul']; ?></h6>
                                <p class="small mb-2">
                                    <strong>Chapters:</strong> <?php echo $komik['total_chapters']; ?><br>
                                    <strong>With Images:</strong> <?php echo $komik['chapters_with_images']; ?>
                                </p>
                                
                                <div class="progress mb-2">
                                    <div class="progress-bar <?php echo $progress_class; ?>" 
                                         style="width: <?php echo $progress; ?>%">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?php echo number_format($progress, 1); ?>% Complete
                                </small>
                                
                                <div class="mt-2">
                                    <a href="upload.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-upload"></i> Upload Images
                                    </a>
                                    <?php if($komik['cover']): ?>
                                        <span class="badge bg-success ms-1">
                                            <i class="fas fa-check"></i> Cover
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger ms-1">
                                            <i class="fas fa-times"></i> No Cover
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Summary -->
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Summary</h5>
            </div>
            <div class="card-body">
                <?php
                $chapter_progress = ($total_chapters > 0) ? ($chapters_with_images / $total_chapters * 100) : 0;
                $cover_progress = ($total_komik > 0) ? ($komik_with_cover / $total_komik * 100) : 0;
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Cover Progress</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: <?php echo $cover_progress; ?>%"></div>
                        </div>
                        <small><?php echo $komik_with_cover; ?> / <?php echo $total_komik; ?> komik memiliki cover</small>
                    </div>
                    <div class="col-md-6">
                        <h6>Chapter Images Progress</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" style="width: <?php echo $chapter_progress; ?>%"></div>
                        </div>
                        <small><?php echo $chapters_with_images; ?> / <?php echo $total_chapters; ?> chapter memiliki gambar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>