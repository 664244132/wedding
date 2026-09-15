# 🌐 สรุปหลักการเขียนโค้ด HTML ที่ดีและแนวทางปฏิบัติ (Semantic HTML & Wedding Studio Standards)

เอกสารนี้รวบรวมหลักการและมาตรฐานการเขียนโค้ด **HTML5** ร่วมกับ **Bootstrap 5.3.0** สำหรับโปรเจกต์ **Eternal Love (สตูดิโอแต่งงานและพรีเวดดิ้ง)** เพื่อให้โครงสร้างหน้าเว็บมีความถูกต้องตามหลัก Semantic, มี Accessibility สูง (a11y) สวยงาม และเป็นมิตรต่อ SEO

---

## 1. การกำหนดข้อมูล Metadata และภาษาของเอกสาร
- **สิ่งที่ต้องทำ:**
  - ประกาศ `<!DOCTYPE html>` และ `<html lang="th">` เสมอ
  - กำหนด `charset="UTF-8"` และ `viewport` ที่ถูกต้อง เพื่อให้แสดงผลภาษาไทยและรองรับมือถือ
  - ตั้งชื่อแท็ก `<title>` ให้สื่อความหมายและสะท้อนแบรนด์สตูดิโอ เช่น `<title>ชื่อหน้า - Eternal Love</title>`

```html
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บริการถ่ายภาพพรีเวดดิ้ง - Eternal Love</title>
    
    <!-- ฟอนต์และสไตล์ชีตมาตรฐาน -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
```

---

## 2. โครงสร้างเชิงความหมาย (Semantic HTML Structure)
- **การจัดสัดส่วนหน้าเพจ:**
  - ใช้แท็ก Landmark ตามมาตรฐาน เช่น `<nav>`, `<section>`, `<header>`, `<footer>`
  - เรียกใช้ Navigation Bar ส่วนกลางผ่าน PHP: `<?php include('navbar.php'); ?>`
- **การจัดลำดับหัวข้อ (Headings Hierarchy):**
  - ใช้ `<h1>` เพียงหนึ่งจุดต่อหน้าสำหรับชื่อหรือหัวข้อหลักของหน้านั้นๆ
  - ใช้ `<h2>` ถึง `<h6>` ตามลำดับความสำคัญของเนื้อหา โดยไม่กระโดดข้ามขั้น

```html
<body>
    <!-- แถบเมนูนำทางหลัก -->
    <?php include('navbar.php'); ?>

    <!-- ส่วนฮีโร่ต้อนรับ -->
    <section class="hero">
        <div class="hero-content">
            <h1>บันทึกความทรงจำนิรันดร์</h1>
            <p>ถ่ายภาพแต่งงานสไตล์วิจิตรศิลป์ และบริการชุดบ่าวสาวระดับพรีเมียม</p>
        </div>
    </section>

    <!-- เนื้อหาหลัก -->
    <main class="container py-5">
        <!-- รายละเอียดเนื้อหา -->
    </main>
</body>
```

---

## 3. การเขียนแบบฟอร์มที่เข้าถึงได้และเป็นมิตรกับผู้ใช้ (Accessible Forms)
- **สิ่งที่ต้องทำ:**
  - ต้องผูก `<label for="inputId">` กับ `<input id="inputId">` เสมอ เพื่อให้คลิกที่ข้อความแล้ว Cursor วิ่งเข้าช่องกรอกได้ทันที
  - ระบุชนิดของ Input ให้เหมาะสม เช่น `type="email"`, `type="tel"`, `type="date"` เพื่อให้คีย์บอร์ดบนมือถือปรับตามประเภทข้อมูล
  - สำหรับฟอร์มที่มีการอัปโหลดไฟล์รูปภาพ (เช่น หน้าแอดมิน หรือหน้ารีวิว) ต้องมี `enctype="multipart/form-data"` เสมอ

```html
<!-- ตัวอย่างฟอร์มรีวิวความประทับใจ -->
<form action="reviews.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="client_name" class="form-label">ชื่อของคุณ</label>
        <input type="text" class="form-control" id="client_name" name="client_name" required>
    </div>
    
    <div class="mb-3">
        <label for="review_image" class="form-label">รูปภาพความประทับใจ (ถ้ามี)</label>
        <input type="file" class="form-control" id="review_image" name="review_image" accept="image/*">
    </div>

    <button type="submit" name="submit_review" class="btn btn-premium">ส่งรีวิว</button>
</form>
```

---

## 4. โครงสร้าง Modal และการเลือกสไตล์งานแต่ง (Modal Structure)
- **สิ่งที่ต้องทำ:** ในการเลือกชุดหรือสไตล์ภาพถ่ายในหน้าจองคิว (`booking.php`) ให้ใช้ Modal ตามมาตรฐานของ Bootstrap 5 เพื่อให้เปิด-ปิดได้ลื่นไหลบนทุกอุปกรณ์:

```html
<div class="modal fade" id="styleModal" tabindex="-1" aria-labelledby="styleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="styleModalLabel">เลือกสไตล์ที่คุณชื่นชอบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="itemGrid">
                    <!-- แสดงสไตล์การแต่งหน้า/ชุดบ่าวสาวผ่าน JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 5. การจัดการรูปภาพและการปรับขนาดแบบ Responsive (Images & Media)
- **สิ่งที่ต้องทำ:**
  - กำหนดคลาส `img-fluid` ของ Bootstrap ให้กับรูปภาพภาพถ่ายแต่งงาน เพื่อให้ย่อ-ขยายตามความกว้างของหน้าจอโดยอัตโนมัติ
  - ใส่ `alt` อธิบายรูปภาพเสมอ เช่น `alt="ชุดแต่งงานไทยประยุกต์"` เพื่อประโยชน์ด้าน SEO และผู้พิการทางสายตา
  - กำหนด `loading="lazy"` สำหรับรูปภาพในแกลเลอรีเพื่อเพิ่มความเร็วในการโหลดหน้าเว็บ