<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

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
$conn = new mysqli('localhost', 'root', '12345678', 'mydb');
$sql = "SELECT orderitems.orderItemsID, products.prodName, sizes.size, products.price, orderitems.quantity
        FROM orderitems
        JOIN sizes ON orderitems.sizeID = sizes.sizeID
        JOIN products ON sizes.prodID = products.prodID
        WHERE orderitems.userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

echo json_encode($cartItems);  // ส่งข้อมูลสินค้าในตะกร้าเป็น JSON
?>
