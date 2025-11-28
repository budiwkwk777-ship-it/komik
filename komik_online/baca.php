<?php
include 'config/database.php';

$chapter_slug = $_GET['chapter'] ?? '';

// Ambil data chapter
$query_chapter = "
    SELECT c.*, k.judul, k.slug as komik_slug 
    FROM chapter c 
    JOIN komik k ON c.komik_id = k.id 
    WHERE c.slug = '$chapter_slug'
";
$result_chapter = mysqli_query($koneksi, $query_chapter);
$chapter = mysqli_fetch_assoc($result_chapter);

if (!$chapter) {
    die("Chapter tidak ditemukan!");
}

// Update views
mysqli_query($koneksi, "UPDATE chapter SET views = views + 1 WHERE id = {$chapter['id']}");

// Ambil halaman chapter dari table chapter_pages
$query_pages = "
    SELECT * FROM chapter_pages 
    WHERE chapter_id = {$chapter['id']} 
    ORDER BY page_number ASC
";
$pages_result = mysqli_query($koneksi, $query_pages);
$pages = [];
while($page = mysqli_fetch_assoc($pages_result)) {
    $pages[] = $page;
}

// Jika tidak ada halaman, gunakan sistem lama (backward compatibility)
if(empty($pages)) {
    $images = json_decode($chapter['images'], true);
    if(empty($images) || !is_array($images)) {
        $images = ['default-page.jpg'];
    }
    
    // Convert ke format baru untuk kompatibilitas
    foreach($images as $index => $image) {
        $pages[] = [
            'page_number' => $index + 1,
            'image_path' => $image
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $chapter['judul_chapter']; ?> - KomikOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .manga-page {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 20px auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .navigation {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .chapter-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .page-navigation {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-home"></i> KomikOnline
            </a>
            <span class="navbar-text d-none d-md-block">
                <?php echo $chapter['judul']; ?> - Chapter <?php echo $chapter['chapter_number']; ?>
            </span>
            <div>
                <a href="chapter.php?komik=<?php echo $chapter['komik_slug']; ?>" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-list"></i> Daftar Chapter
                </a>
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Chapter Info -->
        <div class="chapter-info text-center">
            <h4><?php echo $chapter['judul']; ?></h4>
            <h5>Chapter <?php echo $chapter['chapter_number']; ?> - <?php echo $chapter['judul_chapter']; ?></h5>
            <p class="mb-0">
                <i class="fas fa-eye"></i> <?php echo $chapter['views']; ?> views • 
                <i class="fas fa-file-image"></i> <?php echo count($pages); ?> halaman • 
                <i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($chapter['created_at'])); ?>
            </p>
        </div>

        <!-- Page Navigation -->
        <div class="page-navigation text-center">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary" onclick="previousPage()">
                    <i class="fas fa-arrow-left"></i> Sebelumnya
                </button>
                <button type="button" class="btn btn-outline-secondary" disabled>
                    Halaman <span id="currentPage">1</span> dari <?php echo count($pages); ?>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="nextPage()">
                    Selanjutnya <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Manga Pages -->
        <div class="text-center" id="pagesContainer">
            <?php foreach($pages as $index => $page): ?>
                <?php
                $image_path = "assets/images/pages/" . $page['image_path'];
                $fallback_path = "assets/images/pages/default-page.jpg";
                ?>
                <div class="page-container mb-4" id="page-<?php echo $page['page_number']; ?>" 
                     style="<?php echo $index > 0 ? 'display: none;' : ''; ?>">
                    <p class="text-muted mb-2">Halaman <?php echo $page['page_number']; ?></p>
                    <img src="<?php echo $image_path; ?>" 
                         class="manga-page img-fluid rounded" 
                         alt="Halaman <?php echo $page['page_number']; ?>"
                         onerror="this.src='<?php echo $fallback_path; ?>'"
                         loading="lazy"
                         id="img-<?php echo $page['page_number']; ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Bottom Navigation -->
        <div class="page-navigation text-center">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary" onclick="previousPage()">
                    <i class="fas fa-arrow-left"></i> Sebelumnya
                </button>
                <button type="button" class="btn btn-outline-secondary" disabled>
                    Halaman <span id="currentPageBottom">1</span> dari <?php echo count($pages); ?>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="nextPage()">
                    Selanjutnya <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="d-flex justify-content-between mt-4 mb-5">
            <a href="chapter.php?komik=<?php echo $chapter['komik_slug']; ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Chapter
            </a>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Home
            </a>
        </div>
    </div>

    <!-- Floating Navigation -->
    <div class="navigation">
        <div class="btn-group-vertical">
            <button class="btn btn-primary btn-lg" onclick="previousPage()" title="Halaman Sebelumnya">
                <i class="fas fa-arrow-up"></i>
            </button>
            <button class="btn btn-success btn-lg" onclick="nextPage()" title="Halaman Selanjutnya">
                <i class="fas fa-arrow-down"></i>
            </button>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const totalPages = <?php echo count($pages); ?>;

        function showPage(pageNumber) {
            // Hide all pages
            document.querySelectorAll('.page-container').forEach(page => {
                page.style.display = 'none';
            });
            
            // Show selected page
            const pageElement = document.getElementById('page-' + pageNumber);
            if (pageElement) {
                pageElement.style.display = 'block';
            }
            
            // Update page indicators
            document.getElementById('currentPage').textContent = pageNumber;
            document.getElementById('currentPageBottom').textContent = pageNumber;
            
            currentPage = pageNumber;
            
            // Scroll to top of page
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function nextPage() {
            if (currentPage < totalPages) {
                showPage(currentPage + 1);
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                showPage(currentPage - 1);
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if(e.key === 'ArrowRight' || e.key === ' ') {
                nextPage();
            } else if(e.key === 'ArrowLeft') {
                previousPage();
            } else if(e.key === 'Home') {
                showPage(1);
            } else if(e.key === 'End') {
                showPage(totalPages);
            }
        });

        // Touch swipe for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });

        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextPage(); // Swipe left
                } else {
                    previousPage(); // Swipe right
                }
            }
        }

        // Initialize first page
        showPage(1);
    </script>
</body>
</html>