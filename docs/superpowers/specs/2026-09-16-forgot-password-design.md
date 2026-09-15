# Design Document: ระบบรีเซ็ตรหัสผ่าน (Forgot Password) สำหรับ Eternal Love Studio

**วันที่:** 2026-09-16  
**ผู้จัดทำ:** Senior Designer & Senior Developer  
**สถานะ:** อนุมัติแล้ว (Approved)  

---

## 1. บทนำและวัตถุประสงค์ (Overview & Objectives)
พัฒนาระบบรีเซ็ตรหัสผ่านผ่านไฟล์ [forgot_password.php](file:///C:/xampp/htdocs/wedding/forgot_password.php) ให้สามารถใช้งานได้จริง 100% เชื่อมโยงกับปุ่ม "ลืมรหัสผ่านใช่ไหม?" ในหน้า [login.php](file:///C:/xampp/htdocs/wedding/login.php) โดยปฏิบัติตามคู่มือมาตรฐานความปลอดภัยและการเขียนโค้ดทั้งหมดในโฟลเดอร์ `markdowns/`

---

## 2. ขั้นตอนการทำงานของระบบ (User Flow & Architecture)

### 2.1 ขั้นตอนที่ 1: ตรวจสอบบัญชีผู้ใช้ (Verify Account)
1. ผู้ใช้เข้าสู่หน้า [forgot_password.php](file:///C:/xampp/htdocs/wedding/forgot_password.php)
2. กรอกอีเมลที่ใช้ลงทะเบียน
3. ระบบใช้ **MySQLi Prepared Statement** ตรวจสอบว่ามีอีเมลในตาราง `users` หรือไม่:
   ```sql
   SELECT id, username, email FROM users WHERE email = ? LIMIT 1
   ```
4. หากไม่พบ: แสดงข้อความแจ้งเตือนข้อผิดพลาด "ไม่พบบัญชีผู้ใช้ที่ลงทะเบียนด้วยอีเมลนี้"
5. หากพบ: บันทึกข้อมูลชั่วคราวใน Session / State และสลับไปยังฟอร์มตั้งรหัสผ่านใหม่ (Step 2)

### 2.2 ขั้นตอนที่ 2: ตั้งรหัสผ่านใหม่ (Reset Password)
1. ระบบแสดงชื่อบัญชีผู้ใช้เพื่อยืนยันตัวตน (เช่น คุณ ธนรัฐ)
2. ผู้ใช้กรอก:
   - รหัสผ่านใหม่ (New Password) — อย่างน้อย 6 ตัวอักษร
   - ยืนยันรหัสผ่านใหม่ (Confirm New Password)
3. ระบบตรวจสอบความถูกต้องของรหัสผ่านทั้งสองช่องว่าตรงกันหรือไม่
4. อัปเดตรหัสผ่านใหม่ลงตาราง `users` ด้วย **MySQLi Prepared Statement**:
   ```sql
   UPDATE users SET password = ? WHERE id = ?
   ```
5. แสดงการแจ้งเตือนความสำเร็จ และส่งผู้ใช้กลับไปยังหน้า [login.php](file:///C:/xampp/htdocs/wedding/login.php)

---

## 3. มาตรฐานการปฏิบัติตามคู่มือ Markdowns (Coding Standards Compliance)

### 3.1 [PHPCodingGuide.md](file:///C:/xampp/htdocs/wedding/markdowns/PHPCodingGuide.md) & [SQLCodingGuide.md](file:///C:/xampp/htdocs/wedding/markdowns/SQLCodingGuide.md)
- **Prepared Statements 100%:** ทุกคำสั่ง SQL ใช้ `mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`
- **Output Escaping:** ใช้ `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` ทุกจุดที่นำข้อมูลจากตัวแปรไปแสดงผลบน HTML
- **Two-Zone Architecture:**
  - โซนบน: Logic & Data Preparation
  - โซนล่าง: Presentation (HTML5)
- **ปรับปรุง [login.php](file:///C:/xampp/htdocs/wedding/login.php):** รีแฟกเตอร์จากคำสั่ง SQL แบบต่อสตริงเดิม ให้เป็น Prepared Statement เพื่อปิดช่องโหว่ SQL Injection ให้เป็นไปตามคู่มือ

### 3.2 [HTMLCodingGuide.md](file:///C:/xampp/htdocs/wedding/markdowns/HTMLCodingGuide.md) & [CSSCodingGuide.md](file:///C:/xampp/htdocs/wedding/markdowns/CSSCodingGuide.md)
- โครงสร้าง Semantic HTML5 (`lang="th"`, UTF-8, `<nav>`, `<main>`, `<header>`)
- ฟอร์มเข้าถึงง่ายด้วยการผูก `<label for="...">` กับ `<input id="...">`
- ดีไซน์ Luxury Wedding เข้าชุดกับหน้า [login.php](file:///C:/xampp/htdocs/wedding/login.php) และ [register.php](file:///C:/xampp/htdocs/wedding/register.php) ผ่าน [CSS/login.css](file:///C:/xampp/htdocs/wedding/CSS/login.css)

---

## 4. แผนการทดสอบ (Testing & Verification Plan)
1. ทดสอบการค้นหาอีเมลที่ไม่มีอยู่ในระบบ -> ต้องแสดงข้อความแจ้งเตือนอย่างถูกต้อง ไม่พัง
2. ทดสอบการค้นหาอีเมลที่มีอยู่ในระบบจริง -> ต้องแสดงขั้นตอนที่ 2 และชื่อบัญชีถูกต้อง
3. ทดสอบรหัสผ่านไม่ตรงกัน / รหัสผ่านสั้นเกินไป -> ต้องแจ้งเตือน
4. ทดสอบการเปลี่ยนรหัสผ่านสำเร็จ -> ข้อมูลใน Database เปลี่ยนแปลงจริง และสามารถนำรหัสผ่านใหม่ไปเข้าสู่ระบบใน [login.php](file:///C:/xampp/htdocs/wedding/login.php) ได้ทันที
