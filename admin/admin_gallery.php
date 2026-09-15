<?php 
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }
include('../config.php'); 

if (isset($_POST['upload_image'])) {
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $target_dir = "../uploads/gallery/";
    
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    $file_extension = pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO gallery (image_path, caption) VALUES ('$new_filename', '$caption')";
        mysqli_query($conn, $sql);
        echo "<script>alert('อัปโหลดสำเร็จ!'); window.location='admin_gallery.php';</script>";
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $res = mysqli_query($conn, "SELECT image_path FROM gallery WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    if ($row) { @unlink("../uploads/gallery/" . $row['image_path']); }
    mysqli_query($conn, "DELETE FROM gallery WHERE id = $id");
    header("Location: admin_gallery.php");
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการแกลเลอรี - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }
        .gallery-item { position: relative; border-radius: 10px; overflow: hidden; height: 200px; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
        .delete-btn { position: absolute; top: 10px; right: 10px; background: rgba(220, 53, 69, 0.9); color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">จัดการแกลเลอรีรูปภาพ</h3>
        <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">กลับหน้าหลัก</a>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-5"><input type="file" name="image_file" class="form-control" required accept="image/*"></div>
                <div class="col-md-5"><input type="text" name="caption" class="form-control" placeholder="คำอธิบายภาพ (ถ้ามี)"></div>
                <div class="col-md-2"><button type="submit" name="upload_image" class="btn btn-primary w-100">อัปโหลด</button></div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM gallery ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)):
        ?>
        <div class="col-md-3">
            <div class="gallery-item shadow-sm">
                <img src="../uploads/gallery/<?= $row['image_path'] ?>" alt="Gallery Image">
                <a href="?delete_id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('ลบรูปนี้ใช่ไหม?')"><i class="fa-solid fa-trash"></i></a>
                <div class="p-2 bg-white small text-truncate"><?= htmlspecialchars($row['caption']) ?></div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>