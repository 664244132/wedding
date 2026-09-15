<?php 
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }
include('../config.php'); 

if (isset($_POST['add_new_service'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $price = intval($_POST['price']);
    $new_name = "";

    if (!empty($_FILES['service_icon']['name'])) {
        $ext = pathinfo($_FILES['service_icon']['name'], PATHINFO_EXTENSION);
        $new_name = "icon_" . time() . "." . $ext;
        move_uploaded_file($_FILES['service_icon']['tmp_name'], "../img/icons/" . $new_name);
    }
    mysqli_query($conn, "INSERT INTO services (title, icon, service_type, price) VALUES ('$title', '$new_name', '$service_type', '$price')");
    header("Location: admin_services.php"); exit();
}

if (isset($_POST['update_service'])) {
    $id = intval($_POST['service_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $price = intval($_POST['price']);

    if (!empty($_FILES['service_icon']['name'])) {
        $ext = pathinfo($_FILES['service_icon']['name'], PATHINFO_EXTENSION);
        $new_name = "icon_" . time() . "." . $ext;
        move_uploaded_file($_FILES['service_icon']['tmp_name'], "../img/icons/" . $new_name);
        mysqli_query($conn, "UPDATE services SET title='$title', icon='$new_name', service_type='$service_type', price='$price' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE services SET title='$title', service_type='$service_type', price='$price' WHERE id='$id'");
    }
    header("Location: admin_services.php"); exit();
}

if (isset($_GET['delete_service'])) {
    $id = intval($_GET['delete_service']);
    $res = mysqli_query($conn, "SELECT icon FROM services WHERE id='$id'");
    if($row = mysqli_fetch_assoc($res)) { @unlink("../img/icons/" . $row['icon']); }
    mysqli_query($conn, "DELETE FROM services WHERE id='$id'");
    header("Location: admin_services.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการบริการ - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: #212529; color: white; }
        .service-icon { width: 55px; height: 55px; object-fit: cover; border-radius: 10px; border: 2px solid #fff; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-layer-group me-2"></i>ระบบจัดการบริการ</h4>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">กลับหน้าหลัก</a>
    </div>

    <div class="card main-card mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">เพิ่มบริการใหม่</h6>
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="ชื่อบริการ" required></div>
                <div class="col-md-3"><input type="file" name="service_icon" class="form-control" required></div>
                <div class="col-md-2"><input type="text" name="service_type" class="form-control" placeholder="ประเภท"></div>
                <div class="col-md-2"><input type="number" name="price" class="form-control" placeholder="ราคา"></div>
                <div class="col-md-2"><button type="submit" name="add_new_service" class="btn btn-primary w-100">บันทึก</button></div>
            </form>
        </div>
    </div>

    <div class="card main-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">รูป Icon</th>
                        <th>ข้อมูลบริการ</th>
                        <th>ราคาเริ่มต้น</th>
                        <th class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($res)):
                    ?>
                    <tr>
                        <td class="ps-4"><img src="../img/icons/<?= $row['icon'] ?>" onerror="this.src='https://placehold.co/100x100'" class="service-icon"></td>
                        <td><div class="fw-bold"><?= htmlspecialchars($row['title']) ?></div><small class="text-muted"><?= htmlspecialchars($row['service_type']) ?></small></td>
                        <td><span class="badge bg-light text-dark border">฿<?= number_format($row['price']) ?></span></td>
                        <td class="text-center">
                            <a href="admin_service_gallery.php?s_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-images"></i></a>
                            <a href="?delete_service=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ลบข้อมูลนี้?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>