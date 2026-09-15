<?php 
session_start();
include('config.php');

// --- 1. จัดการ AJAX API สำหรับดึงข้อมูลสไตล์ (แทนที่ไฟล์ get_styles.php) ---
if(isset($_GET['ajax_s_id'])) {
    header('Content-Type: application/json');
    $s_id = intval($_GET['ajax_s_id']);
    $result = mysqli_query($conn, "SELECT * FROM service_images WHERE service_id = $s_id");
    $items = [];
    while($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    echo json_encode($items);
    exit(); // หยุดการทำงานแค่นี้ ไม่ต้องโหลด HTML ด้านล่าง
}

// --- 2. ตรวจสอบสิทธิ์และรับข้อมูลการจอง ---
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_booking'])) {
    $username = $_SESSION['username'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $date = $_POST['booking_date'];
    $loc_type = $_POST['location_type'];
    $lat = !empty($_POST['latitude']) ? $_POST['latitude'] : "NULL";
    $lng = !empty($_POST['longitude']) ? $_POST['longitude'] : "NULL";
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $services = isset($_POST['services']) ? implode(', ', $_POST['services']) : '';

    $sql = "INSERT INTO bookings (username, phone, booking_date, services, note, location_type, latitude, longitude, status) 
            VALUES ('$username', '$phone', '$date', '$services', '$note', '$loc_type', $lat, $lng, 'pending')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('จองคิวสำเร็จ! ทางเราจะติดต่อกลับไปโดยเร็วที่สุด'); window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');</script>";
    }
}

