<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') { header("Location: ../login.php"); exit(); }

if (isset($_POST['save_package'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $detail = mysqli_real_escape_string($conn, $_POST['detail']);

    $image_query = "";
    $filename = "";
    if (!empty($_FILES['image']['name'])) {
        $filename = "pkg_" . time() . "_" . basename($_FILES['image']['name']);
        // เปลี่ยน Path อัปโหลดให้ถอยหลัง 1 โฟลเดอร์
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../img/packages/" . $filename)) {
            $image_query = ", package_image = '$filename'";
        }
    }

    if (!empty($id)) {
        $sql = "UPDATE packages SET package_name='$name', package_price='$price', package_detail='$detail' $image_query WHERE id=$id";
    } else {
        $final_image = (!empty($filename)) ? $filename : "default.jpg";
        $sql = "INSERT INTO packages (package_name, package_price, package_detail, package_image) VALUES ('$name', '$price', '$detail', '$final_image')";
    }
    mysqli_query($conn, $sql);
    header("Location: admin_packages.php?status=success");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = mysqli_query($conn, "SELECT package_image FROM packages WHERE id=$id");
    if($row = mysqli_fetch_assoc($res)){
        if($row['package_image'] != 'default.jpg') { @unlink("../img/packages/" . $row['package_image']); }
    }
    mysqli_query($conn, "DELETE FROM packages WHERE id=$id");
    header("Location: admin_packages.php?status=deleted");
    exit();
}

$sql = "SELECT * FROM packages ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการแพ็กเกจ - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }
        .admin-nav { background: #212529; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .nav-logo span { color: #D4AF37; font-weight: bold; }
        .content-body { padding: 40px; max-width: 1200px; margin: auto; }
        .btn-gold { background: #D4AF37; color: #fff; border: none; }
        .btn-gold:hover { background: #b8952d; color: #fff; }
        .package-img { width: 80px; height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="admin-nav shadow-sm">
        <div class="nav-logo">ETERNAL<span>LOVE</span> ADMIN</div>
        <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">กลับหน้าหลัก</a>
    </div>
    <div class="content-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fa-solid fa-gem text-warning"></i> จัดการแพ็กเกจแต่งงาน</h2>
            <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="clearForm()">
                <i class="fa-solid fa-plus"></i> เพิ่มแพ็กเกจใหม่
            </button>
        </div>
        <div class="card border-0 shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>รูปภาพ</th>
                            <th>ชื่อแพ็กเกจ</th>
                            <th>ราคา</th>
                            <th>รายละเอียดเบื้องต้น</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><img src="../img/packages/<?php echo $row['package_image']; ?>" class="package-img" onerror="this.src='https://placehold.co/80x50'"></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['package_name']); ?></td>
                                <td class="text-success fw-bold"><?php echo number_format($row['package_price']); ?> .-</td>
                                <td class="text-muted small"><?php echo mb_strimwidth($row['package_detail'], 0, 50, "..."); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" onclick='editPackage(<?php echo json_encode($row); ?>)'><i class="fa-solid fa-pen"></i></button>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบแพ็กเกจนี้หรือไม่?')"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">ไม่พบข้อมูลแพ็กเกจ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="packageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">ข้อมูลแพ็กเกจ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="p_id">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">ชื่อแพ็กเกจ</label>
                            <input type="text" name="name" id="p_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ราคา (บาท)</label>
                            <input type="number" name="price" id="p_price" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รายละเอียด</label>
                        <textarea name="detail" id="p_detail" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รูปภาพแพ็กเกจ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save_package" class="btn btn-gold w-100">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editPackage(data) {
            document.getElementById('modalTitle').innerText = "แก้ไขแพ็กเกจ";
            document.getElementById('p_id').value = data.id;
            document.getElementById('p_name').value = data.package_name;
            document.getElementById('p_price').value = data.package_price;
            document.getElementById('p_detail').value = data.package_detail;
            new bootstrap.Modal(document.getElementById('packageModal')).show();
        }
        function clearForm() {
            document.getElementById('modalTitle').innerText = "เพิ่มแพ็กเกจใหม่";
            document.getElementById('p_id').value = '';
            document.getElementById('p_name').value = '';
            document.getElementById('p_price').value = '';
            document.getElementById('p_detail').value = '';
        }
    </script>
</body>
</html>