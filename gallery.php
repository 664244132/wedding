<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/gallery.css?v=<?php echo time(); ?>">
</head>
<body class="gallery-page">

    <?php include('navbar.php'); ?>

    <div class="container pb-5">
        <header class="gallery-header">
            <span class="section-subtitle">Premium Pre-Wedding Studio</span>
            <h1 class="section-title">บันทึกนิยามแห่งรักนิรันดร์</h1>
        </header>

        <div class="row g-4">
            <?php
            // ดึงข้อมูลรูปภาพจากฐานข้อมูล
            $query = "SELECT * FROM gallery ORDER BY id DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // กำหนด path ของรูปภาพ
                    $img_src = "uploads/gallery/" . $row['image_path'];
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card">
                        <div class="inner-border"></div>
                        <img src="<?php echo $img_src; ?>" alt="Wedding Gallery" class="img-fluid">
                        <div class="gallery-info">
                            <h4><?php echo htmlspecialchars($row['caption'] ?: 'Eternal Love'); ?></h4>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            } else {
                // กรณีที่ยังไม่มีรูปในฐานข้อมูล
                echo '<div class="col-12 text-center py-5"><p class="text-muted">กำลังเตรียมรูปภาพสวยๆ มาให้ชมเร็วๆ นี้...</p></div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>