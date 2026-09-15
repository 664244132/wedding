<?php 
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }
include('../config.php'); 

if (isset($_GET['confirm_id']) && !empty($_GET['confirm_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['confirm_id']);
    mysqli_query($conn, "UPDATE bookings SET status = 'confirmed' WHERE id = '$id'");
    header("Location: admin_bookings.php?msg=confirmed");
    exit();
}

if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM bookings WHERE id = '$id'");
    header("Location: admin_bookings.php?msg=deleted");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบจัดการการจอง - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .table thead { background-color: #212529; color: white; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-calendar-check me-2 text-primary"></i> รายการจองคิวลูกค้า</h4>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">กลับหน้าหลัก</a>
    </div>

    <div class="card main-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">วันนัดหมาย</th>
                        <th>ชื่อลูกค้า</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>บริการ / โน้ตเพิ่มเติม</th>
                        <th>สถานที่</th>
                        <th>สถานะ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM bookings ORDER BY id DESC");
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)):
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?= date('d/m/Y', strtotime($row['booking_date'])) ?></div>
                            <small class="text-muted">จองเมื่อ: <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small>
                        </td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row['username']) ?></td>
                        <td class="text-primary fw-bold"><?= htmlspecialchars($row['phone']) ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($row['services']) ?></div>
                            <small class="text-muted italic">สไตล์: <?= htmlspecialchars($row['note']) ?></small>
                        </td>
                        <td><span class="small"><?= htmlspecialchars($row['location_type']) ?></span></td>
                        <td>
                            <?php if($row['status'] == 'confirmed'): ?>
                                <span class="badge bg-success badge-status">ยืนยันแล้ว</span>
                            <?php elseif($row['status'] == 'cancelled'): ?>
                                <span class="badge bg-danger badge-status">ยกเลิก</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark badge-status">รอการยืนยัน</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if($row['status'] == 'pending'): ?>
                                <a href="?confirm_id=<?= $row['id'] ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i></a>
                                <?php endif; ?>
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ลบรายการนี้?')"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; } else { echo "<tr><td colspan='7' class='text-center py-5 text-muted'>ยังไม่มีข้อมูลการจองในระบบ</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>