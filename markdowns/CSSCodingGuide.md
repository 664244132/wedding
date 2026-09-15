# 🎨 สรุปหลักการเขียนโค้ด CSS ที่ดี (CSS Standards & Guidelines)

เอกสารนี้รวบรวมหลักการและมาตรฐานการเขียนสไตล์ชีต **Custom CSS** ร่วมกับ **Bootstrap 5.3.0** สำหรับโปรเจกต์ **Eternal Love (สตูดิโอแต่งงานและพรีเวดดิ้ง)** เพื่อให้สไตล์เว็บไซต์มีความหรูหรา สวยงาม ประณีต เป็นระเบียบ อ่านง่าย และรองรับ Responsive ในทุกขนาดหน้าจอ

---

## 1. โครงสร้างและการแยกไฟล์ Stylesheet (Modular CSS Organization)
- **แนวทางปฏิบัติ:** จัดเก็บไฟล์ CSS ทั้งหมดไว้ในโฟลเดอร์ `CSS/` โดยแยกไฟล์ตามหน้าที่การใช้งานเพื่อความเป็นระเบียบและให้เบราว์เซอร์ทำ Caching ได้อย่างมีประสิทธิภาพ:
  - `CSS/style.css`: สไตล์พื้นฐานส่วนกลาง, CSS Variables, Typography และการตั้งค่าธีมหลัก
  - `CSS/index.css`: เลย์เอาต์และเอฟเฟกต์เฉพาะของหน้าแรก (Hero Section, Service Highlights)
  - `CSS/booking.css`: สไตล์หน้าฟอร์มจองคิว, การ์ดเลือกสไตล์รูปภาพ, และคอนเทนเนอร์แผนที่ Leaflet
  - `CSS/packages.css`: การ์ดแสดงแพ็กเกจแต่งงานและตารางเปรียบเทียบราคา
  - `CSS/gallery.css`: เลย์เอาต์แกลเลอรีรูปภาพและการซูมแสดงผลงาน
  - `CSS/reviews.css`: การจัดแสดงรีวิว การให้คะแนนดาว และการ์ดความคิดเห็นลูกค้า
  - `CSS/login.css`: สไตล์แบบฟอร์มเข้าสู่ระบบและสมัครสมาชิก
  - `CSS/admin.css`: สไตล์แดชบอร์ดและการจัดการตารางในระบบหลังบ้าน

---

## 2. ชุดตัวแปรสีและโทนธีมงานแต่งงาน (Luxury Wedding Design Tokens)
- **แนวทางปฏิบัติ:** ใช้ตัวแปร CSS `:root` ในการกำหนดค่าสีหลัก เพื่อให้โทนสีของแบรนด์สม่ำเสมอตลอดทั้งเว็บไซต์:

```css
:root {
  /* โทนสีทองหรูหรา (Signature Gold) */
  --gold-primary: #c5a059;
  --gold-light: #e2c285;
  --gold-accent: #d4af37;

  /* โทนสีเข้มคลาสสิก (Deep Warm Brown & Charcoal) */
  --dark-wedding: #4a3b2b;
  --dark-text: #3a2e24;
  --dark-bg: #1a1a1a;

  /* โทนสีสว่างนุ่มนวล (Soft Cream & Warm Ivory) */
  --soft-cream: #fdfaf5;
  --cream-alt: #f7f1e6;
  --surface-white: #ffffff;

  /* เส้นขอบและเงา (Borders & Shadows) */
  --border-gold-subtle: rgba(197, 160, 89, 0.2);
  --shadow-gold: rgba(197, 160, 89, 0.25);
  --shadow-card: rgba(93, 74, 55, 0.12);
}
```

---

## 3. มาตรฐาน Typography และแบบอักษร (Luxury Font Hierarchy)
- **ฟอนต์หัวข้อ (Headings):** ใช้ `'Playfair Display', serif` เพื่อสื่อถึงความโรแมนติก คลาสสิก และวิจิตรศิลป์
- **ฟอนต์เนื้อหา (Body Text):** ใช้ `'Sarabun', sans-serif` หรือ `'Prompt', sans-serif` เพื่อให้อ่านง่าย ชัดเจน สบายตา
- **แนวทางปฏิบัติ:** หลีกเลี่ยงการระบุขนาดตัวอักษรตายตัวด้วย `px` ให้ใช้หน่วย `rem` หรือ `clamp()` เพื่อให้ปรับขนาดตามหน้าจอได้อย่างเหมาะสม

```css
/* หัวข้อใหญ่แสดงความรู้สึกโรแมนติก */
.section-title h1, .hero-content h1 {
  font-family: 'Playfair Display', serif;
  color: var(--dark-wedding);
  font-weight: 700;
  letter-spacing: 1px;
}

/* ข้อความเนื้อหาทั่วไป */
body, p, label, .lead {
  font-family: 'Sarabun', sans-serif;
  color: var(--dark-text);
  line-height: 1.7;
}
```

---

## 4. กฎการออกแบบปุ่มกด (Wedding Button Styling Rules)
- **สไตล์ปุ่มหลัก (Luxury / Premium Button):**
  - ทรงแคปซูลมนสวยงาม (`border-radius: 30px`) หรือขอบคมแบบโมเดิร์นคลาสสิก
  - มีเส้นขอบสีทองประกาย พร้อมเอฟเฟกต์ Gradient ขณะชี้เมาส์ (Hover)
  - เพิ่มมิติความลอยเล็กน้อย (`transform: translateY(-2px);`) เพื่อเพิ่มการตอบสนองที่หรูหรา

```css
.btn-premium, .nav-btn {
  border: 1.5px solid var(--gold-primary);
  padding: 8px 25px;
  border-radius: 30px;
  color: var(--gold-primary);
  background: transparent;
  font-weight: 500;
  transition: all 0.4s ease;
  text-decoration: none;
}

.btn-premium:hover, .nav-btn:hover {
  background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
  color: #ffffff;
  border-color: transparent;
  box-shadow: 0 5px 15px var(--shadow-gold);
  transform: translateY(-2px);
}
```

---

## 5. การออกแบบการ์ดบริการและแพ็กเกจ (Cards & Interactive Elements)
- **การ์ดบริการ (`.service-card`):**
  - ใช้พื้นหลังขาวมนขอบโค้งมน (`border-radius: 20px - 25px`)
  - เมื่อชี้เมาส์ (Hover) ให้ขยับลอยขึ้นเบาๆ (`translateY(-8px)`) พร้อมเงานุ่มนวล
- **การเลือกสไตล์รูปภาพในหน้าจองคิว (`.style-card`):**
  - การ์ดที่ถูกเลือกต้องมีไฮไลต์ขอบสีทองเด่นชัด เพื่อให้ลูกค้ารู้ว่าตนเองกำลังเลือกสไตล์ใดอยู่

```css
.service-card {
  border: none;
  border-radius: 25px;
  background: var(--surface-white);
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  overflow: hidden;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px var(--shadow-card);
}

.style-radio:checked + .style-card {
  border: 2px solid var(--gold-primary) !important;
  background: var(--soft-cream);
  box-shadow: 0 5px 15px var(--shadow-gold);
}
```

---

## 6. การจัดการโฟกัสและการรองรับ Accessibility (a11y)
- **แนวทางปฏิบัติ:** เมื่อผู้ใช้กดปุ่ม Tab บนคีย์บอร์ด ต้องมี Focus Ring สีทองแสดงชัดเจน ไม่ปิดกั้น `:focus-visible`
```css
a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible {
  outline: 2px solid var(--gold-primary);
  outline-offset: 2px;
}
```
