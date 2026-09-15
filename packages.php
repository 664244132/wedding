<?php
session_start();
// 1. ตรวจสอบไฟล์เชื่อมต่อ
if (file_exists('config.php')) {
    require_once('config.php'); 
} else {
    die("Error: ไม่พบไฟล์ config.php");
}

// 2. ตรวจสอบการเชื่อมต่อ Database
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 3. ดึงข้อมูล
$sql = "SELECT * FROM packages ORDER BY package_price ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages & Pricing - Eternal Love Wedding</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #1a1a1a; color: white; padding-top: 100px; }
        .section-title { font-family: 'Playfair Display', serif; color: #D4AF37; }
        .package-card { border: 1px solid rgba(212, 175, 55, 0.2); transition: 0.3s; background: #fff; border-radius: 15px; overflow: hidden; color: #333; }
        .package-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.5); border-color: #D4AF37; }
        .featured-card { border: 2px solid #D4AF37 !important; transform: scale(1.03); }
        .btn-select-pkg { background: #222; color: #fff; border: 1px solid #D4AF37; letter-spacing: 2px; transition: 0.3s; }
        .btn-select-pkg:hover { background: #D4AF37; color: #000; }
    </style>
</head>
<body>

    <?php if(file_exists('navbar.php')) include('navbar.php'); ?>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-uppercase" style="letter-spacing: 5px; color: #D4AF37;">Our Packages</h6>
            <h1 class="section-title display-4">เลือกแพ็กเกจที่เหมาะกับคุณ</h1>
            <div style="width: 80px; height: 2px; background: #D4AF37; margin: 20px auto;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php 
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
                    $isGold = (stripos($row['package_name'], 'Gold') !== false) ? 'featured-card' : '';
                    $p_name = $row['package_name'];
                    $p_price = $row['package_price'];
                    $p_detail = $row['package_detail'];
            ?>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 package-card text-center <?php echo $isGold; ?>">
                    <div style="height: 250px; background: #000; overflow: hidden;">
                        <?php if(!empty($row['package_image'])): ?>
                            <img src="img/packages/<?php echo $row['package_image']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 text-white opacity-50">
                                <i class="fa-solid fa-gem fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="py-3" style="background: <?php echo ($isGold) ? '#D4AF37' : '#222'; ?>; color: #fff;">
                        <h5 class="mb-0 text-uppercase" style="letter-spacing: 2px; font-weight: 600;">
                            <?php echo htmlspecialchars($p_name); ?>
                        </h5>
                    </div>
                    
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3" style="font-size: 2.2rem; font-weight: bold; color: #D4AF37;">
                            ฿<?php echo number_format($p_price); ?>
                        </div>
                        
                        <div class="text-start mb-4">
                            <?php 
                                $lines = explode("\n", $p_detail);
                                echo "<ul class='list-unstyled small' style='line-height: 1.8;'>";
                                foreach($lines as $line) {
                                    if(trim($line) != "") echo "<li><i class='fa-solid fa-check text-warning me-2'></i> ".htmlspecialchars($line)."</li>";
                                }
                                echo "</ul>";
                            ?>
                        </div>

                        <div class="mt-auto">
                            <a href="booking.php?selected_item=<?php echo urlencode($p_name); ?>&price=<?php echo $p_price; ?>&details=<?php echo urlencode($p_detail); ?>" 
                               class="btn w-100 py-3 rounded-pill btn-select-pkg fw-bold">
                                SELECT PACKAGE
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">ไม่พบข้อมูลแพ็กเกจ</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>