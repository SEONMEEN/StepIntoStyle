<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "mydb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prodID = $_GET['prodID'];

$sql = "SELECT * FROM sizes WHERE prodID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prodID);
$stmt->execute();
$result = $stmt->get_result();

$sizes = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc() ) {
        if ($row['stock'] > 0) {
            $sizes .= "<option value='{$row['size']}'>{$row['size']}</option>";
        }
       
    }
} else {
    $sizes = "<option disabled>No sizes available</option>";
}

echo $sizes;

$stmt->close();
$conn->close();
?>
