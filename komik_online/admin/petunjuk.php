<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petunjuk Admin - KomikOnline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .guide-section {
            margin-bottom: 40px;
        }
        .step-card {
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .step-number {
            background: #007bff;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .warning-card {
            border-left: 4px solid #dc3545;
            background: #fff5f5;
        }
        .tip-card {
            border-left: 4px solid #28a745;
            background: #f8fff9;
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-book"></i> Petunjuk Admin
            </span>
            <div>
                <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">Dashboard</a>
                <a href="komik.php" class="btn btn-primary btn-sm me-2">Kelola Komik</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-5"><i class="fas fa-graduation-cap"></i> Petunjuk Admin KomikOnline</h1>

        <!-- Quick Navigation -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <a href="#upload-cover" class="card text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-image fa-3x text-primary mb-3"></i>
                        <h5>Upload Cover</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="#upload-chapter" class="card text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-file-upload fa-3x text-success mb-3"></i>
                        <h5>Upload Chapter</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="#edit-chapter" class="card text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-edit fa-3x text-warning mb-3"></i>
                        <h5>Edit Chapter</h5>
                    </div>
                </a>
            </div>
        </div>

        <!-- Upload Cover Section -->
        <section id="upload-cover" class="guide-section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-image"></i> Petunjuk Upload Cover Komik</h4>
                </div>
                <div class="card-body">
                    <!-- Step 1 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">1</div>
                                <h5 class="mb-0">Pilih Komik</h5>
                            </div>
                            <p>Pilih komik yang ingin ditambahkan cover dari dropdown list.</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Tips:</strong> Pastikan komik sudah terdaftar di sistem sebelum upload cover.
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">2</div>
                                <h5 class="mb-0">Pilih File Cover</h5>
                            </div>
                            <p>Klik "Choose File" dan pilih gambar cover yang ingin diupload.</p>
                            <div class="code-block">
                                <strong>Format yang didukung:</strong> JPG, JPEG, PNG, GIF<br>
                                <strong>Ukuran optimal:</strong> 300x400 pixels<br>
                                <strong>Ukuran file maksimal:</strong> 2MB
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">3</div>
                                <h5 class="mb-0">Upload Cover</h5>
                            </div>
                            <p>Klik tombol "Upload Cover" untuk menyimpan cover ke sistem.</p>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                <strong>Success:</strong> Jika berhasil, akan muncul notifikasi "Cover berhasil diupload!"
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="card warning-card">
                        <div class="card-body">
                            <h5><i class="fas fa-exclamation-triangle"></i> Hal Penting:</h5>
                            <ul>
                                <li>Nama file akan diubah otomatis menjadi: <code>cover_[id]_[timestamp].jpg</code></li>
                                <li>Cover akan disimpan di folder: <code>assets/images/covers/</code></li>
                                <li>Jika cover gagal diupload, sistem akan menggunakan default cover</li>
                                <li>Pastikan gambar tidak mengandung konten yang dilindungi hak cipta</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Upload Chapter Section -->
        <section id="upload-chapter" class="guide-section">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-file-upload"></i> Petunjuk Upload Chapter Baru</h4>
                </div>
                <div class="card-body">
                    <!-- Step 1 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">1</div>
                                <h5 class="mb-0">Pilih Komik</h5>
                            </div>
                            <p>Pilih komik yang ingin ditambahkan chapter baru.</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Tips:</strong> Pastikan komik sudah memiliki cover sebelum menambah chapter.
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">2</div>
                                <h5 class="mb-0">Isi Informasi Chapter</h5>
                            </div>
                            <p>Isi detail chapter dengan benar:</p>
                            <div class="code-block">
                                <strong>Nomor Chapter:</strong> Bisa menggunakan angka desimal (contoh: 1.5 untuk special chapter)<br>
                                <strong>Judul Chapter:</strong> Judul yang deskriptif untuk chapter<br>
                                <strong>Slug:</strong> Akan digenerate otomatis dari judul
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">3</div>
                                <h5 class="mb-0">Upload Halaman</h5>
                            </div>
                            <p>Pilih semua file halaman chapter sekaligus.</p>
                            <div class="code-block">
                                <strong>Cara upload multiple:</strong> Tekan Ctrl + Klik untuk memilih beberapa file<br>
                                <strong>Urutan file:</strong> Sistem akan mengurutkan berdasarkan nama file<br>
                                <strong>Format yang didukung:</strong> JPG, JPEG, PNG, GIF<br>
                                <strong>Ukuran optimal:</strong> 800-1200px width
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">4</div>
                                <h5 class="mb-0">Preview & Upload</h5>
                            </div>
                            <p>Periksa preview halaman dan klik "Upload Chapter".</p>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                <strong>Success:</strong> Jika berhasil, akan muncul notifikasi jumlah halaman yang terupload.
                            </div>
                        </div>
                    </div>

                    <!-- File Naming Tips -->
                    <div class="card tip-card">
                        <div class="card-body">
                            <h5><i class="fas fa-lightbulb"></i> Tips Penamaan File:</h5>
                            <p>Gunakan pola penamaan yang konsisten untuk memudahkan pengurutan:</p>
                            <div class="code-block">
                                <strong>Recommended:</strong><br>
                                page-001.jpg, page-002.jpg, page-003.jpg<br><br>
                                
                                <strong>Alternative:</strong><br>
                                001.jpg, 002.jpg, 003.jpg<br><br>
                                
                                <strong>Hindari:</strong><br>
                                image1.jpg, img2.jpg, photo3.jpg (tidak konsisten)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Edit Chapter Section -->
        <section id="edit-chapter" class="guide-section">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4><i class="fas fa-edit"></i> Petunjuk Edit & Manage Chapter</h4>
                </div>
                <div class="card-body">
                    <!-- Manage Chapters -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">1</div>
                                <h5 class="mb-0">Akses Daftar Chapter</h5>
                            </div>
                            <p>Buka menu "Kelola Chapter" untuk melihat semua chapter yang ada.</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Tabel akan menampilkan informasi lengkap setiap chapter termasuk jumlah halaman.
                            </div>
                        </div>
                    </div>

                    <!-- Edit Chapter -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">2</div>
                                <h5 class="mb-0">Edit Chapter</h5>
                            </div>
                            <p>Klik tombol edit (ikon pensil) pada chapter yang ingin diubah.</p>
                            <div class="code-block">
                                <strong>Yang bisa diedit:</strong><br>
                                • Judul chapter<br>
                                • Nomor chapter<br>
                                • Halaman (dengan upload ulang)<br>
                                • Status chapter
                            </div>
                        </div>
                    </div>

                    <!-- Add Pages -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">3</div>
                                <h5 class="mb-0">Tambah Halaman ke Chapter Existing</h5>
                            </div>
                            <p>Gunakan menu "Upload Gambar" untuk menambah halaman ke chapter yang sudah ada.</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Perhatian:</strong> Upload halaman baru akan menggantikan halaman yang sudah ada dengan nomor yang sama.
                            </div>
                        </div>
                    </div>

                    <!-- Delete Chapter -->
                    <div class="card step-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">4</div>
                                <h5 class="mb-0">Hapus Chapter</h5>
                            </div>
                            <p>Klik tombol hapus (ikon trash) dan konfirmasi penghapusan.</p>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> 
                                <strong>Peringatan:</strong> Chapter yang dihapus tidak dapat dikembalikan! Semua halaman juga akan terhapus.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Links -->
        <div class="card">
            <div class="card-body text-center">
                <h5 class="mb-4">Akses Cepat</h5>
                <div class="btn-group flex-wrap" role="group">
                    <a href="komik.php" class="btn btn-primary m-1">
                        <i class="fas fa-book"></i> Kelola Komik
                    </a>
                    <a href="upload-chapter.php" class="btn btn-success m-1">
                        <i class="fas fa-file-upload"></i> Upload Chapter
                    </a>
                    <a href="upload.php" class="btn btn-warning m-1">
                        <i class="fas fa-upload"></i> Upload Gambar
                    </a>
                    <a href="chapter.php" class="btn btn-info m-1">
                        <i class="fas fa-list"></i> Kelola Chapter
                    </a>
                    <a href="dashboard.php" class="btn btn-secondary m-1">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>