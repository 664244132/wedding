# ⚡ สรุปแนวทางการเขียน JavaScript ที่ดี (Vanilla JS Code Standards)

เอกสารนี้รวบรวมหลักการและข้อกำหนดในการเขียนโค้ด **Vanilla JavaScript (ES6+)** สำหรับโปรเจกต์ **Eternal Love (สตูดิโอแต่งงานและพรีเวดดิ้ง)** เพื่อให้การทำงานฝั่งไคลเอนต์มีความรวดเร็ว ลื่นไหล ปลอดภัย และเข้าใจง่ายสำหรับผู้เริ่มต้น

---

## 1. การใช้ Vanilla JavaScript และสถาปัตยกรรมแบบ Native
- **สถาปัตยกรรม:** โปรเจกต์นี้ใช้ **Vanilla JavaScript (ES6+) แบบ Native** ร่วมกับไลบรารีเฉพาะทางเท่าที่จำเป็น (เช่น Leaflet.js สำหรับแผนที่ และ Bootstrap 5 Bundle JS) โดยไม่มีการพึ่งพา Framework ขนาดใหญ่ เพื่อให้หน้าเว็บโหลดได้รวดเร็วและบำรุงรักษาง่าย
- **กฎการเขียน:** หลีกเลี่ยงการเขียนโค้ดแบบเก่า (เช่น `var`) ให้ใช้ `const` สำหรับค่าคงที่ และ `let` สำหรับตัวแปรที่ต้องเปลี่ยนค่า

---

## 2. การดึงข้อมูลแบบ Asynchronous ด้วย Fetch API (AJAX Style Selection)
- **แนวทางปฏิบัติ:** ใช้ `fetch()` ร่วมกับ `async / await` ในการโหลดข้อมูลแบบเบื้องหลัง (AJAX) โดยไม่ต้องรีเฟรชหน้าเว็บ เช่น การโหลดสไตล์ชุดหรือภาพตัวอย่างในหน้าจองคิว (`booking.php`):

```javascript
// ✅ ตัวอย่างการโหลดสไตล์บริการด้วย Fetch API
async function loadStyles(serviceId) {
    const grid = document.getElementById('itemGrid');
    grid.innerHTML = '<div class="col-12 text-center py-4">กำลังโหลดข้อมูล...</div>';

    try {
        const response = await fetch(`booking.php?ajax_s_id=${serviceId}`);
        if (!response.ok) {
            throw new Error('เครือข่ายมีปัญหา');
        }
        const data = await response.json();
        
        grid.innerHTML = "";
        data.forEach(item => {
            grid.innerHTML += `
                <div class="col-6 col-md-3">
                    <label class="w-100 mb-0">
                        <input type="radio" name="selected_style" value="${item.caption}" data-price="${item.img_price}" class="d-none style-radio">
                        <div class="style-card p-2 text-center">
                            <img src="img/gallery/${item.image_path}" class="img-fluid mb-2 rounded" alt="${item.caption}">
                            <div class="small fw-bold">${item.caption}</div>
                            <div class="text-gold small">฿${new Intl.NumberFormat().format(item.img_price)}</div>
                        </div>
                    </label>
                </div>`;
        });
    } catch (error) {
        grid.innerHTML = '<div class="col-12 text-center text-danger">ไม่สามารถโหลดข้อมูลสไตล์ได้</div>';
    }
}
```

---

## 3. การควบคุมแผนที่และการปักหมุดพิกัด (Leaflet.js Integration)
- **แนวทางปฏิบัติ:** ในระบบการจองคิวเมื่อลูกค้าเลือก "นอกสถานที่" จะมีการเปิดแผนที่ Leaflet เพื่อให้ปักหมุดสถานที่จัดงาน:
  - กำหนดค่าเริ่มต้นพิกัด และสร้าง Draggable Marker
  - อัปเดตค่าพิกัดลงใน Hidden Input (`latitude`, `longitude`) เมื่อมีการลากหมุดหรือคลิกบนแผนที่
  - เรียกใช้ `map.invalidateSize()` เมื่อเปิดการแสดงผลแผนที่ เพื่อป้องกันปัญหาแผนที่แสดงผลไม่เต็มช่อง

```javascript
let map, marker;

function initWeddingMap(lat = 13.7563, lng = 100.5018) {
    const mapContainer = document.getElementById('map');
    mapContainer.style.display = 'block';

    setTimeout(() => {
        if (!map) {
            map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            const updateCoords = (coords) => {
                document.getElementById('lat').value = coords.lat.toFixed(8);
                document.getElementById('lng').value = coords.lng.toFixed(8);
            };

            // อัปเดตพิกัดเมื่อลากหมุดหรือคลิกแผนที่
            marker.on('dragend', (e) => updateCoords(e.target.getLatLng()));
            map.on('click', (e) => {
                marker.setLatLng(e.latlng);
                updateCoords(e.latlng);
            });
        }
        map.invalidateSize();
    }, 200);
}
```

---

## 4. การคำนวณราคาแบบ Real-time และการจัดรูปแบบตัวเลข (Intl.NumberFormat)
- **แนวทางปฏิบัติ:** เมื่อลูกค้าเลือกบริการหรือสไตล์เสริม ให้คำนวณราคารวมทันทีและแสดงผลด้วย `Intl.NumberFormat('th-TH')` เพื่อให้มีเครื่องหมายจุลภาคคั่นหลักพันที่ถูกต้องสวยงาม:

```javascript
function calculateTotalPrice(pricesObject) {
    // รวมยอดราคาจากรายการทั้งหมดที่เลือก
    const total = Object.values(pricesObject).reduce((sum, price) => sum + price, 0);
    
    // อัปเดตค่าลงใน Hidden Input สำหรับส่งเข้า Database
    document.getElementById('final_price').value = total;
    
    // แสดงผลบนหน้าจอพร้อมคอมม่า
    document.getElementById('display_price').innerText = new Intl.NumberFormat('th-TH').format(total);
}
```

---

## 5. การจัดการ Navigation Scroll Effect
- **แนวทางปฏิบัติ:** ในหน้าเว็บหลัก เมื่อเลื่อนหน้าจอลงมากกว่า 50px ให้เพิ่มคลาส `.scrolled` ที่แถบเนวิเกชัน เพื่อเปลี่ยนพื้นหลังเป็นสีทึบหรือเบลอ:

```javascript
window.addEventListener('scroll', () => {
    const nav = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});
```

---

## 6. การตรวจสอบความเท่ากันอย่างเข้มงวด (Strict Equality `===`)
- ใช้ `===` และ `!==` เสมอ เพื่อป้องกันข้อผิดพลาดจากการแปลงชนิดข้อมูลอัตโนมัติ (Type Coercion)
- ใช้ Nullish Coalescing (`??`) เมื่อต้องการกำหนดค่าเริ่มต้นในกรณีที่ตัวแปรมีค่าเป็น `null` หรือ `undefined`