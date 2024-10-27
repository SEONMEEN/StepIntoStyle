<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$prodID = $data['prodID'];
$price = $data['price'];
$size = $data['size'];
$quantity = 1;

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "mydb";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// ดึง userID จาก session
$userSql = "SELECT userID FROM users WHERE username = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("s", $_SESSION['username']);
$userStmt->execute();
$userResult = $userStmt->get_result();
if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $userID = $userRow['userID'];
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล userID']);
    exit();
}

// ดึง sizeID และ stock จากตาราง sizes
$sizeSql = "SELECT sizeID, stock FROM sizes WHERE prodID = ? AND size = ?";
$sizeStmt = $conn->prepare($sizeSql);
$sizeStmt->bind_param("is", $prodID, $size);
$sizeStmt->execute();
$sizeResult = $sizeStmt->get_result();
if ($sizeResult->num_rows > 0) {
    $sizeRow = $sizeResult->fetch_assoc();
    $sizeID = $sizeRow['sizeID'];
    $stock = $sizeRow['stock'];

    if ($stock >= $quantity) {
        $newStock = $stock - $quantity;
        $updateStockSql = "UPDATE sizes SET stock = ? WHERE sizeID = ?";
        $updateStockStmt = $conn->prepare($updateStockSql);
        $updateStockStmt->bind_param("ii", $newStock, $sizeID);
        if (!$updateStockStmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to update stock']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'สินค้าในสต็อกไม่เพียงพอ']);
        exit();
    }

    // ตรวจสอบรายการใน orderitems
    $checkOrderSql = "SELECT * FROM orderitems WHERE userID = ? AND sizeID = ?";
    $checkOrderStmt = $conn->prepare($checkOrderSql);
    $checkOrderStmt->bind_param("ii", $userID, $sizeID);
    $checkOrderStmt->execute();
    $checkOrderResult = $checkOrderStmt->get_result();
    if ($checkOrderResult->num_rows > 0) {
        $updateOrderItemsSql = "UPDATE orderitems SET quantity = quantity + ? WHERE userID = ? AND sizeID = ?";
        $updateOrderItemsStmt = $conn->prepare($updateOrderItemsSql);
        $updateOrderItemsStmt->bind_param("iii", $quantity, $userID, $sizeID);
        $updateOrderItemsStmt->execute();
       
    } else {
        $insertOrderItemsSql = "INSERT INTO orderitems (userID, quantity, sizeID) VALUES (?, ?, ?)";
        $insertOrderItemsStmt = $conn->prepare($insertOrderItemsSql);
        $insertOrderItemsStmt->bind_param("iii", $userID, $quantity, $sizeID);
        $insertOrderItemsStmt->execute();
        
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล sizeID หรือ stock']);
    exit();
}

$conn->close();
