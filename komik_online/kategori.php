<?php
include 'config/database.php';

$category_type = $_GET['type'] ?? 'manga';
$category_name = ucfirst($category_type);

// Validasi kategori
$allowed_categories = ['manga', 'manhwa', 'manhua'];
if(!in_array($category_type, $allowed_categories)) {
    $category_type = 'manga';
    $category_name = 'Manga';
}

// Ambil komik berdasarkan kategori
$query = "SELECT * FROM komik WHERE kategori = '$category_name' ORDER BY judul ASC";
$result = mysqli_query($koneksi, $query);

// Category info
$category_info = [
    'manga' => [
        'title' => 'Manga Jepang',
        'description' => 'Komik asal Jepang dengan gaya khas dan cerita yang beragam',
        'color' => 'danger',
        'icon' => 'fas fa-globe-asia'
    ],
    'manhwa' => [
        'title' => 'Manhwa Korea', 
        'description' => 'Komik asal Korea dengan gaya full color dan cerita modern',
        'color' => 'success',
        'icon' => 'fas fa-landmark'
    ],
    'manhua' => [
        'title' => 'Manhua China',
        'description' => 'Komik asal China dengan tema cultivation dan fantasi',
        'color' => 'warning',
        'icon' => 'fas fa-dragon'
    ]
];

$current_category = $category_info[$category_type];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_category['title']; ?> - KomikOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 0;
            margin-bottom: 40px;
        }
        .komik-card {
            transition: transform 0.3s;
        }
        .komik-card:hover {
            transform: translateY(-5px);
        }
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-book"></i> KomikOnline
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="kategori.php?type=<?php echo $category_type; ?>">Kategori</a>
                <a class="nav-link" href="komik.php">Daftar Komik</a>
            </div>
        </div>
    </nav>

    <!-- Category Header -->
    <section class="category-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">
                <i class="<?php echo $current_category['icon']; ?>"></i> 
                <?php echo $current_category['title']; ?>
            </h1>
            <p class="lead"><?php echo $current_category['description']; ?></p>
            <div class="mt-3">
                <span class="badge bg-<?php echo $current_category['color']; ?> fs-6">
                    <?php echo mysqli_num_rows($result); ?> Komik Tersedia
                </span>
            </div>
        </div>
    </section>

    <!-- Category Navigation -->
    <div class="container mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="btn-group" role="group">
                            <a href="kategori.php?type=manga" 
                               class="btn btn-<?php echo $category_type == 'manga' ? 'danger' : 'outline-danger'; ?>">
                                <i class="fas fa-globe-asia"></i> Manga
                            </a>
                            <a href="kategori.php?type=manhwa" 
                               class="btn btn-<?php echo $category_type == 'manhwa' ? 'success' : 'outline-success'; ?>">
                                <i class="fas fa-landmark"></i> Manhwa
                            </a>
                            <a href="kategori.php?type=manhua" 
                               class="btn btn-<?php echo $category_type == 'manhua' ? 'warning' : 'outline-warning'; ?>">
                                <i class="fas fa-dragon"></i> Manhua
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Komik List -->
    <div class="container">
        <div class="row">
            <?php while($komik = mysqli_fetch_assoc($result)): 
                $chapter_count = mysqli_fetch_assoc(mysqli_query($koneksi, 
                    "SELECT COUNT(*) as total FROM chapter WHERE komik_id = {$komik['id']}"))['total'];
            ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                <div class="card komik-card h-100 shadow-sm position-relative">
                    <span class="category-badge badge bg-<?php echo $current_category['color']; ?>">
                        <?php echo $komik['kategori']; ?>
                    </span>
                    <img src="assets/images/covers/<?php echo $komik['cover'] ?: 'default.jpg'; ?>" 
                         class="card-img-top" 
                         alt="<?php echo $komik['judul']; ?>"
                         style="height: 250px; object-fit: cover;"
                         onerror="this.src='assets/images/covers/default.jpg'">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo $komik['judul']; ?></h6>
                        <p class="card-text small text-muted">
                            <?php echo $komik['penulis']; ?><br>
                            <i class="fas fa-file-alt"></i> <?php echo $chapter_count; ?> chapter<br>
                            <i class="fas fa-eye"></i> <?php echo $komik['views']; ?> views
                        </p>
                        <a href="chapter.php?komik=<?php echo $komik['slug']; ?>" 
                           class="btn btn-<?php echo $current_category['color']; ?> btn-sm w-100">
                            Baca Komik
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="text-center py-5">
            <i class="fas fa-book fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Belum ada komik dalam kategori ini</h4>
            <p class="text-muted">Silakan tambahkan komik melalui panel admin</p>
            <a href="admin/login.php" class="btn btn-primary">Login Admin</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 KomikOnline. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>