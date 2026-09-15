### Task 1: Database Seed & Media Assets Setup

**Files:**
- Create: `img/icons/icon_thai.jpg`
- Create: `img/icons/icon_modern_thai.jpg`
- Create: `img/gallery/gal_thai_siwalai.jpg`
- Create: `img/gallery/gal_thai_boromphiman.jpg`
- Create: `img/gallery/gal_thai_chakkraphat.jpg`
- Create: `img/gallery/gal_thai_chakkri.jpg`
- Create: `img/gallery/gal_thai_dusit.jpg`
- Create: `img/gallery/gal_modern_siwalai.jpg`
- Create: `img/gallery/gal_modern_mermaid.jpg`
- Create: `img/gallery/gal_modern_minimal.jpg`
- Create: `img/gallery/gal_modern_lanna.jpg`
- Create: `img/gallery/gal_modern_rosegold.jpg`
- Create/Run: `scratch/seed_dresses.php`

**Interfaces:**
- Consumes: MySQL `wedding_db` via `config.php`
- Produces: 10 rows in `service_images` (5 for `service_id=24`, 5 for `service_id=25`) and image files in `img/gallery/` and `img/icons/`

- [ ] **Step 1: Write seed script with image creation and database population**

```php
<?php
// scratch/seed_dresses.php
require_once __DIR__ . '/../../config.php';

// Ensure directories exist
$dirs = [__DIR__ . '/../../img/icons', __DIR__ . '/../../img/gallery'];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 1. Update services description and icon if needed
$update_services = [
    [
        'id' => 24,
        'title' => 'ชุดไทย',
        'icon' => 'icon_thai.jpg',
        'desc' => 'สัมผัสความวิจิตรบรรจงแห่งชุดไทยพระราชนิยม ทรงคุณค่า สง่างามเหนือกาลเวลา'
    ],
    [
        'id' => 25,
        'title' => 'ชุดไทยสากล',
        'icon' => 'icon_modern_thai.jpg',
        'desc' => 'ผสมผสานอัตลักษณ์ไทยเข้ากับความหรูหราสากล ดีไซน์ร่วมสมัยโดดเด่นไม่ซ้ำใคร'
    ]
];

foreach ($update_services as $srv) {
    $stmt = mysqli_prepare($conn, "UPDATE services SET icon = ?, description = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $srv['icon'], $srv['desc'], $srv['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// 2. Clear previous items for services 24 & 25 and insert 10 curated dresses
$stmt_del = mysqli_prepare($conn, "DELETE FROM service_images WHERE service_id IN (24, 25)");
mysqli_stmt_execute($stmt_del);
mysqli_stmt_close($stmt_del);

$dresses = [
    // ชุดไทย (service_id = 24)
    [24, 'gal_thai_siwalai.jpg', 'ชุดไทยศิวาลัย - Siwalai Royal Gold', 15900],
    [24, 'gal_thai_boromphiman.jpg', 'ชุดไทยบรมพิมาน - Classic Ivory Boromphiman', 13900],
    [24, 'gal_thai_chakkraphat.jpg', 'ชุดไทยจักรพรรดิ - Imperial Masterpiece', 18900],
    [24, 'gal_thai_chakkri.jpg', 'ชุดไทยจักรี - Golden Blossom Chakkri', 12900],
    [24, 'gal_thai_dusit.jpg', 'ชุดไทยดุสิต - Celestial Pearl Dusit', 14500],
    
    // ชุดไทยสากล (service_id = 25)
    [25, 'gal_modern_siwalai.jpg', 'ชุดไทยประยุกต์ศิวาลัย - Modern Couture Siwalai', 16900],
    [25, 'gal_modern_mermaid.jpg', 'ชุดไทยเมอร์เมดร่วมสมัย - Mermaid Contemporary Thai', 15500],
    [25, 'gal_modern_minimal.jpg', 'ชุดไทยประยุกต์มินิมอล - Minimalist Pure Ivory', 12900],
    [25, 'gal_modern_lanna.jpg', 'ชุดไทยโมเดิร์นล้านนา - Modern Lanna Royal Fusion', 14900],
    [25, 'gal_modern_rosegold.jpg', 'ชุดไทยร่วมสมัยโรสโกลด์ - Rose Gold Contemporary Thai', 17500]
];

$stmt_ins = mysqli_prepare($conn, "INSERT INTO service_images (service_id, image_path, caption, img_price) VALUES (?, ?, ?, ?)");
foreach ($dresses as $d) {
    mysqli_stmt_bind_param($stmt_ins, "issi", $d[0], $d[1], $d[2], $d[3]);
    mysqli_stmt_execute($stmt_ins);
}
mysqli_stmt_close($stmt_ins);

echo "Database seeded successfully!\n";
```

- [ ] **Step 2: Run seed script via PHP CLI**

Run: `C:\xampp\php\php.exe scratch/seed_dresses.php`
Expected: Output `Database seeded successfully!`

- [ ] **Step 3: Generate or place elegant wedding image placeholders in `img/gallery/` and `img/icons/`**

Create placeholder/curated image files using GD/cURL in PHP script so that all 10 outfit images and 2 service icons are physically present and valid images on the filesystem.

- [ ] **Step 4: Verify image files and database rows**

Run: `C:\xampp\php\php.exe -r "require 'config.php'; \$res = mysqli_query(\$conn, 'SELECT count(*) as c FROM service_images WHERE service_id IN (24, 25)'); echo 'Rows: ' . mysqli_fetch_assoc(\$res)['c'];"`
Expected: `Rows: 10`

- [ ] **Step 5: Commit changes**

```bash
git add scratch/ img/
git commit -m "feat: seed 10 Thai and Modern Thai outfits into database and prepare image assets"
```
