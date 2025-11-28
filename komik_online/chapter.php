<?php
include 'config/database.php';

$komik_slug = $_GET['komik'] ?? '';

// Ambil data komik
$query_komik = "SELECT * FROM komik WHERE slug = '$komik_slug'";
$result_komik = mysqli_query($koneksi, $query_komik);
$komik = mysqli_fetch_assoc($result_komik);

if (!$komik) {
    die("Komik tidak ditemukan!");
}

// Update views
mysqli_query($koneksi, "UPDATE komik SET views = views + 1 WHERE id = {$komik['id']}");

// Ambil chapter
$query_chapter = "SELECT * FROM chapter WHERE komik_id = {$komik['id']} ORDER BY chapter_number ASC";
$chapters = mysqli_query($koneksi, $query_chapter);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $komik['judul']; ?> - KomikOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cover-detail {
            height: 350px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-arrow-left"></i> KomikOnline
            </a>
            <span class="navbar-text"><?php echo $komik['judul']; ?></span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <img src="assets/images/covers/<?php echo $komik['cover']; ?>" 
                     class="img-fluid rounded shadow cover-detail" 
                     alt="<?php echo $komik['judul']; ?>"
                     onerror="this.src='assets/images/covers/default.jpg'">
            </div>
            <div class="col-md-8">
                <h2><?php echo $komik['judul']; ?></h2>
                <p><strong><i class="fas fa-user"></i> Penulis:</strong> <?php echo $komik['penulis']; ?></p>
                <p><strong><i class="fas fa-info-circle"></i> Status:</strong> 
                    <span class="badge bg-<?php echo $komik['status'] == 'Ongoing' ? 'warning' : 'success'; ?>">
                        <?php echo $komik['status']; ?>
                    </span>
                </p>
                <p><strong><i class="fas fa-tags"></i> Genre:</strong> <?php echo $komik['genre']; ?></p>
                <p><strong><i class="fas fa-book"></i> Kategori:</strong> 
                    <span class="badge bg-<?php 
                        echo $komik['kategori'] == 'Manga' ? 'danger' : 
                             ($komik['kategori'] == 'Manhwa' ? 'success' : 'warning'); 
                    ?>">
                        <?php echo $komik['kategori']; ?>
                    </span>
                </p>
                <p><strong><i class="fas fa-eye"></i> Views:</strong> <?php echo $komik['views']; ?></p>
                <p><strong><i class="fas fa-file-alt"></i> Sinopsis:</strong></p>
                <p class="text-muted"><?php echo $komik['sinopsis']; ?></p>
            </div>
        </div>

        <hr class="my-4">

        <h4><i class="fas fa-list"></i> Daftar Chapter</h4>
        <div class="list-group">
            <?php while($chapter = mysqli_fetch_assoc($chapters)): ?>
            <a href="baca.php?chapter=<?php echo $chapter['slug']; ?>" 
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Chapter <?php echo $chapter['chapter_number']; ?></h6>
                    <p class="mb-1 text-muted"><?php echo $chapter['judul_chapter']; ?></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary rounded-pill"><?php echo $chapter['views']; ?> views</span>
                    <br>
                    <small class="text-muted"><?php echo date('d M Y', strtotime($chapter['created_at'])); ?></small>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>