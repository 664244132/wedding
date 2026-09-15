<?php 
session_start(); 
include('config.php'); 

$services = [];

if (isset($conn)) {
    // ดึงข้อมูลเพิ่มคอลัมน์ description และ icon (ที่เป็นชื่อไฟล์รูป)
    $query = mysqli_query($conn, "SELECT * FROM services");
    if ($query) {
        while($row = mysqli_fetch_assoc($query)) {
            $services[] = [
                'db_id' => $row['id'],
                'id'    => 'modal' . $row['id'], 
                'icon'  => $row['icon'], // ตอนนี้จะเป็นชื่อไฟล์รูป เช่น icon_1.jpg
                'title' => $row['title'],
                'description' => $row['description'], // รายละเอียดที่เพิ่มใหม่
                'type'  => $row['service_type'],
                'price' => $row['price']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style_services.css?v=<?php echo time(); ?>">
    <style>
        .price-badge { background: #d4af37; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; }
        .item-content img { height: 250px; object-fit: cover; width: 100%; }
        .item-radio:checked + .item-content { border-color: #d4af37 !important; background-color: #fdfaf3; }
        
        /* สไตล์ใหม่สำหรับ Icon ที่เป็นรูปภาพ */
        .service-icon-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 2px solid #f1f1f1;
            padding: 5px;
            background: #fff;
        }
        .service-card { transition: 0.3s; cursor: pointer; }
        .service-card:hover { transform: translateY(-10px); }
    </style>
</head>
<body>

    <?php include('navbar.php'); ?>

    <div class="container pb-5 mt-5">
        <div class="text-center mb-5">
            <h6 class="section-subtitle">Luxurious Experience</h6>
            <h1 class="section-title">Our Services</h1>
            <div class="title-line"></div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $s): ?>
                <div class="col-md-3">
                    <div class="card h-100 service-card text-center shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#<?= $s['id'] ?>">
                        <div class="card-body d-flex flex-column align-items-center">
                            <img src="img/icons/<?= $s['icon'] ?>" 
                                 onerror="this.src='https://placehold.co/100x100?text=Service'" 
                                 class="service-icon-img shadow-sm">
                            
                            <h5 class="fw-bold" style="letter-spacing: 1px; color: #5d4a37;"><?= $s['title'] ?></h5>
                            
                            <p class="small text-muted mb-3" style="font-size: 0.85rem;">
                                <?= mb_strimwidth(htmlspecialchars($s['description']), 0, 100, "...") ?>
                            </p>

                            <div class="mt-auto">
                                <p class="small text-gold mb-1 fw-bold">เริ่มต้น ฿<?= number_format($s['price']) ?></p>
                                <p class="small text-muted text-uppercase" style="letter-spacing: 2px; font-size: 0.7rem;">View Collection</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <form id="redirectForm" action="booking.php" method="GET">
        <input type="hidden" name="selected_item" id="hiddenItem">
        <input type="hidden" name="service_type" id="hiddenService">
    </form>

    <?php if (!empty($services)): ?>
        <?php foreach ($services as $s): ?>
        <div class="modal fade" id="<?= $s['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 p-4 pb-0">
                        <div>
                            <h5 class="fw-bold mb-1"><?= $s['title'] ?> Collection</h5>
                            <p class="small text-muted mb-0"><?= htmlspecialchars($s['description']) ?></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <?php 
                            $current_s_id = $s['db_id'];
                            $img_query = mysqli_query($conn, "SELECT * FROM service_images WHERE service_id = $current_s_id");
                            
                            if (mysqli_num_rows($img_query) > 0):
                                while($img = mysqli_fetch_assoc($img_query)):
                            ?>
                            <div class="col-md-3 col-6">
                                <label class="w-100 position-relative" style="cursor: pointer;">
                                    <input type="radio" name="item_select" class="item-radio d-none" 
                                           value="<?= htmlspecialchars($s['title'] . " - " . $img['caption']) ?>">
                                    <div class="item-content text-center border p-2 rounded shadow-sm">
                                        <img src="img/gallery/<?= $img['image_path'] ?>" class="img-fluid rounded mb-2">
                                        <div class="small fw-bold text-dark"><?= htmlspecialchars($img['caption']) ?></div>
                                        <div class="price-badge d-inline-block mt-1">฿<?= number_format($img['img_price']) ?></div>
                                    </div>
                                </label>
                            </div>
                            <?php 
                                endwhile; 
                            else:
                                echo "<div class='col-12 text-center py-5'><p class='text-muted'>ยังไม่มีรายการในขณะนี้</p></div>";
                            endif;
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button class="btn btn-primary w-100 py-3 fw-bold" 
                                style="background: #5d4a37; border: none; border-radius: 10px;"
                                onclick="submitSelection('<?= $s['type'] ?>')">ยืนยันการเลือกและจองบริการ</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function submitSelection(service) {
            const activeModal = document.querySelector('.modal.show');
            const selected = activeModal.querySelector('input[name="item_select"]:checked');
            
            if (selected) {
                document.getElementById('hiddenItem').value = selected.value;
                document.getElementById('hiddenService').value = service;
                document.getElementById('redirectForm').submit();
            } else {
                alert('กรุณาเลือกแบบที่ต้องการก่อนยืนยัน');
            }
        }
    </script>
</body>
</html>