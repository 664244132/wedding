<?php 
/**
 * services.php - หน้ารวมบริการชุดแต่งงาน Eternal Love
 * สถาปัตยกรรม PHP Procedural + MySQLi Best Practices ตามคู่มือ markdowns/
 */

// ==========================================
// 1. DATA PREPARATION ZONE (โซนเตรียมข้อมูล)
// ==========================================
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once('config.php');

$services = [];

// ชุดข้อมูลสำรอง (Fallback Data) รับประกันการแสดงผลชุดไทย 5 ชุด และชุดไทยสากล 5 ชุด
$default_thai_dresses = [
    [
        'id' => 1,
        'image_path' => 'gal_thai_siwalai.jpg',
        'caption' => 'ชุดไทยศิวาลัย - Siwalai Royal Gold',
        'img_price' => 15900
    ],
    [
        'id' => 2,
        'image_path' => 'gal_thai_boromphiman.jpg',
        'caption' => 'ชุดไทยบรมพิมาน - Classic Ivory Boromphiman',
        'img_price' => 13900
    ],
    [
        'id' => 3,
        'image_path' => 'gal_thai_chakkraphat.jpg',
        'caption' => 'ชุดไทยจักรพรรดิ - Imperial Masterpiece',
        'img_price' => 18900
    ],
    [
        'id' => 4,
        'image_path' => 'gal_thai_chakkri.jpg',
        'caption' => 'ชุดไทยจักรี - Golden Blossom Chakkri',
        'img_price' => 12900
    ],
    [
        'id' => 5,
        'image_path' => 'gal_thai_dusit.jpg',
        'caption' => 'ชุดไทยดุสิต - Celestial Pearl Dusit',
        'img_price' => 14500
    ]
];

$default_modern_dresses = [
    [
        'id' => 6,
        'image_path' => 'gal_modern_siwalai.jpg',
        'caption' => 'ชุดไทยประยุกต์ศิวาลัย - Modern Couture Siwalai',
        'img_price' => 16900
    ],
    [
        'id' => 7,
        'image_path' => 'gal_modern_mermaid.jpg',
        'caption' => 'ชุดไทยเมอร์เมดร่วมสมัย - Mermaid Contemporary Thai',
        'img_price' => 15500
    ],
    [
        'id' => 8,
        'image_path' => 'gal_modern_minimal.jpg',
        'caption' => 'ชุดไทยประยุกต์มินิมอล - Minimalist Pure Ivory',
        'img_price' => 12900
    ],
    [
        'id' => 9,
        'image_path' => 'gal_modern_lanna.jpg',
        'caption' => 'ชุดไทยโมเดิร์นล้านนา - Modern Lanna Royal Fusion',
        'img_price' => 14900
    ],
    [
        'id' => 10,
        'image_path' => 'gal_modern_rosegold.jpg',
        'caption' => 'ชุดไทยร่วมสมัยโรสโกลด์ - Rose Gold Contemporary Thai',
        'img_price' => 17500
    ]
];

// ดึงข้อมูลบริการหลักและรายการชุดจากฐานข้อมูลด้วย MySQLi Prepared Statements
if (isset($conn) && $conn) {
    // ระบุคอลัมน์ชัดเจนตาม SQLCodingGuide.md ข้อ 1
    $sql_services = "SELECT id, title, icon, description, service_type, price FROM services ORDER BY id ASC";
    $query = mysqli_query($conn, $sql_services);
    
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $service_id = (int)$row['id'];
            $service_items = [];

            // Prepared Statement สำหรับ service_images ตาม SQLCodingGuide.md ข้อ 2
            $sql_images = "SELECT id, service_id, image_path, caption, img_price FROM service_images WHERE service_id = ? ORDER BY id ASC";
            $stmt_img = mysqli_prepare($conn, $sql_images);

            if ($stmt_img) {
                mysqli_stmt_bind_param($stmt_img, "i", $service_id);
                mysqli_stmt_execute($stmt_img);
                $res_images = mysqli_stmt_get_result($stmt_img);

                while ($img_row = mysqli_fetch_assoc($res_images)) {
                    $service_items[] = [
                        'id'         => (int)$img_row['id'],
                        'image_path' => $img_row['image_path'],
                        'caption'    => $img_row['caption'],
                        'img_price'  => (int)$img_row['img_price']
                    ];
                }
                mysqli_stmt_close($stmt_img);
            }

            // หากในฐานข้อมูลยังไม่มี ให้ใช้ชุดข้อมูล Fallback
            if (empty($service_items)) {
                if (mb_strpos($row['title'], 'สากล') !== false || mb_strpos($row['service_type'], 'สากล') !== false) {
                    $service_items = $default_modern_dresses;
                } else {
                    $service_items = $default_thai_dresses;
                }
            }

            $icon_file = !empty($row['icon']) ? $row['icon'] : ($service_id === 25 ? 'icon_modern_thai.jpg' : 'icon_thai.jpg');
            $desc_text = !empty($row['description']) ? $row['description'] : (
                $service_id === 25 
                ? 'ผสมผสานอัตลักษณ์ไทยเข้ากับความหรูหราสากล ดีไซน์ร่วมสมัยโดดเด่นไม่ซ้ำใคร' 
                : 'สัมผัสความวิจิตรบรรจงแห่งชุดไทยพระราชนิยม ทรงคุณค่า สง่างามเหนือกาลเวลา'
            );

            $services[] = [
                'db_id'       => $service_id,
                'id'          => 'modal' . $service_id,
                'icon'        => $icon_file,
                'title'       => $row['title'],
                'description' => $desc_text,
                'type'        => $row['service_type'],
                'price'       => (float)$row['price'],
                'items'       => $service_items
            ];
        }
    }
}

