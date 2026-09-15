<?php 
include('config.php'); // เชื่อมต่อฐานข้อมูล

$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    // รับค่าจากฟอร์ม
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // บันทึกลงตาราง contacts
    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if (mysqli_query($conn, $sql)) {
        $success_msg = "ส่งข้อความสำเร็จ! เราจะติดต่อกลับโดยเร็วที่สุด";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ติดต่อเรา - Eternal Love Wedding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap');
        body { background-color: #fdfaf5; font-family: 'Sarabun', sans-serif; padding-top: 120px; }
        .contact-info h5 { color: #b38b59; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 15px; }
        .form-control { border-radius: 0; border: 1px solid #ddd; padding: 12px; }
        .form-control:focus { border-color: #b38b59; box-shadow: none; }
        .btn-submit { background: #b38b59; color: white; border-radius: 0; padding: 12px 30px; letter-spacing: 2px; transition: 0.3s; border: none; }
        .btn-submit:hover { background: #8e6d44; color: white; }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <?php if($success_msg): ?>
            <div class="alert alert-success border-0 shadow-sm mb-5 text-center"><?= $success_msg ?></div>
        <?php endif; ?>

        <div class="row g-5">
            <div class="col-md-5 contact-info">
                <h2 style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 40px;" class="mb-5">Contact Us</h2>
                <div class="mb-5">
                    <h5>ที่อยู่สตูดิโอ</h5>
                    <p>123 ถนนสุขุมวิท ซอย 55 (ทองหล่อ)<br>แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพฯ 10110</p>
                </div>
                <div class="mb-5">
                    <h5>ติดต่อฝ่ายขาย</h5>
                    <p>โทร: 02-xxx-xxxx<br>อีเมล: hello@eternallove.com</p>
                </div>
                <div class="mb-5">
                    <h5>เวลาทำการ</h5>
                    <p>วันอังคาร - วันอาทิตย์ (หยุดวันจันทร์)<br>เวลา 10:00 น. - 19:00 น.</p>
                </div>
                <div class="social-links">
                    <a href="#" class="me-3 text-dark"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="me-3 text-dark"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-line"></i></a>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4" style="background: #fff;">
                    <h4 class="mb-4" style="font-family: 'Playfair Display', serif;">ส่งข้อความถึงเรา</h4>
                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">ชื่อของคุณ</label>
                                <input type="text" name="name" class="form-control" required placeholder="ชื่อ-นามสกุล">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">อีเมล</label>
                                <input type="email" name="email" class="form-control" required placeholder="email@example.com">
                            </div>
                            <div class="col-12">
                                <label class="small text-muted mb-1">หัวข้อติดต่อ</label>
                                <input type="text" name="subject" class="form-control" placeholder="เช่น สอบถามแพ็กเกจแต่งงาน">
                            </div>
                            <div class="col-12">
                                <label class="small text-muted mb-1">รายละเอียดข้อความ</label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="พิมพ์ข้อความที่นี่..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="send_message" class="btn btn-submit w-100">SEND MESSAGE</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mt-4">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1200" class="img-fluid rounded shadow-sm" alt="Studio Location">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>