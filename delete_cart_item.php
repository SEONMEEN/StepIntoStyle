<?php
session_start();
require_once 'database_connection.php'; // ใส่การเชื่อมต่อฐานข้อมูลที่ใช้งาน

$data = json_decode(file_get_contents("php://input"), true);
$orderItemsID = $data['orderItemsID'];

// ดึงข้อมูล `sizeID` และจำนวนสินค้าที่จะลบออกจากตะกร้า
$query = "SELECT sizeID, quantity FROM orderitems WHERE orderItemsID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $orderItemsID);
$stmt->execute();
$stmt->bind_result($sizeID, $quantity);
$stmt->fetch();
$stmt->close();

// อัปเดตสต็อกโดยเพิ่มจำนวนสินค้าที่ลบออกไปกลับเข้าสต็อกในตาราง `sizes`
$updateStockQuery = "UPDATE sizes SET stock = stock + ? WHERE sizeID = ?";
$stmt = $conn->prepare($updateStockQuery);
$stmt->bind_param("ii", $quantity, $sizeID);
$stmt->execute();
$stmt->close();

// ลบสินค้าออกจากตะกร้า
$deleteQuery = "DELETE FROM orderitems WHERE orderItemsID = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $orderItemsID);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "ลบสินค้าออกจากตะกร้าและอัปเดตสต็อกเรียบร้อย"]);
} else {
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการลบสินค้า"]);
}
$stmt->close();
$conn->close();
?>
