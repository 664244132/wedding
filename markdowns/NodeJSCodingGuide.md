# 🟢 Node.js & Dev Tooling Guide

> 📌 **สถาปัตยกรรมหลักของโปรเจกต์ Eternal Love:**  
> เว็บไซต์ **Eternal Love (สตูดิโอแต่งงานและพรีเวดดิ้ง)** ขับเคลื่อนด้วยสถาปัตยกรรมหลักคือ **PHP (Procedural) และ MySQL / MariaDB (`wedding_db`)** บนสภาพแวดล้อมจำลอง **XAMPP (Apache)** ซึ่งสามารถทำงานได้ทันทีโดยไม่จำเป็นต้องใช้ Node.js ในฝั่ง Production Runtime  
>  
> เอกสารนี้จัดทำขึ้นเพื่อกำหนดแนวทางและมาตรฐานการใช้งาน **Node.js / npm** ในฐานะ **เครื่องมือสนับสนุนการพัฒนา (Developer Tooling & Build Automation)** หากทีมพัฒนามีการนำเครื่องมือเสริมเข้ามาช่วยในอนาคต

---

## ข้อกำหนดและแนวทางปฏิบัติในการใช้ Node.js / npm ในโปรเจกต์

### 1. บทบาทของ Node.js ในโปรเจกต์ (Dev Tooling Only)
- ใช้ Node.js เฉพาะเป็นเครื่องมือในระหว่างการพัฒนา (Development Tools) เช่น:
  - การบีบอัดขนาดไฟล์รูปภาพงานแต่งงานอัตโนมัติ (Image Optimization)
  - การจัดระเบียบและคอมไพล์สไตล์ชีต (Sass / SCSS หรือ PostCSS)
  - การย่อขนาดและตรวจสอบคุณภาพโค้ด (Minification & Linter)
- ห้ามใช้ Node.js รันเป็น Production Web Server ทับซ้อนกับ Apache/PHP ของระบบหลัก

### 2. การจัดการ Dependency และความปลอดภัย (Package Management)
- บันทึกเครื่องมือเสริมทั้งหมดไว้ใน `devDependencies` ในไฟล์ `package.json`
- ต้องมีไฟล์ `.gitignore` ระบุข้ามโฟลเดอร์ `node_modules/` เสมอ เพื่อป้องกันการอัปโหลดไฟล์แพ็กเกจขึ้นคลังเก็บโค้ด (Repository)
- ใช้คำสั่ง `npm audit` ตรวจสอบความปลอดภัยของแพ็กเกจที่ติดตั้งสม่ำเสมอ

### 3. การจัดการพาธให้รองรับ Cross-Platform (Path Safety)
- เนื่องจากสมาชิกในทีมอาจพัฒนาบนระบบปฏิบัติการที่ต่างกัน (Windows, macOS, Linux) หากเขียนสคริปต์ Node.js ให้ใช้โมดูล `path` (`path.join()` หรือ `path.resolve()`) ในการระบุตำแหน่งโฟลเดอร์เสมอ หลีกเลี่ยงการใช้เครื่องหมาย Slash (`/` หรือ `\`) แบบตายตัว

### 4. การจัดการคำสั่งอัตโนมัติ (NPM Scripts)
- กำหนดคำสั่งใน `package.json` ให้เข้าใจง่ายและสื่อความหมายชัดเจน เช่น:
  ```json
  {
    "scripts": {
      "build:css": "sass CSS/scss:CSS/ --style compressed",
      "watch": "sass --watch CSS/scss:CSS/"
    }
  }
  ```