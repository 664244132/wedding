# 📘 แนวทางการเขียนและรีวิวโค้ด SQL ที่ดี (SQL & MySQL / MariaDB Best Practices)

เอกสารนี้รวบรวมหลักการและมาตรฐานการเขียนคำสั่ง SQL สำหรับโปรเจกต์ **Eternal Love (สตูดิโอแต่งงานและพรีเวดดิ้ง)** ซึ่งใช้ระบบจัดการฐานข้อมูล **MySQL / MariaDB (`wedding_db`)** ร่วมกับภาษา **PHP (Procedural MySQLi)** เพื่อให้ระบบทำงานได้อย่างรวดเร็ว ปลอดภัย และง่ายต่อการดูแลรักษาสำหรับผู้เริ่มต้น

---

## 1. เลือกดึงเฉพาะคอลัมน์ที่จำเป็น (Explicit SELECT Columns)
- **แนวทางปฏิบัติ:** หลีกเลี่ยงการใช้ `SELECT *` ในส่วนที่ต้องการแสดงผลจริง ควรระบุชื่อคอลัมน์ที่ต้องการใช้งานอย่างชัดเจน
- **เหตุผล:** 
  1. ลดภาระการส่งข้อมูลผ่าน Network ระหว่าง Database Server กับ PHP (โดยเฉพาะตารางที่มีข้อความยาวหรือรูปภาพ เช่น `packages`, `reviews`)
  2. ป้องกันการรั่วไหลของข้อมูลที่ละเอียดอ่อน (เช่น คอลัมน์ `password` ในตาราง `users`)
  3. ทำให้โค้ดอ่านง่ายและชัดเจนว่าหน้าดังกล่าวใช้ฟิลด์ข้อมูลใดบ้าง

```sql
-- ❌ เลี่ยงการใช้ (ดึงข้อมูลมาทั้งหมดโดยไม่จำเป็น)
SELECT * FROM packages ORDER BY package_price ASC;

-- ✅ ถูกต้องและแนะนำ (ระบุเฉพาะคอลัมน์ที่นำไปแสดงผลบนการ์ด)
SELECT id, package_name, package_price, package_detail, package_image 
FROM packages 
ORDER BY package_price ASC;
```

---

## 2. ใช้ Parameterized Queries ผ่าน MySQLi Prepared Statements เสมอ
- **แนวทางปฏิบัติ:** ข้อมูลใดๆ ที่รับมาจากผู้ใช้ (`$_POST`, `$_GET`, หรือ Session) ที่นำมาเป็นเงื่อนไขในคำสั่ง SQL **ต้องใช้ Prepared Statements (`mysqli_prepare` และ `mysqli_stmt_bind_param`) เสมอ**
- **เหตุผล:** ป้องกันช่องโหว่ **SQL Injection ได้ 100%** โดยแยกโครงสร้างคำสั่ง SQL ออกจากข้อมูลอย่างแท้จริง แม้กระทั่งการใช้ `mysqli_real_escape_string()` ก็ยังมีความเสี่ยงหากใช้กับค่าตัวเลขที่ไม่ได้ใส่เครื่องหมายคำพูด (Quote)

```php
// ❌ ไม่ปลอดภัย (มีความเสี่ยงต่อ SQL Injection หากนำค่าจากภายนอกมาต่อสตริงโดยตรง)
$sql = "SELECT id, username, role FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
$result = mysqli_query($conn, $sql);

// ✅ ถูกต้อง ปลอดภัย และเข้าใจง่าย (ใช้ mysqli Prepared Statement)
$sql = "SELECT id, username, password, role FROM users WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // "s" หมายถึงชนิดข้อมูล String
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
```

### ตารางชนิดตัวแปรสำหรับ `mysqli_stmt_bind_param`:
| สัญลักษณ์ | ชนิดข้อมูล | ตัวอย่างการใช้งาน |
| :--- | :--- | :--- |
| `i` | Integer (ตัวเลขจำนวนเต็ม) | `id`, `price`, `rating`, `service_id` |
| `d` | Double / Float (ทศนิยม) | `latitude`, `longitude` |
| `s` | String (ข้อความ วันที่) | `username`, `email`, `booking_date`, `status` |
| `b` | Blob (ไบนารี) | ข้อมูลไฟล์ดิบ (ปกติแนะนำให้เก็บเป็นชื่อไฟล์แบบ String) |

---

