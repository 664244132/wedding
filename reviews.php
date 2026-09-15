<?php
session_start();
// 1. เปลี่ยนจาก db_config.php เป็น config.php ตามที่เราตกลงกันไว้
require_once('config.php'); 
   
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    // 2. ปรับตัวแปรให้ตรงกับโครงสร้างที่เราคุยกันในหน้า Admin
    $name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $service = mysqli_real_escape_string($conn, $_POST['service_type']);
    $rating = (int)$_POST['rating'];
    $text = mysqli_real_escape_string($conn, $_POST['review_text']);
    
    // --- ส่วนการจัดการรูปภาพ ---
    $image_name = null;
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
        $target_dir = "uploads/reviews/"; 
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["review_image"]["name"], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $file_extension; 
        $target_file = $target_dir . $image_name;

        $check = getimagesize($_FILES["review_image"]["tmp_name"]);
        if($check !== false) {
            move_uploaded_file($_FILES["review_image"]["tmp_name"], $target_file);
        } else {
            $image_name = null; 
        }
    }

    // 3. ปรับ SQL INSERT ให้ใช้ชื่อคอลัมน์ 'username' และ 'review_text' ตามหน้า Admin
    $sql = "INSERT INTO reviews (username, rating, review_text, review_image) 
            VALUES ('$name', '$rating', '$text', '$image_name')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('ขอบคุณสำหรับรีวิวครับ!'); window.location='reviews.php';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
  
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Customer Reviews - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/reviews.css">
</head>
<body class="reviews-page" style="background-color: #1a1a1a;"> <?php if(file_exists('navbar.php')) include('navbar.php'); ?>

    <div class="container reviews-wrapper" style="margin-top: 140px; margin-bottom: 60px;">
        <div class="text-center mb-5">
            <h6 class="text-uppercase mb-2" style="letter-spacing: 5px; color: #b38b59;">Testimonials</h6>
            <h1 style="font-family: 'Playfair Display', serif; font-weight: 700; color: #ffffff;">เสียงจากลูกค้าของเรา</h1>
            <p class="text-muted">ความสุขของท่าน คือความภูมิใจของเรา</p>
        </div>

        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px; background: rgba(255,255,255,0.95);">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['username']); ?>&background=b38b59&color=fff" 
                                 class="rounded-circle me-3" style="width: 50px; height: 50px; border: 2px solid #b38b59;">
                            <div>
                                <div style="color: #ffc107; font-size: 0.8rem;">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $row['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                </div>
                                <h5 class="mb-0 fw-bold" style="color: #4a3b2b;"><?php echo htmlspecialchars($row['username']); ?></h5>
                                </div>
                        </div>
                        
                        <p class="text-secondary mb-3" style="font-style: italic;">"<?php echo htmlspecialchars($row['review_text']); ?>"</p>

                        <?php if (!empty($row['review_image'])): ?>
                            <div class="review-img-container mb-2">
                                <img src="uploads/reviews/<?php echo $row['review_image']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 250px; width: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 w-100">
                    <p class="text-white">ยังไม่มีรีวิวในขณะนี้ ร่วมเป็นคนแรกที่แบ่งปันประสบการณ์กับเรา</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <button class="btn" style="background: #b38b59; color: white; padding: 15px 40px; border-radius: 0px; letter-spacing: 2px;" 
                    data-bs-toggle="modal" data-bs-target="#reviewModal">
                SHARE YOUR EXPERIENCE
            </button>
        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 0px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header border-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">เขียนรีวิวของคุณ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้รีวิว</label>
                            <input type="text" name="client_name" class="form-control" value="<?php echo $_SESSION['username'] ?? ''; ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">บริการที่ใช้</label>
                                <select name="service_type" class="form-select">
                                    <option>Wedding Ceremony</option>
                                    <option>Pre-Wedding Session</option>
                                    <option>Bridal Gown Service</option>
                                    <option>Full Wedding Organizer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">คะแนน</label>
                                <select name="rating" class="form-select">
                                    <option value="5">5 ดาว</option>
                                    <option value="4">4 ดาว</option>
                                    <option value="3">3 ดาว</option>
                                    <option value="2">2 ดาว</option>
                                    <option value="1">1 ดาว</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ข้อความรีวิว</label>
                            <textarea name="review_text" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">แนบรูปภาพประกอบ (ถ้ามี)</label>
                            <input type="file" name="review_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="submit" name="submit_review" class="btn w-100" style="background: #b38b59; color: white;">บันทึกรีวิว</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>