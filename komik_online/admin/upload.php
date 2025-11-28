<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Handle cover upload
if(isset($_POST['upload_cover'])) {
    $komik_id = $_POST['komik_id'];
    $file = $_FILES['cover_image'];
    
    if($file['error'] === 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'cover_' . $komik_id . '_' . time() . '.' . $ext;
        $upload_path = '../assets/images/covers/' . $filename;
        
        if(move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update database
            mysqli_query($koneksi, "UPDATE komik SET cover = '$filename' WHERE id = $komik_id");
            $success = "Cover berhasil diupload!";
        } else {
            $error = "Gagal upload cover!";
        }
    }
}

// Handle page upload untuk chapter yang sudah ada
if(isset($_POST['upload_pages'])) {
    $chapter_id = $_POST['chapter_id'];
    $files = $_FILES['page_images'];
    
    // Get chapter info
    $chapter_query = mysqli_query($koneksi, "
        SELECT c.*, k.slug as komik_slug 
        FROM chapter c 
        JOIN komik k ON c.komik_id = k.id 
        WHERE c.id = $chapter_id
    ");
    $chapter_data = mysqli_fetch_assoc($chapter_query);
    
    $folder_path = "../assets/images/pages/{$chapter_data['komik_slug']}/{$chapter_data['slug']}/";
    
    // Create directory
    if(!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    
    $uploaded_count = 0;
    $uploaded_files = [];
    
    foreach($files['tmp_name'] as $key => $tmp_name) {
        if($files['error'][$key] === 0) {
            $page_number = $key + 1;
            $ext = strtolower(pathinfo($files['name'][$key], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if(in_array($ext, $allowed_ext)) {
                $filename = "page-" . str_pad($page_number, 3, '0', STR_PAD_LEFT) . ".{$ext}";
                $upload_path = $folder_path . $filename;
                
                if(move_uploaded_file($tmp_name, $upload_path)) {
                    // Insert to chapter_pages table
                    $image_path = "{$chapter_data['komik_slug']}/{$chapter_data['slug']}/{$filename}";
                    
                    // Check if page already exists
                    $existing = mysqli_query($koneksi, 
                        "SELECT id FROM chapter_pages WHERE chapter_id = $chapter_id AND page_number = $page_number");
                    
                    if(mysqli_num_rows($existing) > 0) {
                        mysqli_query($koneksi, 
                            "UPDATE chapter_pages SET image_path = '$image_path' 
                             WHERE chapter_id = $chapter_id AND page_number = $page_number");
                    } else {
                        mysqli_query($koneksi, 
                            "INSERT INTO chapter_pages (chapter_id, page_number, image_path) 
                             VALUES ('$chapter_id', '$page_number', '$image_path')");
                    }
                    
                    $uploaded_count++;
                    $uploaded_files[] = $image_path;
                }
            }
        }
    }
    
    // Update total pages in chapter
    if($uploaded_count > 0) {
        mysqli_query($koneksi, "UPDATE chapter SET total_pages = $uploaded_count WHERE id = $chapter_id");
        $success = "{$uploaded_count} halaman berhasil diupload untuk chapter ini!";
    } else {
        $error = "Tidak ada file yang berhasil diupload!";
    }
}

// Get komik list
$komik_list = mysqli_query($koneksi, "SELECT * FROM komik ORDER BY judul");

// Get chapter list
$chapter_list = mysqli_query($koneksi, "
    SELECT c.*, k.judul as komik_judul 
    FROM chapter c 
    JOIN komik k ON c.komik_id = k.id 
    ORDER BY k.judul, c.chapter_number
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Gambar - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-upload"></i> Upload Gambar
            </span>
            <div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">Dashboard</a>
                <a href="upload-chapter.php" class="btn btn-primary btn-sm me-2">Upload Chapter</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="fas fa-upload"></i> Upload Gambar</h3>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Upload Cover -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-image"></i> Upload Cover Komik</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pilih Komik</label>
                                <select name="komik_id" class="form-select" required>
                                    <option value="">-- Pilih Komik --</option>
                                    <?php while($komik = mysqli_fetch_assoc($komik_list)): ?>
                                        <option value="<?php echo $komik['id']; ?>">
                                            <?php echo $komik['judul']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cover Image</label>
                                <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                                <div class="form-text">
                                    Format: JPG, PNG, GIF. Max size: 2MB
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="upload_cover" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Cover
                    </button>
                </form>
            </div>
        </div>

        <!-- Upload Pages -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-image"></i> Upload Halaman Chapter</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pilih Chapter</label>
                                <select name="chapter_id" class="form-select" required>
                                    <option value="">-- Pilih Chapter --</option>
                                    <?php 
                                    mysqli_data_seek($chapter_list, 0);
                                    while($chapter = mysqli_fetch_assoc($chapter_list)): ?>
                                        <option value="<?php echo $chapter['id']; ?>">
                                            <?php echo $chapter['komik_judul']; ?> - Ch. <?php echo $chapter['chapter_number']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Halaman Komik</label>
                                <input type="file" name="page_images[]" class="form-control" multiple accept="image/*" required>
                                <div class="form-text">
                                    Pilih multiple file untuk upload beberapa halaman sekaligus. 
                                    Format: JPG, PNG, GIF
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="upload_pages" class="btn btn-success">
                        <i class="fas fa-upload"></i> Upload Halaman
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Guide -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Panduan Upload</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Struktur Folder:</h6>
                        <ul>
                            <li><code>assets/images/covers/</code> - Untuk cover komik</li>
                            <li><code>assets/images/pages/komik-slug/chapter-slug/</code> - Untuk halaman</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Tips:</h6>
                        <ul>
                            <li>Gunakan nama file yang konsisten</li>
                            <li>Optimal size: Cover (300x400), Halaman (800x1200)</li>
                            <li>Format recommended: JPG/PNG</li>
                            <li>Backup gambar secara berkala</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>