# 🐘 คู่มือการเขียนและรีวิวโค้ด PHP: มาตรฐานสถาปัตยกรรม Eternal Love (PHP Procedural + MySQLi Best Practices)

เอกสารนี้รวบรวมหลักการและข้อกำหนดในการเขียนโค้ด **PHP (Procedural)** สำหรับโปรเจกต์ **Eternal Love (สตูดิโอแต่งงานและพรีเวดดิ้ง)** เพื่อรักษาความสะอาด ความปลอดภัย และความเป็นมิตรต่อผู้เริ่มต้น (Beginner-Friendly)

---

## 1. การใช้ MySQLi Prepared Statements สำหรับฐานข้อมูล
- **สิ่งที่ต้องทำ:** ทุกคำสั่ง SQL ที่มีการรับค่าพารามิเตอร์จากภายนอก (`$_GET`, `$_POST`, `$_SESSION`) ต้องใช้ **Prepared Statements ผ่าน MySQLi** (`mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`)
- **เหตุผล:** ป้องกันช่องโหว่ SQL Injection ได้อย่างสมบูรณ์ และแยกตรรกะของคำสั่ง SQL ออกจากข้อมูลที่รับเข้ามา
- **ข้อห้าม:** ห้ามนำตัวแปรจากภายนอกไปต่อสตริง (Concatenate) ลงในคำสั่ง SQL โดยตรงเด็ดขาด

```php
// ❌ ไม่ปลอดภัย (ห้ามทำเด็ดขาด - เสี่ยงต่อ SQL Injection)
$service_id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM services WHERE id = " . $service_id);

// ✅ ถูกต้องและปลอดภัย (ใช้ MySQLi Prepared Statement)
$service_id = intval($_GET['id'] ?? 0);
$sql = "SELECT id, title, icon, price FROM services WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $service_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $service = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
```

---

## 2. การ Escape ข้อมูลเพื่อป้องกัน XSS (Universal Output Escaping)
- **สิ่งที่ต้องทำ:** เมื่อนำข้อมูลจากฐานข้อมูลหรือตัวแปรจาก Request มาเรนเดอร์ในส่วนแสดงผล HTML ต้องครอบด้วยฟังก์ชัน `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` เสมอ
- **เหตุผล:** ป้องกันการโจมตีแบบ Cross-Site Scripting (XSS) ไม่ให้ข้อมูลที่อาจมีแท็ก `<script>` หรือ HTML แปลกปลอมถูกนำไปประมวลผลบนเบราว์เซอร์ของลูกค้า

```php
<!-- ✅ การแสดงผลข้อมูลที่ปลอดภัยตามมาตรฐานระบบ Eternal Love -->
<h3><?= htmlspecialchars($package['package_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
<p><?= nl2br(htmlspecialchars($package['package_detail'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
<span class="price">฿<?= number_format((float)($package['package_price'] ?? 0), 2) ?></span>
```

---

## 3. การจัดการความปลอดภัยในหน้า Admin และการตรวจสอบสิทธิ์ (Role-Based Access Control)
- **สิ่งที่ต้องทำ:**
  - ทุกไฟล์ที่อยู่ในโฟลเดอร์ `admin/` ต้องเริ่มต้นด้วย `session_start();` และมีโค้ดตรวจสอบสิทธิ์ที่ส่วนบนสุดของไฟล์เสมอ
  - หากไม่มี Session ของแอดมิน หรือ `role` ไม่เท่ากับ `'Admin'` ให้ตัดการทำงานและส่งกลับไปยังหน้า `login.php` ทันที
- **เหตุผล:** ป้องกันผู้ใช้งานทั่วไปหรือผู้ไม่หวังดีเข้าถึงระบบจัดการหลังบ้านโดยพิมพ์ URL ตรงๆ

```php
<?php
// ส่วนหัวของทุกไฟล์ในโฟลเดอร์ admin/
session_start();

if (!isset($_SESSION['admin_name']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

require_once('../config.php');
?>
```

---

