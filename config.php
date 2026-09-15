<?php
// config.php
$host = "localhost";
$user = "root";      // ปกติ XAMPP คือ root
$pass = "";          // ปกติ XAMPP รหัสผ่านว่างเปล่า
$db   = "wedding_db"; // ชื่อฐานข้อมูลที่คุณสร้างไว้

// สร้างการเชื่อมต่อและเก็บไว้ในตัวแปร $conn
$conn = mysqli_connect($host, $user, $pass, $db);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error());
}

// ตั้งค่าให้อ่านภาษาไทยได้
mysqli_set_charset($conn, "utf8mb4");
?>