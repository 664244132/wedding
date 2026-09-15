# Design Document: ระบบรูปภาพแพ็กเกจแต่งงาน (packages.php) และแกลเลอรีภาพคู่บ่าวสาวพรีเวดดิ้ง (gallery.php)

**วันที่:** 2026-09-16  
**ผู้จัดทำ:** Senior Designer & Senior Developer  
**สถานะ:** อนุมัติแล้ว (Approved)  

---

## 1. บทนำและวัตถุประสงค์ (Overview & Objectives)
แก้ไขและสร้างรูปภาพคุณภาพสูงที่เหมาะสมกับเนื้อหาของการ์ดทั้ง 3 แพ็กเกจในหน้า [packages.php](file:///C:/xampp/htdocs/wedding/packages.php) และสร้างรูปภาพคู่บ่าวสาวสวมใส่ชุดพรีเวดดิ้งหลากหลายสไตล์สำหรับทั้ง 11 การ์ดที่ภาพไม่โหลดในหน้า [gallery.php](file:///C:/xampp/htdocs/wedding/gallery.php) พร้อมปรับปรุงโค้ดให้สอดคล้องกับมาตรฐานความปลอดภัยและคู่มือ markdowns ทั้งหมด

---

## 2. ข้อมูลจำเพาะของรูปภาพแพ็กเกจ (packages.php)

จัดเตรียมและสร้างไฟล์ภาพความละเอียดสูงลงในโฟลเดอร์ `img/packages/`:

1. **แพ็คเกจราคาประหยัด (Economy Package - ฿11,000)**
   - **ไฟล์:** `pkg_1774564008_5.webp` (หรือ `pkg_economy.jpg`)
   - **แนวคิดภาพ:** ภาพถ่ายคู่บ่าวสาวพรีเวดดิ้งในสวนธรรมชาติสไตล์เอาท์ดอร์ (Natural Romantic Garden) สดใส เป็นธรรมชาติและเข้าถึงง่าย
   - **ความสอดคล้อง:** ตรงกับเนื้อหาแพ็กเกจ (ถ่ายภาพ 1 ชม. 200 รูป โลเคชัน 1-2 ที่ ปรับแสงพื้นฐาน)

2. **แพ็คเกจมาตรฐาน (Standard Package - ฿20,000 - Gold Featured)**
   - **ไฟล์:** `pkg_1774564080_9.jpg` (หรือ `pkg_standard.jpg`)
   - **แนวคิดภาพ:** ภาพคู่บ่าวสาวในชุดวิวาห์สีขาวคลาสสิกและสูททักซิโด้สุดหรู (Classic Studio & Outdoor Gown) สวยสง่า ภูมิฐาน
   - **ความสอดคล้อง:** ตรงกับเนื้อหาแพ็กเกจ (ถ่าย Indoor + Outdoor มีชุดบ่าวสาว แต่งหน้าทำผม มี MV)

3. **แพ็คเกจพรีเมียม (Luxury Premium Package - ฿30,000)**
   - **ไฟล์:** `pkg_1774564119_0.jpg` (หรือ `pkg_premium.jpg`)
   - **แนวคิดภาพ:** ภาพคู่บ่าวสาวในห้องสูทโรงแรมหรูระดับ 5 ดาว / แกรนด์บอลรูม (Grand Luxury 5-Star Suite & Ballroom) สวยงาม วิจิตรตระการตา
   - **ความสอดคล้อง:** ตรงกับเนื้อหาแพ็กเกจ (ถ่ายในโรงแรมหรู ห้อง suite แต่งตัว afternoon tea รวมที่พัก)

---

## 3. ข้อมูลจำเพาะของรูปภาพแกลเลอรีคู่บ่าวสาว (gallery.php)

จัดเตรียมและบันทึกไฟล์ภาพคู่บ่าวสาวพรีเวดดิ้งลงในโฟลเดอร์ `uploads/gallery/` สำหรับทั้ง 11 การ์ดที่ยังขาดหาย พร้อมอัปเดตคำบรรยาย (`caption`) ในฐานข้อมูล:

1. `1774565658_69c5b91a2c398.jpg` — **พรีเวดดิ้งชุดวิวาห์สากลคลาสสิก ดั่งเทพนิยาย** (Fairytale Royal Wedding)
2. `1774565628_69c5b8fc92dfd.jpg` — **พรีเวดดิ้งเอาท์ดอร์ริมหาดทรายยามพระอาทิตย์ตก** (Sunset Beach Romance)
3. `1774565613_69c5b8eda7a82.jpg` — **พรีเวดดิ้งสไตล์เกาหลีมินิมอล อบอุ่นละมุนใจ** (Minimalist Korean Studio)
4. `1774565607_69c5b8e725caf.jpg` — **ชุดไทยบรมพิมานและชุดไทยสากลคู่บ่าวสาว** (Royal Thai Elegance Couple)
5. `1774565598_69c5b8de9531b.jpg` — **พรีเวดดิ้งชุดไทยประยุกต์ร่วมสมัย หรูหราสง่างาม** (Contemporary Thai Fusion)
6. `1774565591_69c5b8d748abd.jpg` — **มนต์สะกดแห่งรักในสวนสไตล์ยุโรป** (European Enchanted Garden)
7. `1774565581_69c5b8cde8016.jpg` — **ชุดวิวาห์สไตล์วินเทจคลาสสิก บนบันไดวนสุดหรู** (Vintage Grand Staircase)
8. `1774565572_69c5b8c482692.jpeg` — **พรีเวดดิ้งราตรีใต้แสงดาวและโบเก้ระยิบระยับ** (Midnight Starlight Romance)
9. `1774565565_69c5b8bd59d2c.jpg` — **ธีมแชมเปญกลามัวร์ ชุดราตรีกลิตเตอร์วิบวับ** (Champagne Glamour Haute Couture)
10. `1774565545_69c5b8a977492.webp` — **พรีเวดดิ้งชุดเมอร์เมดเน้นสรีระคู่สูททักซิโด้โมเดิร์น** (Mermaid Silhouette & Tuxedo)
11. `1774565538_69c5b8a20514e.webp` — **ชุดไทยจักรีสีชมพูกลีบบัวคู่ชุดไทยชายประยุกต์** (Lotus Pink Royal Thai Blossom)

---

## 4. การปรับปรุงโค้ดตามมาตรฐาน Markdowns

### 4.1 [packages.php](file:///C:/xampp/htdocs/wedding/packages.php)
- ปรับเปลี่ยน Query: ใช้ Explicit SELECT columns ตาม [SQLCodingGuide.md](file:///C:/xampp/htdocs/wedding/markdowns/SQLCodingGuide.md):
  ```sql
  SELECT id, package_name, package_price, package_detail, package_image FROM packages ORDER BY package_price ASC
  ```
- Escape ข้อมูล: `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- ใส่แท็กรูปภาพพร้อม `onerror` fallback, `alt`, และ `loading="lazy"` ตาม [HTMLCodingGuide.md](file:///C:/xampp/htdocs/wedding/markdowns/HTMLCodingGuide.md)
- คุมโทน Luxury Wedding CSS: ขอบทอง ป้ายราคาชัดเจน

### 4.2 [gallery.php](file:///C:/xampp/htdocs/wedding/gallery.php)
- ปรับเปลี่ยน Query: ใช้ Explicit SELECT columns:
  ```sql
  SELECT id, image_path, caption FROM gallery ORDER BY id DESC
  ```
- Escape คำบรรยายภาพ: `htmlspecialchars($row['caption'] ?: 'Eternal Love Pre-Wedding', ENT_QUOTES, 'UTF-8')`
- ใส่แท็กรูปภาพพร้อม `onerror` fallback, `alt`, และ `loading="lazy"` เพื่อป้องกันปัญหาภาพไม่โหลด 100%

---

## 5. แผนการทดสอบ (Verification Plan)
1. ตรวจสอบว่าไฟล์ภาพแพ็กเกจทั้ง 3 ภาพมีอยู่จริงใน `img/packages/`
2. ตรวจสอบว่าไฟล์ภาพแกลเลอรีทั้ง 11 ภาพ (+ 1 ภาพเดิม) มีอยู่จริงใน `uploads/gallery/`
3. ตรวจสอบว่า `caption` ในตาราง `gallery` ได้รับการอัปเดตครบทุกรายการ
4. Automated Test Script ตรวจสอบการเรนเดอร์ของ `packages.php` และ `gallery.php` ว่าไม่มีรูปแตกและไม่มี SQL Errors
