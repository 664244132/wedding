<?php
/**
 * forgot_password.php - ระบบรีเซ็ตรหัสผ่าน Eternal Love Studio
 * สถาปัตยกรรม PHP Procedural + MySQLi Best Practices ตามคู่มือ markdowns/
 */

// ==========================================
// 1. DATA PREPARATION ZONE (โซนประมวลผลข้อมูล)
// ==========================================
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once('config.php');

$error_msg = "";
$success_msg = "";
$step = 1; // 1: Verify Email, 2: Reset Password
$verified_user = null;

// กรณีผู้ใช้กด "ยกเลิก / เปลี่ยนอีเมล"
if (isset($_GET['reset_flow'])) {
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_user_email']);
    unset($_SESSION['reset_user_name']);
    header("Location: forgot_password.php");
    exit();
}

// ตรวจสอบสถานะการยืนยันอีเมลใน Session
if (isset($_SESSION['reset_user_id']) && isset($_SESSION['reset_user_email'])) {
    $step = 2;
    $verified_user = [
        'id'       => $_SESSION['reset_user_id'],
        'email'    => $_SESSION['reset_user_email'],
        'username' => $_SESSION['reset_user_name'] ?? ''
    ];
}

// ประมวลผลฟอร์มเมื่อมีการส่งข้อมูล (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ----------------------------------------------------
    // Step 1: ตรวจสอบและค้นหาอีเมลในระบบ
    // ----------------------------------------------------
    if (isset($_POST['btn_verify_email'])) {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "กรุณากรอกรูปแบบอีเมลที่ถูกต้อง";
        } else {
            // ใช้ MySQLi Prepared Statement ตาม PHPCodingGuide.md ข้อ 1 และ SQLCodingGuide.md ข้อ 2
            $sql = "SELECT id, username, email FROM users WHERE email = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($user = mysqli_fetch_assoc($result)) {
                    $_SESSION['reset_user_id'] = (int)$user['id'];
                    $_SESSION['reset_user_email'] = $user['email'];
                    $_SESSION['reset_user_name'] = $user['username'];

                    $step = 2;
                    $verified_user = $user;
                } else {
                    $error_msg = "ไม่พบบัญชีผู้ใช้ที่ลงทะเบียนด้วยอีเมลนี้ในระบบ กรุณาตรวจสอบอีกครั้ง";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_msg = "ระบบเกิดข้อผิดพลาดชั่วคราว กรุณาลองใหม่อีกครั้ง";
            }
        }
    }

    // ----------------------------------------------------
    // Step 2: ตรวจสอบและบันทึกรหัสผ่านใหม่
    // ----------------------------------------------------
    if (isset($_POST['btn_reset_password'])) {
        if (!isset($_SESSION['reset_user_id'])) {
            $error_msg = "เซสชันการตั้งรหัสผ่านหมดอายุ กรุณาระบุอีเมลเพื่อเริ่มต้นใหม่อีกครั้ง";
            $step = 1;
        } else {
            $user_id = (int)$_SESSION['reset_user_id'];
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (strlen($new_password) < 4) {
                $error_msg = "รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 4 ตัวอักษร";
                $step = 2;
            } elseif ($new_password !== $confirm_password) {
                $error_msg = "รหัสผ่านใหม่ทั้งสองช่องไม่ตรงกัน กรุณาลองใหม่อีกครั้ง";
                $step = 2;
            } else {
                // อัปเดตรหัสผ่านใหม่ด้วย MySQLi Prepared Statement
                $sql_update = "UPDATE users SET password = ? WHERE id = ?";
                $stmt_up = mysqli_prepare($conn, $sql_update);

                if ($stmt_up) {
                    mysqli_stmt_bind_param($stmt_up, "si", $new_password, $user_id);
                    
                    if (mysqli_stmt_execute($stmt_up)) {
                        // ล้าง Session
                        unset($_SESSION['reset_user_id']);
                        unset($_SESSION['reset_user_email']);
                        unset($_SESSION['reset_user_name']);

                        echo "<script>
                            alert('รีเซ็ตรหัสผ่านใหม่สำเร็จเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่');
                            window.location.href = 'login.php';
                        </script>";
                        exit();
                    } else {
                        $error_msg = "ไม่สามารถบันทึกรหัสผ่านใหม่ได้ กรุณาลองใหม่อีกครั้ง";
                        $step = 2;
                    }
                    mysqli_stmt_close($stmt_up);
                } else {
                    $error_msg = "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล";
                    $step = 2;
                }
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
    <title>ลืมรหัสผ่าน - Eternal Love</title>
    
    <!-- ฟอนต์และสไตล์ชีตมาตรฐาน (ตาม HTMLCodingGuide.md) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Sarabun:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body class="auth-page">

    <!-- แถบเมนูนำทางหลัก -->
    <?php include('navbar.php'); ?>

    <!-- ========================================== -->
    <!-- 2. PRESENTATION ZONE (โซนแสดงผล HTML5)    -->
    <!-- ========================================== -->
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card auth-card shadow-sm p-4 mt-5">
                    
                    <header class="text-center mb-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                  style="width: 60px; height: 60px; background: rgba(197, 160, 89, 0.15); color: #c5a059; font-size: 24px;">
                                <i class="fa-solid <?= ($step === 1) ? 'fa-key' : 'fa-lock-open'; ?>"></i>
                            </span>
                        </div>
                        <h1 class="auth-title fs-3">ลืมรหัสผ่าน</h1>
                        <p class="text-muted small mb-0">
                            <?= ($step === 1) 
                                ? 'ระบุอีเมลที่คุณใช้ลงทะเบียนเพื่อยืนยันบัญชี' 
                                : 'ตั้งรหัสผ่านใหม่เพื่อความปลอดภัยในการเข้าใช้งาน'; ?>
                        </p>
                    </header>

                    <!-- การแจ้งเตือนข้อผิดพลาด (Friendly Error Handling ตาม PHPCodingGuide.md ข้อ 7) -->
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger p-2 small text-center mb-4" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <?= htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- STEP 1: ค้นหาและยืนยันอีเมล -->
                    <?php if ($step === 1): ?>
                        <form action="forgot_password.php" method="POST">
                            <div class="mb-4">
                                <label for="user_email" class="auth-label mb-1">
                                    <i class="fa-regular fa-envelope me-1" style="color: #c5a059;"></i> อีเมลที่ลงทะเบียน
                                </label>
                                <input type="email" 
                                       class="form-control auth-input" 
                                       id="user_email" 
                                       name="email" 
                                       placeholder="your-email@example.com" 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                                       required 
                                       autofocus>
                            </div>
                            <button type="submit" name="btn_verify_email" class="btn auth-btn w-100 py-3 mb-3">
                                ตรวจสอบบัญชี <i class="fa-solid fa-arrow-right ms-1"></i>
                            </button>
                        </form>
                    
                    <!-- STEP 2: ตั้งรหัสผ่านใหม่ -->
                    <?php else: ?>
                        <div class="p-3 mb-4 rounded border" style="background: #fdfaf5; border-color: rgba(197, 160, 89, 0.3) !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge mb-1" style="background: #c5a059;">พบบัญชีผู้ใช้</span>
                                    <div class="fw-bold text-dark small">
                                        <i class="fa-solid fa-user me-1 text-gold"></i> คุณ<?= htmlspecialchars($verified_user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.8rem;">
                                        <?= htmlspecialchars($verified_user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>
                                <a href="forgot_password.php?reset_flow=1" class="btn btn-outline-secondary btn-sm" title="เปลี่ยนอีเมล" style="font-size: 0.75rem;">
                                    เปลี่ยนอีเมล
                                </a>
                            </div>
                        </div>

                        <form action="forgot_password.php" method="POST">
                            <div class="mb-3">
                                <label for="new_password" class="auth-label mb-1">
                                    <i class="fa-solid fa-lock me-1" style="color: #c5a059;"></i> รหัสผ่านใหม่
                                </label>
                                <input type="password" 
                                       class="form-control auth-input" 
                                       id="new_password" 
                                       name="new_password" 
                                       placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 4 ตัวอักษร)" 
                                       minlength="4" 
                                       required 
                                       autofocus>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="auth-label mb-1">
                                    <i class="fa-solid fa-shield-check me-1" style="color: #c5a059;"></i> ยืนยันรหัสผ่านใหม่
                                </label>
                                <input type="password" 
                                       class="form-control auth-input" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" 
                                       minlength="4" 
                                       required>
                            </div>

                            <button type="submit" name="btn_reset_password" class="btn auth-btn w-100 py-3 mb-3">
                                บันทึกรหัสผ่านใหม่ <i class="fa-solid fa-check ms-1"></i>
                            </button>
                        </form>
                    <?php endif; ?>

                    <!-- เมนูช่วยเหลือด้านล่าง -->
                    <footer class="text-center border-top pt-4 mt-2">
                        <p class="small mb-2">
                            จำรหัสผ่านได้แล้วใช่ไหม? 
                            <a href="login.php" class="auth-link-gold fw-semibold">เข้าสู่ระบบ</a>
                        </p>
                        <p class="small text-muted mb-0">
                            ยังไม่มีบัญชี? 
                            <a href="register.php" class="auth-link-dark">สมัครสมาชิกใหม่</a>
                        </p>
                    </footer>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="small text-white-50 text-decoration-none">
                        <i class="fa-solid fa-arrow-left me-1"></i> กลับสู่หน้าหลัก Eternal Love
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
