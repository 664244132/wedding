# Thai & Modern Thai Wedding Dresses in services.php Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create and add 5 Thai traditional outfits and 5 Modern Thai hybrid outfits in `services.php` (displayed in modals when clicking each respective service) with MySQL database integration, resilient fallback, luxury wedding styling, and strict adherence to all project markdown coding standards.

**Architecture:** Two-zone architecture in `services.php` separating Data Preparation (PHP MySQLi prepared statements + XSS-safe preparation) and Presentation (Semantic HTML5 + Bootstrap 5.3 + Luxury CSS tokens + Vanilla ES6+ JS). Database records are seeded in `service_images` for `service_id = 24` (ชุดไทย) and `service_id = 25` (ชุดไทยสากล) alongside curated image assets in `img/gallery/`.

**Tech Stack:** PHP 8+ (Procedural MySQLi), MariaDB / MySQL (`wedding_db`), HTML5 / Bootstrap 5.3.0, Vanilla JavaScript (ES6+), Custom CSS with Luxury Wedding Design Tokens.

## Global Constraints
- Strictly follow `markdowns/PHPCodingGuide.md` (Prepared statements, Universal escaping with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`, 2-zone structure).
- Strictly follow `markdowns/SQLCodingGuide.md` (Explicit SELECT columns, Parameterized queries with `mysqli_stmt_bind_param`, `utf8mb4`).
- Strictly follow `markdowns/HTMLCodingGuide.md` (Semantic HTML, `lang="th"`, proper modal `aria-*` tags, `img-fluid`, `loading="lazy"`, descriptive `alt`).
- Strictly follow `markdowns/CSSCodingGuide.md` (Wedding design tokens `--gold-primary: #c5a059;`, `--dark-wedding: #4a3b2b;`, smooth transitions, accessible focus rings).
- Strictly follow `markdowns/JavascriptCodingGuide.md` (Vanilla ES6+, strict `===`, `Intl.NumberFormat('th-TH')`).

---

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

---

### Task 2: Refactor `services.php` Backend (Data Preparation Zone)

**Files:**
- Modify: `services.php:1-25`

**Interfaces:**
- Consumes: `wedding_db` tables `services` and `service_images` via `config.php`
- Produces: `$services` array populated with prepared statement results and embedded items array

- [ ] **Step 1: Refactor top section to comply with `PHPCodingGuide.md` and `SQLCodingGuide.md`**

Replace the top query block in `services.php` with:
- Safe session check: `if (session_status() === PHP_SESSION_NONE) { session_start(); }`
- Explicit SELECT columns: `SELECT id, title, icon, description, service_type, price FROM services ORDER BY id ASC`
- Prepared statement for `service_images`:
  `SELECT id, service_id, image_path, caption, img_price FROM service_images WHERE service_id = ? ORDER BY id ASC`
- Graceful in-code fallback array if database has 0 items so it always shows the 5 outfits for each category.

- [ ] **Step 2: Run syntax check with PHP CLI**

Run: `C:\xampp\php\php.exe -l services.php`
Expected: `No syntax errors detected in services.php`

- [ ] **Step 3: Test backend data retrieval using dry-run CLI**

Run: `C:\xampp\php\php.exe -r "require 'services.php'; echo 'Loaded services count: ' . count(\$services);"`
Expected: `Loaded services count: 2`

- [ ] **Step 4: Commit**

```bash
git add services.php
git commit -m "refactor(services): implement two-zone architecture and prepared statements"
```

---

### Task 3: Refactor `services.php` Presentation Zone & UI/UX (Luxury Wedding Standards)

**Files:**
- Modify: `services.php:26-166`
- Modify: `CSS/style_services.css`

**Interfaces:**
- Consumes: `$services` array containing 5 items for `ชุดไทย` and 5 items for `ชุดไทยสากล`
- Produces: Semantic HTML5 view with luxury wedding cards, responsive modals, radio selection, and JS redirect to `booking.php`

- [ ] **Step 1: Enhance CSS in `CSS/style_services.css` for Luxury Wedding styling**

Add luxury wedding variables, card styles, and selection badge styles:
- Luxury color variables matching `CSSCodingGuide.md`: `--gold-primary: #c5a059`, `--dark-wedding: #4a3b2b`, `--soft-cream: #fdfaf5`
- Card hover elevation (`transform: translateY(-8px)`)
- Selected dress card highlight: border 2px solid gold, gold shadow, selection checkmark badge
- Luxury modal headers and buttons

- [ ] **Step 2: Refactor HTML & Modals in `services.php`**

Ensure:
- Universal output escaping using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- Modal dialog attributes: `aria-labelledby`, `aria-hidden="true"`, `tabindex="-1"`
- Responsive images with `img-fluid`, `alt` attribute, and `loading="lazy"`
- Radio buttons for selecting dresses with clear thumbnail, caption, and price badge
- Clean Vanilla JavaScript (ES6+) passing `selected_item`, `service_type`, and `price` to `booking.php`
- Fallback image handlers (`onerror`)

- [ ] **Step 3: Validate PHP syntax and output**

Run: `C:\xampp\php\php.exe -l services.php`
Expected: `No syntax errors detected in services.php`

- [ ] **Step 4: Commit**

```bash
git add services.php CSS/style_services.css
git commit -m "feat(services): enhance luxury wedding UI, accessible modals, and dress selection"
```

---

### Task 4: Full System Verification & End-to-End Testing

**Files:**
- Test script: `scratch/test_services.php`

**Interfaces:**
- Consumes: `services.php` output and `booking.php` parameters
- Produces: Verification report confirming all 10 outfits are present, security requirements are met, and booking redirect works

- [ ] **Step 1: Write integration verification script**

Create `scratch/test_services.php` that:
- Loads `services.php`
- Asserts that both service modals exist (`#modal24` and `#modal25`)
- Asserts that all 5 Thai outfits appear in `#modal24`
- Asserts that all 5 Modern Thai outfits appear in `#modal25`
- Asserts that no unescaped strings or SQL errors exist in the output
- Verifies that `booking.php?ajax_s_id=24` and `booking.php?ajax_s_id=25` return JSON with exactly 5 items each

- [ ] **Step 2: Run verification script**

Run: `C:\xampp\php\php.exe scratch/test_services.php`
Expected: All tests PASS

- [ ] **Step 3: Commit and cleanup scratch scripts**

```bash
git add scratch/
git commit -m "test: add integration test suite for services and verify all 10 outfits"
```
