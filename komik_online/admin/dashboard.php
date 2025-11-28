<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Statistics - Komik
$total_komik = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komik"))['total'];
$total_chapter = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM chapter"))['total'];
$total_views_komik = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(views) as total FROM komik"))['total'];
$total_views_chapter = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(views) as total FROM chapter"))['total'];
$total_views = $total_views_komik + $total_views_chapter;

// Statistics - Images
$total_pages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM chapter_pages"))['total'];
$komik_with_cover = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komik WHERE cover IS NOT NULL AND cover != ''"))['total'];
$chapters_with_pages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM chapter WHERE total_pages > 0"))['total'];

// Statistics by category
$manga_count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komik WHERE kategori = 'Manga'"))['total'];
$manhwa_count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komik WHERE kategori = 'Manhwa'"))['total'];
$manhua_count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komik WHERE kategori = 'Manhua'"))['total'];

// Recent activity
$recent_chapters = mysqli_query($koneksi, "
    SELECT c.*, k.judul as komik_judul, k.slug as komik_slug 
    FROM chapter c 
    JOIN komik k ON c.komik_id = k.id 
    ORDER BY c.created_at DESC 
    LIMIT 5
");

// Popular komik
$popular_komik = mysqli_query($koneksi, "
    SELECT * FROM komik 
    ORDER BY views DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KomikOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stat-card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            padding: 0;
        }
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-nav {
            padding: 20px 0;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 0;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 3px solid white;
        }
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-brand">
                    <h4 class="mb-0">
                        <i class="fas fa-book"></i> KomikOnline
                    </h4>
                    <small class="text-white-50">Admin Panel</small>
                </div>
                
                <div class="sidebar-nav">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="petunjuk.php">
                                <i class="fas fa-graduation-cap"></i> Petunjuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="komik.php">
                                <i class="fas fa-book"></i> Kelola Komik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="chapter.php">
                                <i class="fas fa-file-alt"></i> Kelola Chapter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload-chapter.php">
                                <i class="fas fa-file-upload"></i> Upload Chapter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload.php">
                                <i class="fas fa-upload"></i> Upload Gambar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="images.php">
                                <i class="fas fa-images"></i> Manage Images
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="../index.php" target="_blank">
                                <i class="fas fa-eye"></i> View Website
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Top Navbar -->
                <nav class="navbar navbar-light bg-light shadow-sm">
                    <div class="container-fluid">
                        <h5 class="mb-0">Dashboard Overview</h5>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted">
                                <i class="fas fa-user-circle"></i> 
                                <?php echo $_SESSION['admin']; ?>
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Main Content Area -->
                <div class="container-fluid mt-4">
                    <!-- Quick Stats -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-0 bg-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="card-title">Total Komik</h5>
                                            <h2 class="mb-0"><?php echo $total_komik; ?></h2>
                                            <small>Komik terdaftar</small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book stat-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-0 bg-success text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="card-title">Total Chapter</h5>
                                            <h2 class="mb-0"><?php echo $total_chapter; ?></h2>
                                            <small>Chapter tersedia</small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-alt stat-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-0 bg-warning text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="card-title">Total Views</h5>
                                            <h2 class="mb-0"><?php echo number_format($total_views); ?></h2>
                                            <small>Total pembacaan</small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-eye stat-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-0 bg-info text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="card-title">Total Halaman</h5>
                                            <h2 class="mb-0"><?php echo number_format($total_pages); ?></h2>
                                            <small>Halaman komik</small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-images stat-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-globe-asia fa-2x text-danger mb-2"></i>
                                    <h5>Manga</h5>
                                    <h3 class="text-danger"><?php echo $manga_count; ?></h3>
                                    <small class="text-muted">Komik Jepang</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-landmark fa-2x text-success mb-2"></i>
                                    <h5>Manhwa</h5>
                                    <h3 class="text-success"><?php echo $manhwa_count; ?></h3>
                                    <small class="text-muted">Komik Korea</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-dragon fa-2x text-warning mb-2"></i>
                                    <h5>Manhua</h5>
                                    <h3 class="text-warning"><?php echo $manhua_count; ?></h3>
                                    <small class="text-muted">Komik China</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Activity -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <?php if(mysqli_num_rows($recent_chapters) > 0): ?>
                                        <div class="list-group list-group-flush">
                                            <?php while($chapter = mysqli_fetch_assoc($recent_chapters)): ?>
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo $chapter['komik_judul']; ?></h6>
                                                            <p class="mb-1 text-muted">
                                                                Chapter <?php echo $chapter['chapter_number']; ?> - <?php echo $chapter['judul_chapter']; ?>
                                                            </p>
                                                            <small class="text-muted">
                                                                <?php echo date('d M Y H:i', strtotime($chapter['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <span class="badge bg-primary">
                                                            <?php echo $chapter['total_pages'] ?: 0; ?> halaman
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Belum ada activity</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Popular Komik -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-fire"></i> Popular Komik</h5>
                                </div>
                                <div class="card-body">
                                    <?php if(mysqli_num_rows($popular_komik) > 0): ?>
                                        <div class="list-group list-group-flush">
                                            <?php while($komik = mysqli_fetch_assoc($popular_komik)): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $komik['judul']; ?></h6>
                                                        <small class="text-muted"><?php echo $komik['penulis']; ?></small>
                                                    </div>
                                                    <span class="badge bg-warning"><?php echo $komik['views']; ?> views</span>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-book fa-3x mb-3"></i>
                                            <p>Belum ada komik</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="mb-4">Quick Actions</h5>
                                    <div class="btn-group flex-wrap" role="group">
                                        <a href="upload-chapter.php" class="btn btn-success m-1">
                                            <i class="fas fa-file-upload"></i> Upload Chapter Baru
                                        </a>
                                        <a href="komik.php" class="btn btn-primary m-1">
                                            <i class="fas fa-plus-circle"></i> Tambah Komik Baru
                                        </a>
                                        <a href="upload.php" class="btn btn-warning m-1">
                                            <i class="fas fa-image"></i> Upload Cover
                                        </a>
                                        <a href="petunjuk.php" class="btn btn-info m-1">
                                            <i class="fas fa-graduation-cap"></i> Petunjuk Admin
                                        </a>
                                        <a href="../index.php" class="btn btn-outline-primary m-1" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Visit Website
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>