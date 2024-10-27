<?php
ini_set('display_errors', 1);
error_reporting(~0);

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "mydb";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$updateSuccess = false;

// ตรวจสอบการรับค่า Product ID จาก URL
if (isset($_GET["ProbID"])) {
    $prodID = $_GET["ProbID"];
}

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $prodName = $_POST['txtProdName'];
    $price = $_POST['txtPrice'];
    $stockUpdates = $_POST['stock'];

    // อัปเดตข้อมูลสินค้าลงในตาราง products
    $sqlUpdateProduct = "UPDATE products SET prodName = ?, price = ? WHERE prodID = ?";
    $stmtUpdateProduct = $conn->prepare($sqlUpdateProduct);
    $stmtUpdateProduct->bind_param("sdi", $prodName, $price, $prodID);
    $stmtUpdateProduct->execute();

    // อัปเดตข้อมูลขนาดและสต็อกในตาราง sizes
    foreach ($stockUpdates as $sizeID => $stock) {
        $sqlUpdateStock = "UPDATE sizes SET stock = ? WHERE prodID = ? AND sizeID = ?";
        $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
        $stmtUpdateStock->bind_param("iii", $stock, $prodID, $sizeID);  // sizeID แทน size
        $stmtUpdateStock->execute();
    }

    $updateSuccess = true;
}

// Query ข้อมูลสินค้าจากตาราง products
$sql = "SELECT * FROM products WHERE prodID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prodID);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Query ขนาดและจำนวนสินค้าจากตาราง sizes
$sizeSql = "SELECT sizeID, size, stock FROM sizes WHERE prodID = ?";
$sizeStmt = $conn->prepare($sizeSql);
$sizeStmt->bind_param("i", $prodID);
$sizeStmt->execute();
$sizeQuery = $sizeStmt->get_result();
?>

<html>

<head>
    <title>Edit Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/headerstyle.css">
    <style>
        body {
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
        }

        .form-container {
            max-width: 550px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        .lobster-regular {
            font-family: "Lobster", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 50px;
            color: #fff;
        }

        h2 {
            text-align: center;
            color: #444;
            font-size: 24px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #FDDDE6;
            color: #333;
            border-radius: 8px;
        }

        td input[type="text"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .size-stock {
            margin-bottom: 15px;
        }

        .size-stock input[type="text"] {
            width: 60px;
            padding: 8px;
            margin-left: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            border: none;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .b1 {
            background-color: #FDDDE6;
            color: #333;
        }

        .b1:hover {
            background-color: #e3c2ce;
        }

        .b2 {
            background-color: #007bff;
            color: #fff;
        }

        .b2:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="lobster-regular">StepIntoStyle</div>
        <a href="admin_dashboard.php" class="home-button"><i class="bi bi-house-heart"></i></a>
    </div>

    <div class="form-container">
        <h2>Edit Product Form</h2>

        <!-- แสดงกล่องแจ้งเตือนเมื่อมีการอัปเดตข้อมูลสำเร็จ -->
        <?php if ($updateSuccess) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Product information updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form action="" method="post">
            <table class="table">
                <tr>
                    <th>Product ID</th>
                    <td>
                        <input type="hidden" name="txtProdID" value="<?php echo $product['prodID']; ?>">
                        <?php echo $prodID ?>
                    </td>
                </tr>
                <tr>
                    <th>Product Name</th>
                    <td><input type="text" name="txtProdName" value="<?php echo $product['prodName']; ?>"></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td><input type="text" name="txtPrice" value="<?php echo $product['price']; ?>"></td>
                </tr>
                <tr>
                    <th>Available Sizes & Stock</th>
                    <td>
                        <?php

                        if ($sizeQuery->num_rows > 0) {
                            while ($size = $sizeQuery->fetch_assoc()) {
                                echo "<div class='size-stock'>Size: " . $size['size'] . " - Stock: <input type='text' name='stock[" . $size['sizeID'] . "]' value='" . $size['stock'] . "'></div>";
                            }
                        } else {
                            echo "No sizes found for this product.";
                        }

                        ?>
                    </td>
                </tr>
            </table>

            <div class="form-actions">
                <input type="submit" class="btn b1" value="Update Product">
                <a href="editProduct.php" class="btn b2">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>