## 3. การจัดการภาษาไทยและการตั้งค่า Character Set (`utf8mb4`)
- **แนวทางปฏิบัติ:** ต้องกำหนด Charset ของการเชื่อมต่อให้เป็น `utf8mb4` เสมอในไฟล์ `config.php`
- **เหตุผล:** เพื่อให้สามารถจัดเก็บภาษาไทย อีโมจิ หรืออักขระพิเศษได้อย่างสมบูรณ์ โดยไม่เกิดปัญหาตัวอักษรกลายเป็นเครื่องหมายคำถาม (`???`) หรือตัวอักขระเพี้ยน

```php
// กำหนด Charset หลังเชื่อมต่อฐานข้อมูลทันทีใน config.php
mysqli_set_charset($conn, "utf8mb4");
```

```sql
-- โครงสร้างตารางและ Collation ที่แนะนำสำหรับฐานข้อมูล wedding_db
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `review_text` TEXT NOT NULL,
    `review_image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. การจัดการค่า NULL และพิกัดสถานที่ (NULL Handling)
- **แนวทางปฏิบัติ:** ในระบบการจองคิว (`bookings`) ลูกค้าอาจเลือกการจัดงาน "ในสตูดิโอ" หรือ "นอกสถานที่" กรณีไม่มีการระบุพิกัด ต้องจัดการค่า `NULL` ให้ถูกต้องด้วยคำสั่ง `IS NULL` หรือ `IS NOT NULL` ห้ามนำค่ามาเปรียบเทียบด้วยเครื่องหมายเท่ากับ (`= NULL`)
- **ตัวอย่าง:**
```sql
-- ❌ ผิด (ค่า NULL ไม่สามารถเปรียบเทียบด้วยเครื่องหมาย = ได้)
SELECT id, username, booking_date FROM bookings WHERE latitude = NULL;

-- ✅ ถูกต้อง (ใช้ IS NULL ในการตรวจสอบ)
SELECT id, username, booking_date, location_type 
FROM bookings 
WHERE location_type = 'ในสตูดิโอ' OR latitude IS NULL;
```

---

## 5. การจัดเรียงข้อมูลและการแบ่งหน้า (Sorting & Pagination)
- **แนวทางปฏิบัติ:** 
  1. การดึงข้อมูลล่าสุดเพื่อนำไปแสดงผล (เช่น รีวิว, ข้อความติดต่อ, รายการจองคิว) ให้ใช้ `ORDER BY id DESC` หรือ `ORDER BY created_at DESC`
  2. การดึงแพ็กเกจแนะนำหรือเรียงตามราคา ให้ใช้ `ORDER BY package_price ASC`
  3. หากมีการทำระบบแบ่งหน้า (Pagination) ต้องตรวจสอบว่าค่า `LIMIT` และ `OFFSET` ผ่านการแปลงเป็น Integer ด้วย `intval()` ก่อนเสมอ

```php
// ตัวอย่างการคิวรีแบบกำหนดจำนวนรายการอย่างปลอดภัย
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT id, image_path, caption FROM gallery ORDER BY id DESC LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $offset, $limit);
mysqli_stmt_execute($stmt);
$gallery_items = mysqli_stmt_get_result($stmt);
```

---

## 6. โครงสร้างฐานข้อมูลอ้างอิงของระบบ Eternal Love (Database Schema Reference)
ตรวจสอบโครงสร้างคอลัมน์จริงของฐานข้อมูล `wedding_db` เสมอ เพื่อป้องกันข้อผิดพลาดในการตั้งชื่อฟิลด์:

### 1) ตาราง `users` (ข้อมูลผู้ใช้งานและผู้ดูแลระบบ)
* `id` : INT (Primary Key, Auto Increment)
* `username` : VARCHAR(100) — ชื่อผู้ใช้หรือชื่อ-นามสกุล
* `email` : VARCHAR(100) — อีเมลสำหรับเข้าสู่ระบบ (Unique)
* `password` : VARCHAR(255) — รหัสผ่าน
* `role` : ENUM('Admin', 'Customer') — ระดับสิทธิ์การเข้าใช้งาน

### 2) ตาราง `services` (หมวดหมู่บริการหลัก)
* `id` : INT (Primary Key, Auto Increment)
* `title` : VARCHAR(150) — ชื่อบริการ (เช่น ถ่ายภาพพรีเวดดิ้ง, ชุดแต่งงาน)
* `icon` : VARCHAR(255) — ชื่อไฟล์ไอคอนหรือรูปภาพตัวแทนบริการ
* `service_type` : VARCHAR(100) — ประเภทบริการ
* `price` : INT / DECIMAL(10,2) — ราคาเริ่มต้นของบริการ

