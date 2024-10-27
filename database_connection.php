
<?php
$servername = "localhost";  // เปลี่ยนให้เป็นชื่อเซิร์ฟเวอร์จริง
$username = "root";         // เปลี่ยนให้เป็นชื่อผู้ใช้จริง
$password = "12345678";             // รหัสผ่านจริง
$dbname = "mydb";  // ชื่อฐานข้อมูลจริง

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
