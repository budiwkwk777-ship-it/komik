<?php 
include 'config/database.php';

// Ambil komik terpopuler
$query_populer = "SELECT * FROM komik ORDER BY views DESC LIMIT 8";
$komik_populer = mysqli_query($koneksi, $query_populer);

// Ambil komik terbaru
$query_terbaru = "
    SELECT k.*, c.chapter_number, c.judul_chapter, c.slug as chapter_slug, c.created_at 
    FROM komik k 
    LEFT JOIN chapter c ON k.id = c.komik_id 
    WHERE c.id IS NOT NULL
    ORDER BY c.created_at DESC 
    LIMIT 8
";
$komik_terbaru = mysqli_query($koneksi, $query_terbaru);

// Ambil komik per kategori
$manga_list = mysqli_query($koneksi, "SELECT * FROM komik WHERE kategori = 'Manga' ORDER BY views DESC LIMIT 6");
$manhwa_list = mysqli_query($koneksi, "SELECT * FROM komik WHERE kategori = 'Manhwa' ORDER BY views DESC LIMIT 6");
$manhua_list = mysqli_query($koneksi, "SELECT * FROM komik WHERE kategori = 'Manhua' ORDER BY views DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KomikOnline - Baca Manga, Manhwa, Manhua Gratis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .komik-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .komik-card:hover {
            transform: translateY(-5px);
        }
        .cover-komik {
            height: 300px;
            object-fit: cover;
            width: 100%;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 2;
        }
        .category-section {
            margin: 50px 0;
        }
        .category-title {
            border-left: 5px solid;
            padding-left: 15px;
            margin-bottom: 25px;
        }
        .manga-title { border-color: #dc3545; color: #dc3545; }
        .manhwa-title { border-color: #28a745; color: #28a745; }
        .manhua-title { border-color: #ffc107; color: #ffc107; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-book"></i> KomikOnline
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            Kategori
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="kategori.php?type=manga">Manga</a></li>
                            <li><a class="dropdown-item" href="kategori.php?type=manhwa">Manhwa</a></li>
                            <li><a class="dropdown-item" href="kategori.php?type=manhua">Manhua</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="komik.php">Daftar Komik</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <a class="nav-link" href="admin/login.php">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Baca Manga, Manhwa, Manhua Gratis</h1>
            <p class="lead">Nikmati berbagai koleksi komik dari Jepang, Korea, dan China dengan kualitas terbaik</p>
            <div class="mt-4">
                <a href="kategori.php?type=manga" class="btn btn-light btn-lg me-2">Manga</a>
                <a href="kategori.php?type=manhwa" class="btn btn-light btn-lg me-2">Manhwa</a>
                <a href="kategori.php?type=manhua" class="btn btn-light btn-lg">Manhua</a>
            </div>
        </div>
    </section>

    <!-- Komik Populer -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-fire text-danger"></i> Komik Terpopuler</h2>
            <div class="row">
                <?php while($komik = mysqli_fetch_assoc($komik_populer)): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card komik-card shadow position-relative">
                        <span class="category-badge badge bg-<?php 
                            echo $komik['kategori'] == 'Manga' ? 'danger' : 
                                 ($komik['kategori'] == 'Manhwa' ? 'success' : 'warning'); 
                        ?>">
                            <?php echo $komik['kategori']; ?>
                        </span>
                        <img src="assets/images/covers/<?php echo $komik['cover'] ?: 'default.jpg'; ?>" 
                             class="card-img-top cover-komik" 
                             alt="<?php echo $komik['judul']; ?>"
                             onerror="this.src='assets/images/covers/default.jpg'">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo $komik['judul']; ?></h6>
                            <p class="card-text small text-muted">
                                <i class="fas fa-eye"></i> <?php echo $komik['views']; ?> views • 
                                <?php echo $komik['status']; ?>
                            </p>
                            <a href="chapter.php?komik=<?php echo $komik['slug']; ?>" 
                               class="btn btn-primary btn-sm w-100">Baca Komik</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Kategori Manga -->
    <section class="category-section">
        <div class="container">
            <h3 class="category-title manga-title">
                <i class="fas fa-globe-asia"></i> Manga Jepang
            </h3>
            <div class="row">
                <?php while($manga = mysqli_fetch_assoc($manga_list)): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="card komik-card h-100 shadow-sm">
                        <img src="assets/images/covers/<?php echo $manga['cover'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo $manga['judul']; ?>"
                             style="height: 200px; object-fit: cover;"
                             onerror="this.src='assets/images/covers/default.jpg'">
                        <div class="card-body p-2">
                            <h6 class="card-title small" style="height: 40px; overflow: hidden;">
                                <?php echo $manga['judul']; ?>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye"></i> <?php echo $manga['views']; ?>
                                </small>
                                <a href="chapter.php?komik=<?php echo $manga['slug']; ?>" 
                                   class="btn btn-primary btn-sm">Baca</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-3">
                <a href="kategori.php?type=manga" class="btn btn-outline-danger">Lihat Semua Manga</a>
            </div>
        </div>
    </section>

    <!-- Kategori Manhwa -->
    <section class="category-section bg-light py-5">
        <div class="container">
            <h3 class="category-title manhwa-title">
                <i class="fas fa-landmark"></i> Manhwa Korea
            </h3>
            <div class="row">
                <?php while($manhwa = mysqli_fetch_assoc($manhwa_list)): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="card komik-card h-100 shadow-sm">
                        <img src="assets/images/covers/<?php echo $manhwa['cover'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo $manhwa['judul']; ?>"
                             style="height: 200px; object-fit: cover;"
                             onerror="this.src='assets/images/covers/default.jpg'">
                        <div class="card-body p-2">
                            <h6 class="card-title small" style="height: 40px; overflow: hidden;">
                                <?php echo $manhwa['judul']; ?>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye"></i> <?php echo $manhwa['views']; ?>
                                </small>
                                <a href="chapter.php?komik=<?php echo $manhwa['slug']; ?>" 
                                   class="btn btn-primary btn-sm">Baca</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-3">
                <a href="kategori.php?type=manhwa" class="btn btn-outline-success">Lihat Semua Manhwa</a>
            </div>
        </div>
    </section>

    <!-- Kategori Manhua -->
    <section class="category-section py-5">
        <div class="container">
            <h3 class="category-title manhua-title">
                <i class="fas fa-dragon"></i> Manhua China
            </h3>
            <div class="row">
                <?php while($manhua = mysqli_fetch_assoc($manhua_list)): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="card komik-card h-100 shadow-sm">
                        <img src="assets/images/covers/<?php echo $manhua['cover'] ?: 'default.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo $manhua['judul']; ?>"
                             style="height: 200px; object-fit: cover;"
                             onerror="this.src='assets/images/covers/default.jpg'">
                        <div class="card-body p-2">
                            <h6 class="card-title small" style="height: 40px; overflow: hidden;">
                                <?php echo $manhua['judul']; ?>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye"></i> <?php echo $manhua['views']; ?>
                                </small>
                                <a href="chapter.php?komik=<?php echo $manhua['slug']; ?>" 
                                   class="btn btn-primary btn-sm">Baca</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-3">
                <a href="kategori.php?type=manhua" class="btn btn-outline-warning">Lihat Semua Manhua</a>
            </div>
        </div>
    </section>

    <!-- Chapter Terbaru -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-clock text-primary"></i> Update Terbaru</h2>
            <div class="row">
                <?php while($update = mysqli_fetch_assoc($komik_terbaru)): ?>
                <div class="col-md-6 mb-3">
                    <div class="card bg-dark border-light">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-white"><?php echo $update['judul']; ?></h6>
                                    <small class="text-light">
                                        Chapter <?php echo $update['chapter_number']; ?> - <?php echo $update['judul_chapter']; ?>
                                    </small>
                                    <br>
                                    <small class="text-light">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('d M Y', strtotime($update['created_at'])); ?>
                                    </small>
                                </div>
                                <a href="baca.php?chapter=<?php echo $update['chapter_slug']; ?>" 
                                   class="btn btn-primary btn-sm">Baca</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 KomikOnline. All rights reserved.</p>
            <p class="mb-0">Website baca Manga, Manhwa, Manhua online gratis</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>