### 3) ตาราง `service_images` (สไตล์ภาพ/ชุดย่อยในแต่ละบริการ สำหรับตัวเลือกการจอง)
* `id` : INT (Primary Key, Auto Increment)
* `service_id` : INT — เชื่อมโยงกับ `services.id` (Foreign Key)
* `image_path` : VARCHAR(255) — ชื่อไฟล์ภาพตัวอย่างสไตล์
* `caption` : VARCHAR(150) — ชื่อสไตล์หรือชุด (เช่น ชุดไทยศิวาลัย, Minimal Studio)
* `img_price` : INT / DECIMAL(10,2) — ราคาเพิ่มเติมของสไตล์นั้นๆ

### 4) ตาราง `packages` (แพ็กเกจแต่งงานและราคา)
* `id` : INT (Primary Key, Auto Increment)
* `package_name` : VARCHAR(150) — ชื่อแพ็กเกจ (เช่น Silver Romantic, Gold Eternity)
* `package_price` : DECIMAL(10,2) / INT — ราคาแพ็กเกจ
* `package_detail` : TEXT — รายละเอียดและสิทธิประโยชน์ของแพ็กเกจ
* `package_image` : VARCHAR(255) — ชื่อไฟล์ภาพประกอบแพ็กเกจ

### 5) ตาราง `bookings` (รายการจองคิวบริการจากลูกค้า)
* `id` : INT (Primary Key, Auto Increment)
* `username` : VARCHAR(100) — ชื่อลูกค้าผู้ทำการจอง
* `phone` : VARCHAR(20) — เบอร์โทรศัพท์ติดต่อ
* `booking_date` : DATE — วันที่นัดหมายใช้บริการ
* `services` : TEXT — รายการบริการและสไตล์ที่เลือก
* `note` : TEXT — หมายเหตุเพิ่มเติมจากลูกค้า
* `location_type` : VARCHAR(50) — ประเภทสถานที่ ('ในสตูดิโอ' หรือ 'นอกสถานที่')
* `latitude` : DECIMAL(10,8) NULL — พิกัดละติจูด (จาก Leaflet Map)
* `longitude` : DECIMAL(11,8) NULL — พิกัดลองจิจูด (จาก Leaflet Map)
* `status` : VARCHAR(50) DEFAULT 'pending' — สถานะการจอง ('pending', 'confirmed', 'cancelled')

### 6) ตาราง `contacts` (ข้อความติดต่อสอบถาม)
* `id` : INT (Primary Key, Auto Increment)
* `name` : VARCHAR(100) — ชื่อผู้ติดต่อ
* `email` : VARCHAR(100) — อีเมลผู้ติดต่อ
* `subject` : VARCHAR(200) — หัวข้อที่สอบถาม
* `message` : TEXT — เนื้อหาข้อความ
* `created_at` : TIMESTAMP DEFAULT CURRENT_TIMESTAMP — วันเวลาที่ส่งข้อความ

### 7) ตาราง `gallery` (รูปภาพผลงานในแกลเลอรี)
* `id` : INT (Primary Key, Auto Increment)
* `image_path` : VARCHAR(255) — ชื่อไฟล์ภาพผลงาน
* `caption` : VARCHAR(200) NULL — คำบรรยายภาพหรือหมวดหมู่

### 8) ตาราง `reviews` (รีวิวความประทับใจจากลูกค้า)
* `id` : INT (Primary Key, Auto Increment)
* `username` : VARCHAR(100) — ชื่อลูกค้าผู้ให้รีวิว
* `rating` : INT DEFAULT 5 — คะแนนความประทับใจ (1 - 5 ดาว)
* `review_text` : TEXT — ข้อความรีวิว
* `review_image` : VARCHAR(255) NULL — ชื่อไฟล์รูปถ่ายที่ลูกค้าแนบมา
* `created_at` : TIMESTAMP DEFAULT CURRENT_TIMESTAMP — วันเวลาที่บันทึกรีวิว

---

## 7. ตัวอย่างการเชื่อมโยงข้อมูลแบบสัมพันธ์ (JOIN Queries)
ในการแสดงผลข้อมูลบริการพร้อมรูปภาพสไตล์ย่อย สามารถใช้ `LEFT JOIN` หรือ `INNER JOIN` เพื่อดึงข้อมูลพร้อมกันได้อย่างมีประสิทธิภาพ:

```sql
-- ตัวอย่างการดึงข้อมูลบริการหลักพร้อมรายการสไตล์ภาพตัวอย่าง
SELECT 
    s.id AS service_id,
    s.title AS service_title,
    s.price AS base_price,
    si.id AS image_id,
    si.caption AS style_name,
    si.image_path,
    si.img_price
FROM services s
LEFT JOIN service_images si ON s.id = si.service_id
ORDER BY s.id ASC, si.id ASC;
```