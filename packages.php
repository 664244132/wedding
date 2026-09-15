<?php
/**
 * packages.php - หน้ารวมแพ็กเกจแต่งงานและพรีเวดดิ้ง Eternal Love
 * สถาปัตยกรรม PHP Procedural + MySQLi Best Practices ตามคู่มือ markdowns/
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once('config.php'); 

// ดึงข้อมูลแพ็กเกจด้วย Explicit Columns ตาม SQLCodingGuide.md ข้อ 1
$sql = "SELECT id, package_name, package_price, package_detail, package_image FROM packages ORDER BY package_price ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แพ็กเกจและราคา - Eternal Love Wedding</title>
    
    <!-- ฟอนต์และสไตล์ชีตมาตรฐาน (ตาม HTMLCodingGuide.md & CSSCodingGuide.md) -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Prompt:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/packages.css?v=<?= time(); ?>">
    
    <style>
        :root {
            --gold-primary: #c5a059;
            --gold-light: #e2c285;
            --dark-wedding: #4a3b2b;
            --soft-cream: #fdfaf5;
        }

        body { 
            font-family: 'Prompt', 'Sarabun', sans-serif; 
            background-color: #16120e; 
            background-image: radial-gradient(rgba(197, 160, 89, 0.08) 1px, transparent 0);
            background-size: 24px 24px;
            color: #ffffff; 
            padding-top: 130px; 
        }

        .section-title { 
            font-family: 'Playfair Display', serif; 
            color: var(--gold-light); 
            letter-spacing: 0.5px;
        }

        .section-subtitle {
            letter-spacing: 6px;
            color: var(--gold-primary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .title-line {
            width: 70px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
            margin: 20px auto 35px auto;
        }

        .package-card { 
            border: 1px solid rgba(197, 160, 89, 0.25); 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            background: #ffffff; 
            border-radius: 20px; 
            overflow: hidden; 
            color: #333333; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .package-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6); 
            border-color: var(--gold-primary); 
        }

        .featured-card { 
            border: 2px solid var(--gold-primary) !important; 
            transform: scale(1.03); 
            box-shadow: 0 15px 40px rgba(197, 160, 89, 0.25);
        }

        .package-img-container {
            height: 260px;
            background: #201a14;
            overflow: hidden;
            position: relative;
        }

        .package-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .package-card:hover .package-img-container img {
            transform: scale(1.06);
        }

        .badge-popular {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
            color: #ffffff;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 6px 14px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 2;
            letter-spacing: 1px;
        }

        .btn-select-pkg { 
            background: #2a2016; 
            color: #ffffff; 
            border: 1.5px solid var(--gold-primary); 
            letter-spacing: 2px; 
            transition: all 0.3s ease; 
        }

        .btn-select-pkg:hover { 
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-light)); 
            color: #ffffff; 
            border-color: transparent;
            box-shadow: 0 5px 15px rgba(197, 160, 89, 0.35);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <!-- แถบเมนูนำทางหลัก -->
    <?php include('navbar.php'); ?>

    <main class="container py-5">
        <header class="text-center mb-5">
            <span class="section-subtitle d-block mb-2">Pre-Wedding & Ceremony Collections</span>
            <h1 class="section-title display-4 fw-bold">เลือกแพ็กเกจที่เหมาะกับคุณ</h1>
            <div class="title-line"></div>
            <p class="text-white-50 mx-auto" style="max-width: 620px; font-size: 0.95rem;">
                บริการถ่ายภาพพรีเวดดิ้งและชุดแต่งงานระดับพรีเมียม ตอบโจทย์ทุกความต้องการของคู่บ่าวสาวอย่างสมบูรณ์แบบ
            </p>
        </header>

        <div class="row g-4 justify-content-center">
            <?php 
            if ($result && mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): 
                    // ตรวจสอบการ์ดเด่น (Featured) จากชื่อหรือราคา
                    $isGold = (stripos($row['package_name'], 'มาตรฐาน') !== false || stripos($row['package_name'], 'Gold') !== false);
                    $cardClass = $isGold ? 'featured-card' : '';
                    $p_name = $row['package_name'];
                    $p_price = (float)$row['package_price'];
                    $p_detail = $row['package_detail'];
                    $p_image = !empty($row['package_image']) ? $row['package_image'] : '';
            ?>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 package-card text-center <?= $cardClass; ?>">
                    <div class="package-img-container">
                        <?php if ($isGold): ?>
                            <span class="badge-popular"><i class="fa-solid fa-crown me-1"></i> POPULAR</span>
                        <?php endif; ?>

                        <?php if (!empty($p_image)): ?>
                            <img src="img/packages/<?= htmlspecialchars($p_image, ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?= htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="img-fluid"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/2a2016/c5a059?text=Pre-Wedding+Package';">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 text-white opacity-50">
                                <i class="fa-solid fa-gem fa-3x" style="color: var(--gold-primary);"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="py-3" style="background: <?= ($isGold) ? 'linear-gradient(135deg, #c5a059, #e2c285)' : '#261e16'; ?>; color: #ffffff;">
                        <h2 class="h5 mb-0 text-uppercase" style="letter-spacing: 2px; font-weight: 600;">
                            <?= htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8'); ?>
                        </h2>
                    </div>
                    
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3" style="font-size: 2.3rem; font-weight: bold; color: #c5a059;">
                            ฿<?= number_format($p_price); ?>
                        </div>
                        
                        <div class="text-start mb-4 flex-grow-1">
                            <?php 
                                $lines = explode("\n", $p_detail);
                                echo "<ul class='list-unstyled small mb-0' style='line-height: 1.9;'>";
                                foreach($lines as $line) {
                                    $cleanLine = trim($line);
                                    if ($cleanLine !== "") {
                                        echo "<li><i class='fa-solid fa-circle-check me-2' style='color: #c5a059;'></i> " . htmlspecialchars($cleanLine, ENT_QUOTES, 'UTF-8') . "</li>";
                                    }
                                }
                                echo "</ul>";
                            ?>
                        </div>

                        <div class="mt-auto pt-3 border-top">
                            <a href="booking.php?selected_item=<?= urlencode($p_name); ?>&price=<?= $p_price; ?>&service_type=แพ็กเกจแต่งงาน" 
                               class="btn w-100 py-3 rounded-pill btn-select-pkg fw-bold">
                                SELECT PACKAGE <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">ไม่พบข้อมูลแพ็กเกจในขณะนี้</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>