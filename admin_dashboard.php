<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// เปิดการแสดงผลข้อผิดพลาด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตั้งค่าการเชื่อมต่อกับฐานข้อมูล
$serverName = "localhost";
$userName = "root";
$userPassword = "12345678";
$dbName = "mydb";
$conn = mysqli_connect($serverName, $userName, $userPassword, $dbName);

// ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Please fill out all fields!'];
    } else {
        $checkQuery = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Username or email already exists!'];
        } else {
            $insert = "INSERT INTO users(username, email, password, role) 
                       VALUES('$username', '$email', '$password', '$role')";

            if (mysqli_query($conn, $insert)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'User added successfully!'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Could not add the user, please try again!'];
            }
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepIntoStyleForAdmin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/headerstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            padding: 0;
            background-color: #f4f4f4;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .lobster-regular {
            font-family: "Lobster", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 50px;
            color: #fff;
        }

        /* Header styles */

        .pic {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 20px 0;
        }

        .icon {
            font-size: 180px;
            color: #FFD4E4;
            margin-bottom: 20px;
        }

        .butt {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 20px 0;
        }

        /* Button styles */
        .btn {
            margin: 10px;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 25px;
            text-transform: uppercase;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            z-index: 1;
        }

        .btn:before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            z-index: -1;
            filter: blur(10px);
            transform: scale(1.05);
        }

        .btn {
            margin: 10px;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 25px;
            text-transform: uppercase;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            /* Prevents the pseudo-element from overflowing */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Pseudo-element for the background */
        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.2);
            /* Light highlight */
            border-radius: 25px;
            transition: transform 0.3s ease;
            /* Smooth transition */
            z-index: 0;
            /* Behind the button text */
        }

        .button-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
            margin-bottom: 15px;
        }

        .button-card:hover {
            transform: translateY(-5px);
        }

        /* Shadow effect */
        .btn {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            /* Adding shadow */
        }

        .btn:hover {
            transform: translateY(-3px);
            /* Lift the button */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            /* Darker shadow on hover */
        }

        /* Specific button color styles */
        .b1 {
            background-color: #6c757d;
            color: #fff;
        }

        .b1:hover {
            background-color: #5a6268;
        }

        .b2 {
            background-color: #007bff;
            color: #fff;
        }

        .b2:hover {
            background-color: #0056b3;
        }

        .b3 {
            background-color: #B9103B;
            color: #fff;
        }

        .b3:hover {
            background-color: #c82333;
        }

        .b4 {
            background-color: #28a745;
        }

        .b4:hover {
            background-color: #218838;
        }

        /* Card styles for buttons */
        h4 {
            text-align: center;
            color: #2d3436;
            margin-bottom: 30px;
        }

        #registrationForm {
            display: none;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 16px;
        }

        input:focus,
        select:focus {
            border-color: #0984e3;
            box-shadow: 0 0 8px rgba(9, 132, 227, 0.5);
            outline: none;
        }

        button[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #6c5ce7;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #a29bfe;
        }

        button[type="submit"]:active {
            background-color: #5a3cba;
            transform: scale(0.98);
        }

        .btn-info {
            width: 100%;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="lobster-regular">StepIntoStyle</div>

    </div>
    <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']['text']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    <div class="pic">
        <i class="icon bi bi-person-badge-fill"></i>
        <h3>Welcome, Admin <?php echo $_SESSION['username']; ?></h3>
    </div>
    <div class="butt">
        <div class="button-card">
            <a href="addProduct.php" class="btn b1" style="color: white; text-decoration: none;">Add Product</a>
            <a href="addSize.php" class="btn b4" style="color: white; text-decoration: none;">Add Size</a>
            <a href="editProduct.php" class="btn b2" style="color: white; text-decoration: none;">Edit Product</a>
        </div>
        <div class="">
            <button class="btn btn-info" onclick="toggleForm()">Register New User</button>

            <form id="registrationForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" class="btn" name="submit">Sign Up</button>
            </form>
        </div>
        <a href="logout.php" class="btn b3 btn-outline-danger" style="color: white; text-decoration: none;">Logout</a>
    </div>
    <script>
        function toggleForm() {
            const form = document.getElementById('registrationForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>

</html>