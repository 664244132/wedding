<?php
session_start();
require_once('config.php');

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน กรุณาลองใหม่'); window.history.back();</script>";
        exit();
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            echo "<script>alert('อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น'); window.history.back();</script>";
            exit();
        } else {
            $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'Customer')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); window.location.href='login.php';</script>";
                exit();
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Sarabun:wght@200;300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body class="auth-page">

    <?php include('navbar.php'); ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card auth-card shadow-sm">
                    <div class="text-center mb-4">
                        <h2 class="auth-title">สร้างบัญชีใหม่</h2>
                        <p class="text-muted small">กรอกข้อมูลเพื่อเริ่มต้นการเดินทางกับเรา</p>
                        <?php if($error_msg) echo "<div class='text-danger small'>$error_msg</div>"; ?>
                    </div>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="auth-label mb-1">ชื่อ-นามสกุล</label>
                            <input type="text" class="form-control auth-input" name="fullname" placeholder="ระบุชื่อจริงของคุณ" required>
                        </div>
                        <div class="mb-3">
                            <label class="auth-label mb-1">อีเมล</label>
                            <input type="email" class="form-control auth-input" name="email" placeholder="email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="auth-label mb-1">รหัสผ่าน</label>
                            <input type="password" class="form-control auth-input" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="mb-4">
                            <label class="auth-label mb-1">ยืนยันรหัสผ่าน</label>
                            <input type="password" class="form-control auth-input" name="confirm_password" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn auth-btn w-100 py-3 mb-4">ยืนยันการสมัคร</button>
                    </form>

                    <div class="text-center border-top pt-4" style="border-color: #eee !important;">
                        <p class="small text-muted mb-0">เป็นสมาชิกอยู่แล้วใช่ไหม? <a href="login.php" class="auth-link-gold fw-bold">เข้าสู่ระบบที่นี่</a></p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="index.php" class="small text-muted text-decoration-none">← กลับสู่หน้าหลัก</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>