## 4. การจัดการอัปโหลดและลบไฟล์รูปภาพอย่างปลอดภัย (Secure File Uploads)
- **สิ่งที่ต้องทำ:**
  - สุ่มตั้งชื่อไฟล์ใหม่ด้วย Timestamp และ Unique ID เสมอ เพื่อป้องกันชื่อไฟล์ซ้ำและการเขียนทับไฟล์เดิม: `time() . '_' . uniqid() . '.' . $extension`
  - ตรวจสอบความถูกต้องของไฟล์รูปภาพด้วย `getimagesize()` และจำกัดนามสกุลไฟล์ที่อนุญาต (เช่น `jpg`, `jpeg`, `png`, `webp`)
  - เมื่อมีการลบข้อมูลจากฐานข้อมูล (เช่น ลบแพ็กเกจ, ลบรูปแกลเลอรี, ลบรีวิว) ต้องลบไฟล์รูปจริงในเครื่องแม่ข่ายด้วยฟังก์ชัน `@unlink()` เพื่อไม่ให้เหลือขยะในระบบ
- **โฟลเดอร์จัดเก็บไฟล์ภาพ:**
  - `uploads/gallery/` : รูปภาพผลงานพรีเวดดิ้ง
  - `uploads/reviews/` : รูปภาพประกอบการรีวิวของลูกค้า
  - `img/packages/` : รูปภาพแพ็กเกจแต่งงาน
  - `img/icons/` : ไอคอนหรือรูปแทนบริการ

```php
// ตัวอย่างการอัปโหลดรูปภาพอย่างปลอดภัย
if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/gallery/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed_extensions)) {
        $check = getimagesize($_FILES['image_file']['tmp_name']);
        if ($check !== false) {
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $target_file = $target_dir . $new_filename;
            move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file);
        }
    }
}
```

---

## 5. การแฮชและการตรวจสอบรหัสผ่าน (Password Hashing with Bcrypt)
- **สิ่งที่ต้องทำ:** ในระบบสมาชิก (`register.php` และ `login.php`) ควรจัดเก็บรหัสผ่านด้วย `password_hash($password, PASSWORD_DEFAULT)` และตรวจสอบด้วย `password_verify($password, $hashed_password)`
- **ข้อกำหนด:** หลีกเลี่ยงการจัดเก็บรหัสผ่านเป็นข้อความธรรมดา (Plain text) เพื่อความปลอดภัยสูงสุดของข้อมูลลูกค้าและทีมงาน

```php
// ✅ ตอนสมัครสมาชิก (บันทึกลง Database)
$hash = password_hash($password, PASSWORD_DEFAULT);

// ✅ ตอนเข้าสู่ระบบ (ตรวจสอบความถูกต้อง)
if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}
```

---

## 6. สถาปัตยกรรมการแบ่งโซนโค้ด (Data Preparation Zone at Top)
- **สิ่งที่ต้องทำ:** ในแต่ละหน้าไฟล์ PHP ให้แบ่งโครงสร้างโค้ดออกเป็น 2 โซนอย่างชัดเจน:
  1. **โซนบน (Logic / Data Preparation):** ทำหน้าที่เปิด Session, เชื่อมต่อ `config.php`, รับค่า Request, ตรวจสอบ Validation และคิวรีข้อมูล
  2. **โซนล่าง (Presentation / HTML):** ทำหน้าที่วนลูปแสดงผล และเรียกใช้ `navbar.php` หรือคอมโพเนนต์ต่างๆ
- **เหตุผล:** ทำให้โค้ดเป็นระเบียบ อ่านเข้าใจง่าย และง่ายต่อการ Debug ข้อผิดพลาด

---

## 7. การจัดการข้อผิดพลาดอย่างปลอดภัย (Friendly Error Handling)
- **สิ่งที่ต้องทำ:** หลีกเลี่ยงการใช้คำสั่ง `die(mysqli_error($conn))` หรือแสดงข้อความ Error ของฐานข้อมูลให้ผู้ใช้งานทั่วไปเห็นบนหน้าเว็บ ให้ใช้การแจ้งเตือนที่เข้าใจง่ายผ่าน JavaScript Alert หรือ Bootstrap Alert แทน
- **เหตุผล:** การเปิดเผยรายละเอียด Error ของ SQL ออกสู่สาธารณะจะช่วยให้ผู้ไม่หวังดีทราบโครงสร้างตารางและชื่อคอลัมน์ในระบบได้