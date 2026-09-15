<?php
session_start();
// 1. เชื่อมต่อฐานข้อมูล (ปรับ Path ให้ชัวร์)
if (file_exists('config.php')) {
    include('config.php');
} else {
    die("ไม่พบไฟล์ config.php กรุณาตรวจสอบว่าไฟล์อยู่ในโฟลเดอร์ Project หรือไม่");
}

$error_msg = "";

// 2. ส่วนประมวลผลการเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    if (isset($conn)) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // เก็บข้อมูลลง Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // แยกหน้าที่จะไปตามสิทธิ์
            if ($user['role'] == 'Admin') {
                $_SESSION['admin_name'] = $user['username'];
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: services.php");
            }
            exit();
        } else {
            $error_msg = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error_msg = "ระบบเชื่อมต่อฐานข้อมูลมีปัญหา (Variable \$conn is null)";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Sarabun:wght@200;300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body class="auth-page">
    <?php include('navbar.php'); ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card auth-card shadow-sm p-4 mt-5">
                    <div class="text-center mb-5">
                        <h2 class="auth-title">ยินดีต้อนรับ</h2>
                        <p class="text-muted small">เข้าสู่ระบบเพื่อจัดการการจองของคุณ</p>
                    </div>

                    <?php if ($error_msg != ""): ?>
                        <div class="alert alert-danger p-2 small text-center"><?php echo $error_msg; ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-4">
                            <label class="auth-label mb-1">อีเมล</label>
                            <input type="email" class="form-control auth-input" name="email" required>
                        </div>
                        <div class="mb-5">
                            <label class="auth-label mb-1">รหัสผ่าน</label>
                            <input type="password" class="form-control auth-input" name="password" required>
                        </div>
                        <button type="submit" name="login_btn" class="btn auth-btn w-100 py-3 mb-4">เข้าสู่ระบบ</button>
                    </form>

                    <div class="text-center border-top pt-4">
                        <p class="small mb-2"><a href="forgot_password.php" class="auth-link-gold">ลืมรหัสผ่านใช่ไหม?</a></p>
                        <p class="small text-muted mb-0">ยังไม่มีบัญชี? <a href="register.php" class="auth-link-dark">สมัครสมาชิกใหม่</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>