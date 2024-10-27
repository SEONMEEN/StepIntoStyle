<?php
session_start();
require_once 'database_connection.php'; // ใส่การเชื่อมต่อฐานข้อมูลที่ใช้งาน

$data = json_decode(file_get_contents("php://input"), true);
$orderItemsID = $data['orderItemsID'];
$change = $data['change'];

// ดึงข้อมูล `sizeID` และจำนวนจากตาราง `orderitems`
$query = "SELECT sizeID, quantity FROM orderitems WHERE orderItemsID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $orderItemsID);
$stmt->execute();
$stmt->bind_result($sizeID, $currentQuantity);
$stmt->fetch();
$stmt->close();

// คำนวณจำนวนสินค้าใหม่
$newQuantity = $currentQuantity + $change;

// ตรวจสอบว่าจำนวนสินค้าที่ปรับใหม่ถูกต้อง (ไม่เป็นค่าติดลบและอยู่ในจำนวนสต็อกที่เหลือ)
if ($newQuantity > 0) {
    // ตรวจสอบจำนวนสต็อกที่มีอยู่ในตาราง `sizes`
    $stockQuery = "SELECT stock FROM sizes WHERE sizeID = ?";
    $stmt = $conn->prepare($stockQuery);
    $stmt->bind_param("i", $sizeID);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    if ($newQuantity <= $stock + $currentQuantity) { // ตรวจสอบสต็อกให้เพียงพอ
        // อัปเดตจำนวนสินค้าในตะกร้า
        $updateCartQuery = "UPDATE orderitems SET quantity = ? WHERE orderItemsID = ?";
        $stmt = $conn->prepare($updateCartQuery);
        $stmt->bind_param("ii", $newQuantity, $orderItemsID);
        $stmt->execute();
        $stmt->close();

        // ปรับสต็อกตามการเปลี่ยนแปลงของจำนวนสินค้า
        $stockAdjustment = $currentQuantity - $newQuantity;
        $updateStockQuery = "UPDATE sizes SET stock = stock + ? WHERE sizeID = ?";
        $stmt = $conn->prepare($updateStockQuery);
        $stmt->bind_param("ii", $stockAdjustment, $sizeID);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["success" => true, "message" => "อัปเดตตะกร้าเรียบร้อยแล้ว"]);
    } else {
        echo json_encode(["success" => false, "message" => "สินค้าคงคลังไม่เพียงพอ"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "จำนวนสินค้าไม่ถูกต้อง"]);
}

$conn->close();
?>
