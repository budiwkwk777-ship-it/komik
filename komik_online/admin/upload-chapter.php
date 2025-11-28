<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Handle chapter upload
if(isset($_POST['upload_chapter'])) {
    $komik_id = $_POST['komik_id'];
    $chapter_number = $_POST['chapter_number'];
    $judul_chapter = mysqli_real_escape_string($koneksi, $_POST['judul_chapter']);
    
    // Generate slug
    $slug = generateSlug($judul_chapter . '-chapter-' . $chapter_number);
    
    // Insert chapter
    $query = "INSERT INTO chapter (komik_id, chapter_number, judul_chapter, slug) 
              VALUES ('$komik_id', '$chapter_number', '$judul_chapter', '$slug')";
    
    if(mysqli_query($koneksi, $query)) {
        $chapter_id = mysqli_insert_id($koneksi);
        
        // Handle file uploads
        $files = $_FILES['chapter_pages'];
        $uploaded_count = 0;
        
        // Get komik data for folder
        $komik_query = mysqli_query($koneksi, "SELECT slug FROM komik WHERE id = $komik_id");
        $komik_data = mysqli_fetch_assoc($komik_query);
        $komik_slug = $komik_data['slug'];
        
        $folder_path = "../assets/images/pages/{$komik_slug}/{$slug}/";
        
        // Create directory
        if(!is_dir($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        
        // Upload each page
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
                        $image_path = "{$komik_slug}/{$slug}/{$filename}";
                        mysqli_query($koneksi, 
                            "INSERT INTO chapter_pages (chapter_id, page_number, image_path) 
                             VALUES ('$chapter_id', '$page_number', '$image_path')");
                        $uploaded_count++;
                    }
                }
            }
        }
        
        // Update total pages in chapter
        mysqli_query($koneksi, "UPDATE chapter SET total_pages = $uploaded_count WHERE id = $chapter_id");
        
        $success = "Chapter berhasil diupload! {$uploaded_count} halaman terupload.";
    } else {
        $error = "Gagal membuat chapter!";
    }
}

// Function to generate slug
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'chapter-' . time();
    }
    
    return $text;
}

// Get komik list
$komik_list = mysqli_query($koneksi, "SELECT * FROM komik ORDER BY judul");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Chapter - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .page-preview {
            max-height: 150px;
            object-fit: contain;
            margin: 5px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            padding: 5px;
            background: #f8f9fa;
        }
        .file-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
        }
        #fileList {
            min-height: 100px;
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-upload"></i> Upload Chapter
            </span>
            <div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">Dashboard</a>
                <a href="chapter.php" class="btn btn-primary btn-sm me-2">Kelola Chapter</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="fas fa-file-upload"></i> Upload Chapter Baru</h3>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Chapter Baru</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" id="uploadForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pilih Komik</label>
                                <select name="komik_id" class="form-select" required id="komikSelect">
                                    <option value="">-- Pilih Komik --</option>
                                    <?php while($komik = mysqli_fetch_assoc($komik_list)): ?>
                                        <option value="<?php echo $komik['id']; ?>">
                                            <?php echo $komik['judul']; ?> (<?php echo $komik['kategori']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nomor Chapter</label>
                                <input type="number" name="chapter_number" class="form-control" 
                                       step="0.1" min="0.1" required placeholder="Contoh: 1.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Judul Chapter</label>
                                <input type="text" name="judul_chapter" class="form-control" 
                                       required placeholder="Contoh: Pertemuan Pertama">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Halaman Chapter</label>
                        <input type="file" name="chapter_pages[]" class="form-control" 
                               multiple accept="image/*" required id="fileInput">
                        <div class="form-text">
                            Pilih semua halaman chapter sekaligus. File akan diurutkan secara otomatis berdasarkan nama file.
                        </div>
                    </div>

                    <!-- File Preview & Order -->
                    <div class="mb-3">
                        <label class="form-label">Preview & Urutan Halaman</label>
                        <div id="fileList" class="d-flex flex-wrap">
                            <div class="text-muted text-center w-100 py-4" id="emptyMessage">
                                <i class="fas fa-folder-open fa-3x mb-2"></i><br>
                                Belum ada file yang dipilih
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="upload_chapter" class="btn btn-success btn-lg">
                        <i class="fas fa-upload"></i> Upload Chapter
                    </button>
                </form>
            </div>
        </div>

        <!-- Upload Guide -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Panduan Upload Chapter</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Tips Upload:</h6>
                        <ol>
                            <li><strong>Urutan File:</strong> Sistem akan mengurutkan halaman berdasarkan nama file</li>
                            <li><strong>Penamaan File:</strong> Gunakan nama seperti: <code>page-001.jpg, page-002.jpg</code></li>
                            <li><strong>Format:</strong> JPG, PNG, GIF (Recommended: JPG)</li>
                            <li><strong>Ukuran:</strong> Optimal 800-1200px width</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6>Contoh Struktur:</h6>
                        <pre class="bg-light p-3 rounded">
/one-piece/
  /chapter-1-romance-dawn/
    ├── page-001.jpg
    ├── page-002.jpg
    ├── page-003.jpg
    └── page-004.jpg</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File preview and ordering
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileList');
            const emptyMessage = document.getElementById('emptyMessage');
            const files = e.target.files;
            
            fileList.innerHTML = '';
            
            if (files.length === 0) {
                fileList.innerHTML = '<div class="text-muted text-center w-100 py-4" id="emptyMessage">' +
                    '<i class="fas fa-folder-open fa-3x mb-2"></i><br>Belum ada file yang dipilih</div>';
                return;
            }
            
            // Sort files by name
            const sortedFiles = Array.from(files).sort((a, b) => {
                return a.name.localeCompare(b.name, undefined, {numeric: true});
            });
            
            sortedFiles.forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item col-3';
                    fileItem.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" class="page-preview" alt="${file.name}">
                            <div class="small text-truncate mt-1" title="${file.name}">
                                <strong>${index + 1}.</strong> ${file.name}
                            </div>
                            <div class="small text-muted">
                                ${(file.size / 1024 / 1024).toFixed(2)} MB
                            </div>
                        </div>
                    `;
                    fileList.appendChild(fileItem);
                };
                
                reader.readAsDataURL(file);
            });
        });

        // Auto-fill chapter title based on komik
        document.getElementById('komikSelect').addEventListener('change', function() {
            const komikName = this.options[this.selectedIndex].text;
            const chapterTitle = document.querySelector('input[name="judul_chapter"]');
            
            if (!chapterTitle.value) {
                chapterTitle.placeholder = `Chapter ${komikName}`;
            }
        });
    </script>
</body>
</html>