// รับค่าเริ่มต้นจากหน้า Gallery
$pre_selected_item = isset($_GET['selected_item']) ? $_GET['selected_item'] : '';
$pre_price = isset($_GET['price']) ? $_GET['price'] : '0';
$pre_service_type = isset($_GET['service_type']) ? $_GET['service_type'] : '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Reservation - Eternal Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="CSS/booking.css?v=<?php echo time(); ?>">
    <style>
        .style-card { border: 1px solid #eee; transition: 0.3s; background: #fff; cursor: pointer; height: 100%; border-radius: 4px; }
        .style-card img { width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .style-radio:checked + .style-card { border: 2px solid var(--gold); background: #faf7f2; box-shadow: 0 5px 15px rgba(179,139,89,0.2); }
        .service-title-text { font-weight: 700; transition: 0.3s; color: var(--dark-text); }
        #map { display: none; }
        .price-display { color: var(--gold); font-weight: bold; font-size: 1.2rem; }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container my-5" style="max-width: 1000px;">
        <div class="reservation-header text-center mb-5">
            <h6 style="letter-spacing: 8px; color: var(--gold-light); text-transform: uppercase;">Eternal Love Studio</h6>
            <h1>RESERVATION</h1>
        </div>

        <div class="booking-card shadow-lg">
            <form action="" method="POST">
                <div class="mb-5">
                    <label class="form-label-custom text-center mb-4">1. Select Your Services</label>
                    <div class="row g-3 justify-content-center">
                        <?php 
                        $s_query = mysqli_query($conn, "SELECT * FROM services");
                        while($s_row = mysqli_fetch_assoc($s_query)):
                            $is_checked = ($s_row['service_type'] == $pre_service_type) ? 'checked' : '';
                        ?>
                        <div class="col-6 col-md-3">
                            <input type="checkbox" name="services[]" value="<?= $s_row['title'] ?>" id="ser_<?= $s_row['id'] ?>" class="service-check" <?= $is_checked ?> onclick="openModal(this, '<?= $s_row['service_type'] ?>', <?= $s_row['id'] ?>)">
                            <label for="ser_<?= $s_row['id'] ?>" class="service-btn">
                                <div class="service-icon-big">
                                    <img src="img/icons/<?= $s_row['icon'] ?>" width="45" onerror="this.src='img/icons/default.png'">
                                </div>
                                <div class="service-title-text mt-2"><?= $s_row['title'] ?></div>
                                <div id="val_ser_<?= $s_row['id'] ?>" class="small fw-bold mt-1" style="color: var(--gold);">
                                    <?= ($is_checked) ? '✓ SELECTED' : '' ?>
                                </div>
                            </label>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <hr class="my-5" style="opacity: 0.1;">

                <div class="row g-4">
                    <div class="col-12"><label class="form-label-custom">2. Contact Information</label></div>
                    <input type="hidden" name="note" id="final_note" value="<?= htmlspecialchars($pre_selected_item) ?>">
                    <input type="hidden" name="total_price" id="final_price" value="<?= $pre_price ?>">
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Customer Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $_SESSION['username'] ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="08x-xxxxxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Appointment Date</label>
                        <input type="date" name="booking_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Selected Package / Style</label>
                        <input type="text" id="display_note" class="form-control" readonly value="<?= htmlspecialchars($pre_selected_item) ?>" placeholder="Please select a service">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Estimate Price</label>
                        <div class="price-display">฿ <span id="display_price"><?= number_format($pre_price) ?></span></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Location</label>
                        <select name="location_type" class="form-select" onchange="toggleMap(this.value)">
                            <option value="สตูดิโอ Eternal Love">สตูดิโอ Eternal Love</option>
                            <option value="นอกสถานที่">นอกสถานที่ (โปรดระบุพิกัดในแผนที่)</option>
                        </select>
                        <div id="map" class="mt-3"></div>
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                    </div>
                </div>
                <button type="submit" name="submit_booking" class="btn-select-main">Confirm Reservation</button>
            </form>
        </div>
    </div>

    <div class="modal fade" id="itemModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 0px; border: 2px solid var(--gold);">
                <div class="modal-header border-0 p-4">
                    <h5 class="fw-bold mb-0" style="letter-spacing: 2px;">SELECT PREFERRED STYLE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cancelSelection()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3" id="itemGrid"></div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button class="btn btn-dark px-5 py-3 rounded-0" style="background: var(--dark-text); border: 1px solid var(--gold);" onclick="saveItem()">SAVE SELECTION</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('itemModal'));
        let currentId = '', selections = {}, prices = {}, map, marker;
        const initialVal = "<?= $pre_selected_item ?>";
        const initialPrice = "<?= $pre_price ?>";
        if(initialVal !== "") { selections['init'] = initialVal; prices['init'] = parseInt(initialPrice); }

        async function openModal(cb, type, s_id) {
            if(cb.checked) {
                currentId = cb.id;
                let grid = document.getElementById('itemGrid');
                grid.innerHTML = '<div class="col-12 text-center py-5">Loading...</div>';
                modal.show();
                try {
                    // เรียก API ที่อยู่ด้านบนของไฟล์นี้แหละครับ
                    const response = await fetch(`booking.php?ajax_s_id=${s_id}`);
                    const data = await response.json();
                    grid.innerHTML = "";
                    data.forEach(item => {
                        grid.innerHTML += `
                            <div class="col-6 col-md-3">
                                <label class="w-100 mb-0">
                                    <input type="radio" name="temp" value="${item.caption}" data-price="${item.img_price}" class="d-none style-radio">
                                    <div class="style-card p-2 text-center">
                                        <img src="img/gallery/${item.image_path}" class="img-fluid mb-2">
                                        <div class="small fw-bold text-uppercase">${item.caption}</div>
                                        <div style="color:var(--gold); font-size: 0.85rem;">฿${new Intl.NumberFormat().format(item.img_price)}</div>
                                    </div>
                                </label>
                            </div>`;
                    });
                } catch (e) { grid.innerHTML = 'Error loading.'; }
            } else {
                delete selections[cb.id]; delete prices[cb.id]; updateNote();
                document.getElementById('val_'+cb.id).innerText="";
            }
        }

        function saveItem() {
            let sel = document.querySelector('input[name="temp"]:checked');
            if(sel) {
                selections[currentId] = sel.value; prices[currentId] = parseInt(sel.getAttribute('data-price'));
                document.getElementById('val_'+currentId).innerText="✓ SELECTED"; updateNote(); modal.hide();
            } else { alert("Please select a style."); }
        }

        function cancelSelection() { if(!selections[currentId]) { document.getElementById(currentId).checked = false; } }

        function updateNote() {
            let val = Object.values(selections).join(', ');
            document.getElementById('final_note').value = val; document.getElementById('display_note').value = val;
            let total = Object.values(prices).reduce((a, b) => a + b, 0);
            document.getElementById('final_price').value = total; document.getElementById('display_price').innerText = new Intl.NumberFormat().format(total);
        }

        function toggleMap(v) {
            let m = document.getElementById('map');
            if(v === 'นอกสถานที่') {
                m.style.display = 'block';
                setTimeout(() => {
                    if(!map) {
                        map = L.map('map').setView([13.7563, 100.5018], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        marker = L.marker([13.7563, 100.5018], {draggable: true}).addTo(map);
                        const setC = (p) => { document.getElementById('lat').value = p.lat; document.getElementById('lng').value = p.lng; };
                        setC(marker.getLatLng());
                        map.on('click', e => { marker.setLatLng(e.latlng); setC(e.latlng); });
                        marker.on('dragend', e => setC(marker.getLatLng()));
                    }
                    map.invalidateSize();
                }, 200);
            } else { m.style.display = 'none'; }
        }
    </script>
</body>
</html>