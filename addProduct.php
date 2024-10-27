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
    $product_name = mysqli_real_escape_string($conn, $_POST['name']);
    $product_brand = mysqli_real_escape_string($conn, $_POST['brand_id']);
    $product_price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // จัดการรูปภาพ
    $product_image = $_FILES['image']['name'];
    $product_image_tmp_name = $_FILES['image']['tmp_name'];
    $product_image_folder = 'uploads/' . basename($product_image);

    // ตรวจสอบว่าข้อมูลทุกช่องถูกกรอกครบหรือไม่
    if (empty($product_name) || empty($product_brand) || empty($product_price) || empty($product_image)) {
        $message = 'Please fill out all fields!';
    } else {
        // คำสั่ง SQL สำหรับเพิ่มสินค้า
        
        $insert = "INSERT INTO products(prodName, brandID, price, prodImage) 
                   VALUES('$product_name', '$product_brand', '$product_price', '$product_image_folder')";

        $upload = mysqli_query($conn, $insert);

        // ตรวจสอบว่าการอัปโหลดสำเร็จหรือไม่
        if ($upload) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
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
    <title>StepIntoStyle - Add Product</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/headerstyle.css">
    <style>
        body {
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
            max-width: 500px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 15px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: inset 3px 3px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #FFBBDF;
            outline: none;
            box-shadow: 0 0 10px rgba(255, 187, 223, 0.5), inset 0 0 5px rgba(255, 187, 223, 0.3);
        }

        .form-container button {
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

        .form-container button:hover {
            background: linear-gradient(145deg, #EB3C7C, #FE74B4);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        .form-container button:active {
            transform: translateY(1px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
<div class="header">
        <div class="lobster-regular">StepIntoStyle</div>
        <a href="admin_dashboard.php" class="home-button"><i class="bi bi-house-heart"></i></a>
    </div>
    <div class="form-container">
        <h2>Add Product</h2>

        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form id="add-product-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" class="form-control" placeholder="Product Name" required>
            <select name="brand_id" class="form-control" required>
                <option value="" disabled selected>Select Brand</option>
                <option value="3">Adidas</option>
                <option value="1">Nike</option>
                <option value="2">Puma</option>
                <option value="4">Converse</option>
                <option value="5">Others</option>
            </select>
            <input type="number" name="price" class="form-control" placeholder="Price" required>
            <input type="file" name="image" class="form-control" accept="image/*" required>
            <button type="submit" name="add_product" class="btn">Add Product</button>
        </form>
    </div>
</body>

</html>
