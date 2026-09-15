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
