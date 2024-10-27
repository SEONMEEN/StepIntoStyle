<!-- get_product.php -->
<?php
    $servername = "localhost";  
    $username = "root";         
    $password = "12345678";     
    $dbname = "mydb";           
    
    // สร้างการเชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $prodID = $_GET['prodID'];
    $query = $db->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $query->execute([$prodID]);

    $product = $query->fetch(PDO::FETCH_ASSOC);
    echo json_encode($product);
?>
