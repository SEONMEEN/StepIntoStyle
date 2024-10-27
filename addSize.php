<?php
// เปิดการแสดงผลข้อผิดพลาด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตั้งค่าการเชื่อมต่อกับฐานข้อมูล
$serverName = "localhost";
$userName = "root";
$userPassword = "12345678";
$dbName = "mydb";
$conn = mysqli_connect($serverName, $userName, $userPassword, $dbName);

// ตรวจสอบว่ามีการกดปุ่มเพิ่มสินค้าหรือไม่
$message = '';
if (isset($_POST['add_product'])) {

    // รับค่าจากฟอร์ม
    $product_id = mysqli_real_escape_string($conn, $_POST['productid']);
    $product_size = mysqli_real_escape_string($conn, $_POST['size']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['stock']);


    // ตรวจสอบว่าข้อมูลทุกช่องถูกกรอกครบหรือไม่
    if (empty($product_size) || empty($product_stock)) {
        $message = 'Please fill out all fields!';
    } else {
        // คำสั่ง SQL สำหรับเพิ่มสินค้า
        $insert = "INSERT INTO sizes(prodID, size, stock) 
                   VALUES('$product_id', '$product_size', '$product_stock')";

        $upload = mysqli_query($conn, $insert);

        // ตรวจสอบว่าการอัปโหลดสำเร็จหรือไม่
        if ($upload) {
            $message = 'Product added successfully!'; // แจ้งเพิ่มสินค้าสำเร็จ
        } else {
            $message = 'Could not add the product, please try again!'; // แจ้งไม่สามารถเพิ่มได้
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/headerstyle.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .lobster-regular {
            font-family: "Lobster", sans-serif;
            font-weight: 400;
            font-size: 50px;
            color: #fff;
        }


        .form-container {
            width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 500;
        }

        .form-container .form-control {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-container select.form-control {
            appearance: none;
            padding-right: 40px;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iOCIgdmlld0JveD0iMCAwIDEyIDgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbD0iIzAwMCI+PHBhdGggZD0iTTYgNy41TDExIDEgNy45MiAwIDYgMi4xMyA0LjA4IDAgMSAwIDExbDYgNy41eiIvPjwvc3ZnPg==') no-repeat right 10px center;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .alert {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(145deg, #FE74B4, #EB3C7C);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }



        button:hover {
            background: linear-gradient(145deg, #EB3C7C, #FE74B4);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        button:active {
            transform: translateY(1px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="lobster-regular">StepIntoStyle</div>
        <a href="admin_dashboard.php" class="home-button"><i class="bi bi-house-heart"></i></a>
    </div>
    <div class="form-container">
        <h2>Add Size</h2>

        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form id="add-product-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <?php
            // เชื่อมต่อฐานข้อมูล
            $conn = mysqli_connect($serverName, $userName, $userPassword, $dbName);

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // ดึงข้อมูลจากตาราง products
            $sql = "SELECT prodID, prodName FROM products";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                echo '<select name="productid" id="product" class="form-control">';
                // แสดงผลแต่ละรายการใน select
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['prodID'] . '">' . $row['prodName'] . '</option>';
                }
                echo '</select>';
            } else {
                echo "ไม่มีข้อมูลสินค้า";
            }

            // ปิดการเชื่อมต่อฐานข้อมูล
            mysqli_close($conn);
            ?>

            <input type="number" name="size" class="form-control" placeholder="Size" required>
            <input type="number" name="stock" class="form-control" placeholder="Stock" required>
            <button type="submit" name="add_product">Add Size</button>
        </form>
    </div>
</body>

</html>