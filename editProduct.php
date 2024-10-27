<?php 
ini_set('display_errors', 1);
error_reporting(~0);

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "mydb";
$conn = mysqli_connect($servername, $username, $password, $dbname);

$statusMessage = '';  // ตัวแปรสำหรับเก็บข้อความแจ้งเตือน

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['prodID'])) {  
    $id = intval($_GET['prodID']); // ใช้ intval() ป้องกัน SQL injection
    
    // ลบข้อมูลในตาราง sizes ก่อน
    $sqlDeleteSizes = "DELETE FROM sizes WHERE prodID = $id";
    $queryDeleteSizes = mysqli_query($conn, $sqlDeleteSizes);

    // ตรวจสอบการลบในตาราง sizes
    if (mysqli_affected_rows($conn) > 0 || mysqli_affected_rows($conn) == 0) {
        // ลบข้อมูลในตาราง products หลังจากลบในตาราง sizes สำเร็จ
        $sqlDeleteProduct = "DELETE FROM products WHERE prodID = $id";
        $queryDeleteProduct = mysqli_query($conn, $sqlDeleteProduct);

        if (mysqli_affected_rows($conn) > 0) {
            $statusMessage = "Successfully deleted product and related sizes.";  // ข้อความสถานะเมื่อสำเร็จ
        } else {
            $statusMessage = "Error: Could not delete product.";  // ข้อความสถานะเมื่อไม่สำเร็จ
        }
    } else {
        $statusMessage = "Error: Could not delete sizes.";  // ข้อความสถานะเมื่อการลบ sizes ไม่สำเร็จ
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepIntoStyle - Edit Product</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/headerstyle.css">
    <style>
        /* Basic styles */
        body {
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
        }

        .lobster-regular {
            font-family: "Lobster", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 50px;
            color: #fff;
        }

        

        .home-button {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
        }

        /* Form container */
        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
            margin-bottom: 50px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f7f7f7;
            color: #333;
            font-weight: 600;
        }

        td {
            background-color: #fff;
            transition: transform 0.3s ease;
        }

        tr:hover td {
            transform: scale(1.05);
        }

        /* Button styles */
        .delete-btn {
            display: inline-block;
            padding: 8px 16px;
            color: white;
            background-color: #e74c3c;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c0392b;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }

        a {
            display: inline-block;
            padding: 8px 16px;
            color: #333;
            background-color: #FDDDE6;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        a:hover {
            background-color: #fbc2cf;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        /* Message styles */
        .status-message {
            margin-bottom: 20px;
            font-size: 18px;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>

<body>
<div class="header">
        <div class="lobster-regular">StepIntoStyle</div>
        <a href="admin_dashboard.php" class="home-button"><i class="bi bi-house-heart"></i></a>
    </div>
    <div class="form-container">
        <h2>Edit Product</h2>

        <!-- Display status message -->
        <?php if ($statusMessage != ''): ?>
            <div class="status-message <?php echo (strpos($statusMessage, 'Successfully') !== false) ? 'success' : 'error'; ?>">
                <?php echo $statusMessage; ?>
            </div>
        <?php endif; ?>

        <?php
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        } else {
            $sql = "SELECT * FROM products";
            $result = mysqli_query($conn, $sql);

            echo "<table>";
            echo "<tr><th>No.</th>";
            echo "<th>Name</th>";
            echo "<th>Action</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            $i = 1;
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr><td>" . $i . "</td><td>" . $row["prodName"] . "</td>
                    <td><a href='ProductEditForm.php?ProbID=" . $row["prodID"] . "'>Edit</a> </td>
                    <td><a href='editProduct.php?prodID=" . $row["prodID"] . "' class='delete-btn'>Delete</a></td></tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='4'>No results</td></tr>";
            }
        }
        echo "</table>";
        mysqli_close($conn);
        ?>
    </div>
</body>

</html>
