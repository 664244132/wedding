<?php
/**
 * gallery.php - หน้ารวมภาพถ่ายพรีเวดดิ้งผลงานสตูดิโอ Eternal Love
 * สถาปัตยกรรม PHP Procedural + MySQLi Best Practices ตามคู่มือ markdowns/
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once('config.php');

// ดึงข้อมูลรูปภาพแกลเลอรีด้วย Explicit Columns ตาม SQLCodingGuide.md ข้อ 1
$sql = "SELECT id, image_path, caption FROM gallery ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลงานภาพถ่ายพรีเวดดิ้ง - Eternal Love</title>
    
    <!-- Bootstrap 5.3.0 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Gallery Stylesheet (ตาม CSSCodingGuide.md) -->
    <link rel="stylesheet" href="CSS/gallery.css?v=<?= time(); ?>">
</head>
<body class="gallery-page">

    <!-- แถบเมนูนำทางหลัก -->
    <?php include('navbar.php'); ?>

    <!-- ส่วนเนื้อหาหลักแบบ Semantic HTML (ตาม HTMLCodingGuide.md) -->
    <main class="container pb-5">
        <header class="gallery-header">
            <span class="section-subtitle">Premium Pre-Wedding Studio</span>
            <h1 class="section-title">บันทึกนิยามแห่งรักนิรันดร์</h1>
            <p class="text-white-50 mt-3 mx-auto" style="max-width: 650px; font-size: 0.95rem; line-height: 1.8;">
                สัมผัสความงดงามของคู่บ่าวสาวในหลากหลายสไตล์ ทั้งชุดไทยพระราชนิยมวิจิตรตระการตา ชุดวิวาห์สากลสุดคลาสสิก และพรีเวดดิ้งร่วมสมัยอันเปี่ยมด้วยมนต์เสน่ห์
            </p>
        </header>

        <div class="row g-4">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $caption = !empty($row['caption']) ? $row['caption'] : 'Eternal Love Pre-Wedding';
                    $img_src = "uploads/gallery/" . $row['image_path'];
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card rounded-3">
                        <div class="inner-border"></div>
                        <img src="<?= htmlspecialchars($img_src, ENT_QUOTES, 'UTF-8'); ?>" 
                             alt="<?= htmlspecialchars($caption, ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='https://placehold.co/600x800/1e1711/c5a059?text=Pre-Wedding+Couple';">
                        <div class="gallery-info shadow">
                            <h2 class="h6 mb-0">
                                <?= htmlspecialchars($caption, ENT_QUOTES, 'UTF-8'); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            } else {
                echo '<div class="col-12 text-center py-5"><p class="text-muted">กำลังเตรียมรูปภาพสวยๆ มาให้ชมเร็วๆ นี้...</p></div>';
            }
            ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>