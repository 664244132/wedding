<?php 
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }
include('../config.php'); 

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM contacts WHERE id = $id");
    header("Location: admin_contact.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อความจากลูกค้า - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .table thead { background-color: #1abc9c; color: white; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-envelope me-2 text-success"></i> ข้อความติดต่อจากลูกค้า</h4>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">กลับหน้าหลัก</a>
    </div>

    <div class="card main-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">วันที่</th>
                        <th>ชื่อผู้ติดต่อ</th>
                        <th>หัวข้อ/อีเมล</th>
                        <th>ข้อความ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM contacts ORDER BY id DESC");
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)):
                    ?>
                    <tr>
                        <td class="ps-4"><small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small></td>
                        <td><div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div></td>
                        <td>
                            <div class="small fw-bold text-primary"><?= htmlspecialchars($row['subject'] ?? 'สอบถามข้อมูล') ?></div>
                            <div class="small text-muted"><?= htmlspecialchars($row['email']) ?></div>
                        </td>
                        <td>
                            <p class="mb-0 small text-secondary" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($row['message']) ?>
                            </p>
                        </td>
                        <td class="text-center">
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-light btn-sm text-danger" onclick="return confirm('ยืนยันการลบข้อความนี้?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    } else { echo "<tr><td colspan='5' class='text-center py-5 text-muted'>ไม่มีข้อความติดต่อในขณะนี้</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>