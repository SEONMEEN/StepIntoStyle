<?php
session_start();
?>


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

// ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username1']);
    $email = mysqli_real_escape_string($conn, $_POST['email1']);
    $password = mysqli_real_escape_string($conn, $_POST['password1']);
    $role = "user";

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
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url(backgroud/bg1.jpg);
            background-size: cover;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .lobster-regular {
            font-family: "Lobster", sans-serif;
            font-size: 50px;
            margin-bottom: 20px;
            color: #fff;
        }

        .toggle-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            border-radius: 30px;
            width: 160px;
            padding: 5px;
            margin: 20px auto;
            cursor: pointer;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .toggle-circle {
            background-color: #FDDDE6;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            transition: left 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .toggle-container.active .toggle-circle {
            left: calc(100% - 75px);
            /* Move circle to the right */
        }

        .toggle-option {
            font-weight: 600;
            font-size: 16px;
            color: #333;
            flex: 1;
            text-align: center;
            z-index: 1;
            transition: color 0.3s ease;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .login-container input {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background-color: white;
            color: black;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .login-container input:focus {
            background-color: #f0f0f0;
            border-color: #A7C7E7;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            outline: none;
        }

        .login-container input::placeholder {
            color: #999;
        }

        .login-container button {
            background: linear-gradient(135deg, #A7C7E7, #FDDDE6);
            color: white;
            padding: 14px 20px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2), 0 2px 4px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .login-container button:hover {
            background: linear-gradient(135deg, #FDDDE6, #A7C7E7);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3), 0 4px 6px rgba(0, 0, 0, 0.25);
            transform: translateY(-2px);
        }

        .register-btn {
            background-color: #ccc;
            color: black;
            padding: 14px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            background-color: #bbb;
            transform: translateY(-2px);
        }

        .error-message {
            color: #ff4c4c;
            font-size: 14px;
            margin-bottom: 10px;
        }
        /* สไตล์กล่องแจ้งเตือน */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

/* สไตล์ข้อความในกล่องแจ้งเตือน */
.alert-dismissible {
    padding-right: 40px;
}

/* ปุ่มปิดในกล่องแจ้งเตือน */
.alert .btn-close {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    background-color: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #333;
}

/* สไตล์เฉพาะประเภทของการแจ้งเตือน */
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

/* ปุ่มปิดแจ้งเตือนเมื่อ hover */
.alert .btn-close:hover {
    color: #000;
    transform: translateY(-50%) scale(1.1);
}

    </style>
</head>

<body>
   <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']['text']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

    <header class="lobster-regular">StepIntoStyle</header>
    <div class="login-container">
        <!-- Toggle Switch ระหว่าง Login และ Register -->
        <div class="toggle-container" id="toggleSwitch" onclick="toggleForm()">
            <div class="toggle-option">Login</div>
            <div class="toggle-option">Register</div>
            <div class="toggle-circle"></div>
        </div>

        <!-- แสดงข้อความ Invalid username or password -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error-message"><?php echo $_SESSION['login_error']; ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <!-- ฟอร์ม Login -->
        <div id="loginForm" class="form-section active">
            <h1 class="poppins-extrabold">Login</h1>
            <form action="authenticate.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>

        <!-- ฟอร์ม Register -->
        <div id="registerForm" class="form-section">
            <h1 class="poppins-extrabold">Register</h1>
            <form id="registrationForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

                <input type="email" name="email1" id="email" placeholder="Email" required>


                <input type="text" name="username1" id="username" placeholder="Username" required>


                <input type="password" name="password1" id="password" placeholder="Password" required>



                <button type="submit" class="btn" name="submit">Sign Up</button>
            </form>
            </form>
        </div>
    </div>

    <script>
        function toggleForm() {
            const toggleSwitch = document.getElementById('toggleSwitch');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            toggleSwitch.classList.toggle('active');

            if (toggleSwitch.classList.contains('active')) {
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
            } else {
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
            }
        }
    </script>
</body>

</html>