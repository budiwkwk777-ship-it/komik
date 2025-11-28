<?php
include 'config/database.php';

// Ambil semua komik
$query = "SELECT * FROM komik ORDER BY judul ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Komik - KomikOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-book"></i> KomikOnline
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="komik.php">Daftar Komik</a>
                <a class="nav-link" href="admin/login.php">Admin</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Daftar Semua Komik</h2>
        
        <div class="row">
            <?php while($komik = mysqli_fetch_assoc($result)): 
                $chapter_count = mysqli_fetch_assoc(mysqli_query($koneksi, 
                    "SELECT COUNT(*) as total FROM chapter WHERE komik_id = {$komik['id']}"))['total'];
            ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                <div class="card komik-card h-100 shadow-sm position-relative">
                    <span class="category-badge badge bg-<?php 
                        echo $komik['kategori'] == 'Manga' ? 'danger' : 
                             ($komik['kategori'] == 'Manhwa' ? 'success' : 'warning'); 
                    ?>">
                        <?php echo $komik['kategori']; ?>
                    </span>
                    <img src="assets/images/covers/<?php echo $komik['cover'] ?: 'default.jpg'; ?>" 
                         class="card-img-top" 
                         alt="<?php echo $komik['judul']; ?>"
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='assets/images/covers/default.jpg'">
                    <div class="card-body p-2">
                        <h6 class="card-title small" style="height: 40px; overflow: hidden;">
                            <?php echo $komik['judul']; ?>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> <?php echo $komik['views']; ?>
                            </small>
                            <a href="chapter.php?komik=<?php echo $komik['slug']; ?>" 
                               class="btn btn-primary btn-sm">Baca</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>