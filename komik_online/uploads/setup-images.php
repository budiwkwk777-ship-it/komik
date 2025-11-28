<?php
echo "<h2>Setup Images Structure</h2>";

// Create directories
$directories = [
    'assets/images/covers',
    'assets/images/pages',
    'assets/images/pages/one-piece/chapter-1',
    'assets/images/pages/one-piece/chapter-2',
    'assets/images/pages/naruto/chapter-1',
    'assets/images/pages/attack-on-titan/chapter-1',
    'assets/images/pages/demon-slayer/chapter-1',
    'assets/images/pages/my-hero-academia/chapter-1'
];

foreach($directories as $dir) {
    if(!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "<div style='color: green;'>✓ Created directory: $dir</div>";
    } else {
        echo "<div style='color: blue;'>✓ Directory exists: $dir</div>";
    }
}

// Create default cover if not exists
$default_cover = 'assets/images/covers/default.jpg';
if(!file_exists($default_cover)) {
    $im = imagecreate(300, 400);
    $bg = imagecolorallocate($im, 52, 152, 219);
    $text_color = imagecolorallocate($im, 255, 255, 255);
    imagestring($im, 5, 80, 150, 'KOMIK', $text_color);
    imagestring($im, 5, 60, 180, 'ONLINE', $text_color);
    imagestring($im, 2, 90, 220, 'COVER IMAGE', $text_color);
    imagejpeg($im, $default_cover);
    imagedestroy($im);
    echo "<div style='color: green;'>✓ Created default cover image</div>";
}

// Create default page if not exists
$default_page = 'assets/images/pages/default-page.jpg';
if(!file_exists($default_page)) {
    $im = imagecreate(800, 1200);
    $bg = imagecolorallocate($im, 240, 240, 240);
    $text_color = imagecolorallocate($im, 100, 100, 100);
    $border_color = imagecolorallocate($im, 200, 200, 200);
    
    // Add border
    imagerectangle($im, 0, 0, 799, 1199, $border_color);
    
    // Add text
    imagestring($im, 5, 300, 500, 'KOMIK PAGE', $text_color);
    imagestring($im, 3, 320, 530, 'Coming Soon', $text_color);
    imagestring($im, 2, 250, 580, 'Image will be available soon', $text_color);
    
    imagejpeg($im, $default_page);
    imagedestroy($im);
    echo "<div style='color: green;'>✓ Created default page image</div>";
}

echo "<hr>";
echo "<h3>Setup Complete!</h3>";
echo "<p>Folder structure and default images have been created.</p>";
echo "<div class='mt-3'>";
echo "<a href='index.php' class='btn btn-primary'>Go to Website</a> ";
echo "<a href='admin/login.php' class='btn btn-secondary'>Admin Panel</a>";
echo "</div>";
?>