// Fallback หากฐานข้อมูลเชื่อมต่อไม่ได้
if (empty($services)) {
    $services = [
        [
            'db_id'       => 24,
            'id'          => 'modal24',
            'icon'        => 'icon_thai.jpg',
            'title'       => 'ชุดไทย',
            'description' => 'สัมผัสความวิจิตรบรรจงแห่งชุดไทยพระราชนิยม ทรงคุณค่า สง่างามเหนือกาลเวลา',
            'type'        => 'ชุดไทย',
            'price'       => 999,
            'items'       => $default_thai_dresses
        ],
        [
            'db_id'       => 25,
            'id'          => 'modal25',
            'icon'        => 'icon_modern_thai.jpg',
            'title'       => 'ชุดไทยสากล',
            'description' => 'ผสมผสานอัตลักษณ์ไทยเข้ากับความหรูหราสากล ดีไซน์ร่วมสมัยโดดเด่นไม่ซ้ำใคร',
            'type'        => 'ชุดไทยสากล',
            'price'       => 999,
            'items'       => $default_modern_dresses
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บริการชุดไทยและชุดไทยสากล - Eternal Love</title>
    
    <!-- Bootstrap 5.3.0 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS (Luxury Wedding Standards) -->
    <link rel="stylesheet" href="CSS/style_services.css?v=<?= time(); ?>">
</head>
<body>

    <!-- แถบเมนูนำทางหลัก -->
    <?php include('navbar.php'); ?>

    <!-- ========================================== -->
    <!-- 2. PRESENTATION ZONE (โซนแสดงผลหน้าเว็บ)  -->
    <!-- ========================================== -->
    <main class="container pb-5 mt-4">
        <!-- ส่วนหัวข้อหลักตาม Semantic HTML -->
        <header class="text-center mb-5">
            <span class="section-subtitle d-block mb-2">Exquisite Wedding Wardrobe</span>
            <h1 class="section-title">Our Bridal Services</h1>
            <div class="title-line"></div>
            <p class="text-white-50 mx-auto" style="max-width: 600px; font-size: 0.95rem;">
                คัดสรรชุดแต่งงานไทยโบราณและชุดไทยประยุกต์ร่วมสมัยระดับพรีเมียม ตัดเย็บประณีตด้วยผ้าไหมแท้และงานปักวิจิตรศิลป์
            </p>
        </header>

        <!-- รายการการ์ดบริการหลัก -->
        <div class="row g-4 justify-content-center">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $s): ?>
                <div class="col-lg-5 col-md-6">
                    <div class="card h-100 service-card text-center shadow-sm" 
                         role="button"
                         tabindex="0"
                         data-bs-toggle="modal" 
                         data-bs-target="#<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?>"
                         aria-haspopup="dialog">
                        <div class="card-body d-flex flex-column align-items-center">
                            <img src="img/icons/<?= htmlspecialchars($s['icon'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 onerror="this.src='https://placehold.co/100x100?text=Service'" 
                                 alt="<?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="service-icon-img shadow-sm"
                                 loading="lazy">
                            
                            <h3 class="fw-bold fs-4 mb-2" style="letter-spacing: 1px; color: var(--dark-wedding);">
                                <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </h3>
                            
                            <p class="small text-muted mb-4 px-2" style="line-height: 1.7;">
                                <?= htmlspecialchars($s['description'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <div class="mt-auto w-100 pt-2 border-top border-light">
                                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold mb-2 d-inline-block">
                                    <i class="fa-solid fa-gem text-gold me-1" style="color: var(--gold-primary);"></i> 
                                    มีให้เลือก <?= count($s['items']); ?> คอลเลกชัน
                                </span>
                                <p class="small text-gold mb-0 fw-bold" style="color: var(--gold-primary);">
                                    เริ่มต้น ฿<?= number_format($s['price']); ?>
                                </p>
                                <span class="small text-muted text-uppercase d-block mt-1" style="letter-spacing: 2px; font-size: 0.72rem;">
                                    คลิกเพื่อเลือกชุด <i class="fa-solid fa-arrow-right ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- ฟอร์มส่งข้อมูลไปยังหน้าจองคิว (booking.php) -->
    <form id="redirectForm" action="booking.php" method="GET">
        <input type="hidden" name="selected_item" id="hiddenItem">
        <input type="hidden" name="service_type" id="hiddenService">
        <input type="hidden" name="price" id="hiddenPrice">
    </form>

    <!-- ========================================== -->
    <!-- 3. MODAL SELECTION ZONE (โมดอลเลือกชุด)    -->
    <!-- ========================================== -->
    <?php if (!empty($services)): ?>
        <?php foreach ($services as $s): ?>
        <div class="modal fade" 
             id="<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?>" 
             tabindex="-1" 
             aria-labelledby="<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?>Label" 
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content shadow-lg">
                    <!-- Modal Header -->
                    <div class="modal-header p-4">
                        <div>
                            <span class="badge mb-1 px-3 py-1 text-uppercase" style="background: var(--gold-primary); color: #fff; font-size: 0.75rem;">
                                <?= htmlspecialchars($s['type'], ENT_QUOTES, 'UTF-8'); ?> Collection
                            </span>
                            <h2 class="modal-title fs-4 mb-0 fw-bold" id="<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?>Label">
                                คอลเลกชัน<?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8'); ?> (<?= count($s['items']); ?> ชุด)
                            </h2>
                            <p class="small text-muted mb-0 mt-1">
                                <?= htmlspecialchars($s['description'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <p class="small text-muted mb-3">
                            <i class="fa-solid fa-circle-info me-1" style="color: var(--gold-primary);"></i>
                            กรุณาคลิกเลือกชุดที่คุณชื่นชอบเพื่อดูรายละเอียดและดำเนินการจองบริการ
                        </p>
                        <div class="row g-3">
                            <?php foreach ($s['items'] as $item): ?>
                            <div class="col-lg-2-4 col-md-4 col-6">
                                <label class="item-card-wrapper">
                                    <input type="radio" 
                                           name="item_select" 
                                           class="item-radio d-none" 
                                           value="<?= htmlspecialchars($s['title'] . ' - ' . $item['caption'], ENT_QUOTES, 'UTF-8'); ?>"
                                           data-price="<?= (int)$item['img_price']; ?>">
                                    <div class="item-content text-center shadow-sm">
                                        <div class="item-thumb-container">
                                            <span class="select-indicator">
                                                <i class="fa-solid fa-check"></i>
                                            </span>
                                            <img src="img/gallery/<?= htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                                                 alt="<?= htmlspecialchars($item['caption'], ENT_QUOTES, 'UTF-8'); ?>" 
                                                 class="img-fluid"
                                                 loading="lazy"
                                                 onerror="this.src='https://placehold.co/400x520/33261c/c5a059?text=Wedding+Gown'">
                                        </div>
                                        <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                                            <div class="small fw-bold text-dark mb-2" style="font-size: 0.85rem; line-height: 1.4;">
                                                <?= htmlspecialchars($item['caption'], ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                            <div>
                                                <span class="price-badge d-inline-block">
                                                    ฿<?= number_format($item['img_price']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer p-4 border-top bg-white">
                        <div class="w-100 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                            <span class="small text-muted">
                                <i class="fa-solid fa-shield-halved me-1 text-gold" style="color: var(--gold-primary);"></i> บริการปรับแก้ไซส์ฟรีและรวมเครื่องประดับชุดไทยครบเซต
                            </span>
                            <button type="button" 
                                    class="btn btn-luxury px-5 py-2" 
                                    onclick="submitSelection('<?= htmlspecialchars($s['type'], ENT_QUOTES, 'UTF-8'); ?>')">
                                ยืนยันการเลือกและจองบริการ <i class="fa-solid fa-chevron-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Custom Style for 5 columns in a row on Desktop -->
    <style>
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 auto;
                width: 20%;
            }
        }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /**
         * ส่งค่าชุดที่เลือกไปยังหน้าจองคิว (booking.php)
         * ตาม JavascriptCodingGuide.md (Vanilla ES6+, strict checking)
         */
        function submitSelection(service) {
            const activeModal = document.querySelector('.modal.show');
            if (!activeModal) {
                return;
            }
            
            const selected = activeModal.querySelector('input[name="item_select"]:checked');
            
            if (selected) {
                const itemValue = selected.value;
                const itemPrice = selected.getAttribute('data-price') || '0';
                
                document.getElementById('hiddenItem').value = itemValue;
                document.getElementById('hiddenService').value = service;
                document.getElementById('hiddenPrice').value = itemPrice;
                document.getElementById('redirectForm').submit();
            } else {
                alert('กรุณาคลิกเลือกชุดที่คุณต้องการก่อนยืนยัน');
            }
        }
    </script>
</body>
</html>