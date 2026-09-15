<?php 
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }
require_once('../config.php');

if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    // ลบรูปที่แนบมากับรีวิวด้วย (ถ้ามี)
    $res = mysqli_query($conn, "SELECT review_image FROM reviews WHERE id = '$id'");
    if($row = mysqli_fetch_assoc($res)) {
        if(!empty($row['review_image'])) { @unlink("../uploads/reviews/" . $row['review_image']); }
    }
    mysqli_query($conn, "DELETE FROM reviews WHERE id = '$id'");
    header("Location: admin_reviews.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการรีวิว - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .table thead { background-color: #212529; color: white; }
        .star-rating { color: #f1c40f; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-star me-2 text-warning"></i> จัดการรีวิวจากลูกค้า</h4>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">กลับหน้าหลัก</a>
    </div>

    <div class="card main-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">วันที่รีวิว</th>
                        <th>ชื่อลูกค้า</th>
                        <th>คะแนน</th>
                        <th>ข้อความรีวิว</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM reviews ORDER BY id DESC";
                    $res = mysqli_query($conn, $sql);
                    if($res && mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)):
                            $cust_name = $row['username'] ?? $row['customer_name'] ?? $row['name'] ?? 'ไม่ระบุชื่อ';
                    ?>
                    <tr>
                        <td class="ps-4"><small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($cust_name) ?></td>
                        <td>
                            <div class="star-rating">
                                <?php for($i=1; $i<=5; $i++) echo ($i <= $row['rating']) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
                            </div>
                        </td>
                        <td>
                            <p class="mb-0 small text-secondary"><?= htmlspecialchars($row['review_text'] ?? '-') ?></p>
                            <?php if(!empty($row['review_image'])): ?>
                                <a href="../uploads/reviews/<?= $row['review_image'] ?>" target="_blank" class="badge bg-info mt-1 text-decoration-none">ดูรูปแนบ</a>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบรีวิวนี้?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; } else { echo "<tr><td colspan='5' class='text-center py-4 text-muted'>ยังไม่มีข้อมูลรีวิวในระบบ</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>