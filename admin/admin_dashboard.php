<?php
session_start();
// เช็กสิทธิ์ ถ้าไม่ใช่แอดมินให้เด้งกลับไปโฟลเดอร์หลัก (../) ที่ไฟล์ login.php
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { 
    header("Location: ../login.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .admin-nav { background: #212529; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .nav-logo { font-weight: bold; letter-spacing: 1px; }
        .nav-logo span { color: #D4AF37; }
        .logout-btn { border: 1px solid #D4AF37; color: #D4AF37; text-decoration: none; padding: 5px 20px; border-radius: 20px; transition: 0.3s; }
        .logout-btn:hover { background: #D4AF37; color: #000; }
        .content-body { padding: 50px 20px; max-width: 1200px; margin: auto; }
        .welcome-text { margin-bottom: 30px; border-left: 5px solid #D4AF37; padding-left: 15px; }
        .manage-card { border: none; border-radius: 10px; transition: 0.3s; color: white; height: 100%; }
        .manage-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .card-icon { font-size: 2.5rem; opacity: 0.3; position: absolute; right: 20px; top: 20px; }
        .btn-manage { background: rgba(0,0,0,0.2); border: none; color: white; width: 100%; margin-top: 15px; padding: 10px; border-radius: 5px; }
        .btn-manage:hover { background: rgba(0,0,0,0.4); color: white; }
    </style>
</head>
<body>

    <div class="admin-nav shadow-sm">
        <div class="nav-logo">ETERNAL<span>LOVE</span> ADMIN</div>
        <div class="d-flex align-items-center">
            <span class="me-3 text-white-50"><i class="fa-solid fa-user-gear"></i> คุณ <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
            <a href="../logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>
    </div>

    <div class="content-body">
        <div class="welcome-text">
            <h2>ยินดีต้อนรับ คุณ <?= htmlspecialchars($_SESSION['admin_name']) ?></h2>
            <p class="text-muted">เลือกเมนูที่คุณต้องการจัดการข้อมูลในหน้าเว็บไซต์</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card manage-card" style="background: #f1c40f;">
                    <div class="card-body">
                        <i class="fa-solid fa-calendar-check card-icon"></i>
                        <h5>รายการจองคิว</h5>
                        <p>ตรวจสอบและอัปเดตสถานะการจองของลูกค้า</p>
                        <a href="admin_bookings.php" class="btn btn-manage">ไปจัดการการจอง</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card manage-card" style="background: #3498db;">
                    <div class="card-body">
                        <i class="fa-solid fa-bell card-icon"></i>
                        <h5>จัดการบริการ</h5>
                        <p>แก้ไขข้อมูล Our Services บนหน้าเว็บ</p>
                        <a href="admin_services.php" class="btn btn-manage">ไปจัดการบริการ</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card manage-card" style="background: #9b59b6;">
                    <div class="card-body">
                        <i class="fa-solid fa-gem card-icon"></i>
                        <h5>จัดการแพ็กเกจ</h5>
                        <p>เพิ่ม/ลบ แพ็กเกจแต่งงานและราคา</p>
                        <a href="admin_packages.php" class="btn btn-manage">ไปจัดการแพ็กเกจ</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card manage-card" style="background: #e67e22;">
                    <div class="card-body">
                        <i class="fa-solid fa-star card-icon"></i>
                        <h5>จัดการรีวิว</h5>
                        <p>คัดเลือกความประทับใจลูกค้ามาโชว์</p>
                        <a href="admin_reviews.php" class="btn btn-manage">ไปจัดการรีวิว</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card manage-card" style="background: #2ecc71;">
                    <div class="card-body">
                        <i class="fa-solid fa-images card-icon"></i>
                        <h5>จัดการแกลเลอรี</h5>
                        <p>อัปโหลดรูปภาพผลงานใหม่ๆ</p>
                        <a href="admin_gallery.php" class="btn btn-manage">ไปจัดการรูปภาพ</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card manage-card" style="background: #1abc9c;">
                    <div class="card-body">
                        <i class="fa-solid fa-envelope card-icon"></i>
                        <h5>ข้อความจากลูกค้า</h5>
                        <p>ดูข้อมูลติดต่อจากหน้า Contact Us</p>
                        <a href="admin_contact.php" class="btn btn-manage">ไปดูข้อความ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>