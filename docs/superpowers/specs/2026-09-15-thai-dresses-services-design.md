# Design Document: ระบบแสดงชุดไทยและชุดไทยสากล (อย่างละ 5 ชุด) ใน services.php

**วันที่:** 2026-09-15  
**ผู้จัดทำ:** Senior Designer & Senior Developer  
**สถานะ:** รอการตรวจสอบและอนุมัติ (Pending User Review)  

---

## 1. บทนำและวัตถุประสงค์ (Overview & Objectives)
สร้างและเพิ่มรายการชุดแต่งงาน **ชุดไทยโบราณพระราชนิยม (5 ชุด)** และ **ชุดไทยสากลร่วมสมัย (5 ชุด)** รวม 10 ชุด ในระบบสตูดิโอแต่งงาน **Eternal Love** บนหน้า [services.php](file:///C:/xampp/htdocs/wedding/services.php) ให้ทำงานได้อย่างสมบูรณ์เมื่อลูกค้ากดเปิดการ์ดบริการ (Modal) พร้อมปฏิบัติตามมาตรฐานการเขียนโค้ดตามคู่มือ markdowns ทั้งหมด (`PHPCodingGuide.md`, `HTMLCodingGuide.md`, `CSSCodingGuide.md`, `JavascriptCodingGuide.md`, `SQLCodingGuide.md`)

---

## 2. โครงสร้างฐานข้อมูลและข้อมูลชุด (Database Architecture & Seed Data)

### 2.1 ตาราง `services` (บริการหลัก)
ตรวจสอบและอัปเดตข้อมูลบริการหลัก 2 รายการในตาราง `services`:
- `id = 24`: `title = 'ชุดไทย'`, `service_type = 'ชุดไทย'`, `description = 'สัมผัสความวิจิตรบรรจงแห่งชุดไทยพระราชนิยม ทรงคุณค่า สง่างามเหนือกาลเวลา'`
- `id = 25`: `title = 'ชุดไทยสากล'`, `service_type = 'ชุดไทยสากล'`, `description = 'ผสมผสานอัตลักษณ์ไทยเข้ากับความหรูหราสากล ดีไซน์ร่วมสมัยโดดเด่นไม่ซ้ำใคร'`

### 2.2 ตาราง `service_images` (รายการชุดย่อย 10 ชุด)
เพิ่มข้อมูลลงในตาราง `service_images` โดยแบ่งเป็น:

#### ก. ชุดไทยโบราณพระราชนิยม (`service_id = 24`)
1. **ชุดไทยศิวาลัย (Siwalai Royal Elegance)**
   - `image_path`: `gal_thai_siwalai.jpg`
   - `caption`: `ชุดไทยศิวาลัย - Siwalai Royal Gold`
   - `img_price`: `15900`
2. **ชุดไทยบรมพิมาน (Boromphiman Classic Gold)**
   - `image_path`: `gal_thai_boromphiman.jpg`
   - `caption`: `ชุดไทยบรมพิมาน - Classic Ivory Boromphiman`
   - `img_price`: `13900`
3. **ชุดไทยจักรพรรดิ (Chakkraphat Imperial Masterpiece)**
   - `image_path`: `gal_thai_chakkraphat.jpg`
   - `caption`: `ชุดไทยจักรพรรดิ - Imperial Masterpiece`
   - `img_price`: `18900`
4. **ชุดไทยจักรี (Chakkri Golden Blossom)**
   - `image_path`: `gal_thai_chakkri.jpg`
   - `caption`: `ชุดไทยจักรี - Golden Blossom Chakkri`
   - `img_price`: `12900`
5. **ชุดไทยดุสิต (Dusit Celestial Radiance)**
   - `image_path`: `gal_thai_dusit.jpg`
   - `caption`: `ชุดไทยดุสิต - Celestial Pearl Dusit`
   - `img_price`: `14500`

#### ข. ชุดไทยสากลร่วมสมัย (`service_id = 25`)
1. **ชุดไทยประยุกต์ศิวาลัยร่วมสมัย (Modern Couture Siwalai)**
   - `image_path`: `gal_modern_siwalai.jpg`
   - `caption`: `ชุดไทยประยุกต์ศิวาลัย - Modern Couture Siwalai`
   - `img_price`: `16900`
2. **ชุดไทยโมเดิร์นเมอร์เมดสไบแก้ว (Mermaid Contemporary Thai)**
   - `image_path`: `gal_modern_mermaid.jpg`
   - `caption`: `ชุดไทยเมอร์เมดร่วมสมัย - Mermaid Contemporary Thai`
   - `img_price`: `15500`
3. **ชุดไทยประยุกต์มินิมอลไอวอรี่ (Minimalist Pure Ivory Gown)**
   - `image_path`: `gal_modern_minimal.jpg`
   - `caption`: `ชุดไทยประยุกต์มินิมอล - Minimalist Pure Ivory`
   - `img_price`: `12900`
4. **ชุดไทยโมเดิร์นเจ้าหญิงล้านนา (Modern Lanna Royal Fusion)**
   - `image_path`: `gal_modern_lanna.jpg`
   - `caption`: `ชุดไทยโมเดิร์นล้านนา - Modern Lanna Royal Fusion`
   - `img_price`: `14900`
5. **ชุดไทยประยุกต์โรสโกลด์เอ็มไพร์ (Rose Gold Contemporary Thai)**
   - `image_path`: `gal_modern_rosegold.jpg`
   - `caption`: `ชุดไทยร่วมสมัยโรสโกลด์ - Rose Gold Contemporary Thai`
   - `img_price`: `17500`

### 2.3 การจัดการไฟล์รูปภาพ (Media Handling)
- นำรูปภาพความละเอียดสูงบันทึกไว้ที่โฟลเดอร์ `img/gallery/`
- จัดเตรียมรูปภาพไอคอนตัวแทนของบริการใน `img/icons/` เพื่อป้องกันการโหลดภาพไม่ติด
- มีการกำหนด fallback รูปภาพด้วย `onerror` เพื่อความต่อเนื่องในการแสดงผล

---

## 3. สถาปัตยกรรมและการปรับปรุงโค้ด [services.php](file:///C:/xampp/htdocs/wedding/services.php)

### 3.1 การแบ่งโซนโค้ด (Two-Zone Architecture ตาม PHPCodingGuide.md)
1. **โซนบน (Logic & Data Preparation Zone):**
   - ตรวจสอบ Session ด้วย `session_status() === PHP_SESSION_NONE`
   - ดึงข้อมูลบริการจากตาราง `services` โดยระบุชื่อคอลัมน์ชัดเจน (`SELECT id, title, icon, service_type, price, description FROM services ORDER BY id ASC`)
   - สำหรับแต่ละบริการ ดึงข้อมูลรูปภาพชุดจาก `service_images` โดยใช้ **MySQLi Prepared Statements**:
     ```php
     $sql_img = "SELECT id, service_id, image_path, caption, img_price FROM service_images WHERE service_id = ? ORDER BY id ASC";
     $stmt = mysqli_prepare($conn, $sql_img);
     mysqli_stmt_bind_param($stmt, "i", $service_id);
     mysqli_stmt_execute($stmt);
     $result_img = mysqli_stmt_get_result($stmt);
     ```
   - เตรียมโครงสร้าง Array ข้อมูลชุดพร้อม Mock / Fallback ในกรณีที่ฐานข้อมูลไม่ได้เชื่อมต่อหรือยังไม่มีรายการ
2. **โซนล่าง (Presentation Zone / HTML5):**
   - โครงสร้าง Semantic HTML (`<main>`, `<section>`, `<header>`, `lang="th"`)
   - ทำการ Escape ทุกข้อมูลที่นำมาแสดงผลด้วย `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` ป้องกันช่องโหว่ XSS 100%
   - จัดรูปแบบตัวเลขราคาด้วย `number_format($price, 0)`

### 3.2 การออกแบบ UI/UX สไตล์ Luxury Wedding (ตาม CSSCodingGuide.md)
- **สไตล์สีและธีม:**
  - สีทองหลัก: `--gold-primary: #c5a059;`
  - สีพื้นหลังอ่อน: `--soft-cream: #fdfaf5;`
  - สีน้ำตาลเข้มหรู: `--dark-wedding: #4a3b2b;`
  - ขอบและเงา: `--border-gold-subtle: rgba(197, 160, 89, 0.2);`, `--shadow-card: rgba(93, 74, 55, 0.12);`
- **การ์ดบริการ (`.service-card`):** ขอบมน 20px, เพิ่มเอฟเฟกต์ยกตัว `transform: translateY(-8px)` เมื่อ Hover
- **การ์ดชุดแต่งงานใน Modal (`.item-card`):**
  - แสดงภาพชุดสัดส่วน `aspect-ratio: 3/4` คมชัด พร้อมเอฟเฟกต์ซูมเบาๆ เมื่อ Hover
  - เมื่อคลิกเลือก (Radio checked) จะมีขอบสีทองประกาย พร้อมเครื่องหมายติ๊กถูกชัดเจน
  - ป้ายราคา `badge-price` สีทองหรูหรา สะดุดตา
- **ปุ่มยืนยันและจองบริการ (`.btn-luxury`):** สไตล์ Gradient ทอง-น้ำตาล นุ่มนวล โค้งมนสวยงาม

### 3.3 การทำงานฝั่ง JavaScript (ตาม JavascriptCodingGuide.md)
- ใช้ **Vanilla JavaScript (ES6+)** ไร้ Dependencies เสริม
- ใช้ `const` และ `let`, การเปรียบเทียบแบบเคร่งครัด `===`
- จัดรูปแบบราคาแสดงผลด้วย `Intl.NumberFormat('th-TH')`
- เมื่อผู้ใช้เลือกชุดและกดปุ่ม "ยืนยันการเลือกและจองบริการ":
  - ระบบจะส่งค่า `selected_item`, `service_type`, และ `price` ไปยังหน้า [booking.php](file:///C:/xampp/htdocs/wedding/booking.php) ผ่านฟอร์ม Redirect เพื่อให้ลูกค้าจองคิวต่อได้ทันทีอย่างไร้รอยต่อ

---

## 4. แผนการทดสอบและความถูกต้อง (Verification & Testing Plan)
1. **ทดสอบการเชื่อมโยงฐานข้อมูล (Database Integrity):**
   - ตรวจสอบว่าตาราง `service_images` มีข้อมูล 10 รายการครบถ้วน (ชุดไทย 5 รายการ, ชุดไทยสากล 5 รายการ)
   - ตรวจสอบ Foreign Key `service_id` เชื่อมโยงถูกต้อง (24 และ 25)
2. **ทดสอบความปลอดภัย (Security Audit):**
   - ตรวจสอบว่าไม่มี SQL Injection โดยการใช้ Prepared Statements
   - ตรวจสอบว่ามีการ Escape ข้อความทุกจุดด้วย `htmlspecialchars()` ป้องกัน XSS
3. **ทดสอบ UI/UX และ Responsive (Interface Testing):**
   - ตรวจสอบการแสดงผลบน Desktop, Tablet และ Mobile
   - ตรวจสอบว่า Modal เปิด-ปิดได้ราบรื่น ไม่มีปัญหากระตุก
   - ตรวจสอบการเลือกชุดและส่งข้อมูลต่อไปยัง [booking.php](file:///C:/xampp/htdocs/wedding/booking.php)
