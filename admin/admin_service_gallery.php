<?php 
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }
include('../config.php'); 

$s_id = isset($_GET['s_id']) ? intval($_GET['s_id']) : (isset($_POST['service_id']) ? intval($_POST['service_id']) : 0);

if (isset($_POST['upload_gallery'])) {
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $img_price = intval($_POST['img_price']);
    if (!empty($_FILES['gallery_img']['name'])) {
        $file_name = "gal_" . time() . "_" . basename($_FILES['gallery_img']['name']);
        if (move_uploaded_file($_FILES['gallery_img']['tmp_name'], "../img/gallery/" . $file_name)) {
            mysqli_query($conn, "INSERT INTO service_images (service_id, image_path, caption, img_price) VALUES ('$s_id', '$file_name', '$caption', '$img_price')");
        }
    }
    header("Location: admin_service_gallery.php?s_id=$s_id"); exit();
}

if (isset($_GET['delete_gallery'])) {
    $id = intval($_GET['delete_gallery']);
    $res = mysqli_query($conn, "SELECT image_path FROM service_images WHERE id = $id");
    if($row = mysqli_fetch_assoc($res)) { @unlink("../img/gallery/" . $row['image_path']); }
    mysqli_query($conn, "DELETE FROM service_images WHERE id = $id");
    header("Location: admin_service_gallery.php?s_id=$s_id"); exit();
}

$res_service = mysqli_query($conn, "SELECT title FROM services WHERE id = $s_id");
$service = mysqli_fetch_assoc($res_service);
if(!$service) { die("ไม่พบข้อมูลบริการ"); }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการแกลเลอรีบริการ - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .gallery-card { border: none; border-radius: 12px; overflow: hidden; }
        .img-container { height: 180px; overflow: hidden; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-images me-2 text-warning"></i>คอลเลกชัน: <?= $service['title'] ?></h4>
        <a href="admin_services.php" class="btn btn-outline-secondary btn-sm">กลับหน้าบริการ</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="service_id" value="<?= $s_id ?>">
                <div class="col-md-4"><input type="file" name="gallery_img" class="form-control" required></div>
                <div class="col-md-3"><input type="text" name="caption" class="form-control" placeholder="คำบรรยายภาพ"></div>
                <div class="col-md-3"><input type="number" name="img_price" class="form-control" placeholder="ราคา (ถ้ามี)"></div>
                <div class="col-md-2"><button type="submit" name="upload_gallery" class="btn btn-warning w-100">อัปโหลด</button></div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM service_images WHERE service_id = $s_id ORDER BY id DESC");
        while($img = mysqli_fetch_assoc($res)):
        ?>
        <div class="col-md-3">
            <div class="card gallery-card shadow-sm">
                <div class="img-container"><img src="../img/gallery/<?= $img['image_path'] ?>"></div>
                <div class="card-body p-3 text-center">
                    <p class="small mb-1 fw-bold"><?= $img['caption'] ?: '-' ?></p>
                    <p class="text-primary small mb-2">฿<?= number_format($img['img_price']) ?></p>
                    <a href="?delete_gallery=<?= $img['id'] ?>&s_id=<?= $s_id ?>" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('ยืนยันการลบรูปนี้?')"><i class="fa-solid fa-trash"></i> ลบ</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>