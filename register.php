<?php
session_start();
include "includes/db.php";

$message = "";

if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // VALIDATION
    if (empty($username) || empty($password)) {
        $message = "All fields are required!";
    } elseif (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password) ||
        !preg_match("/[\W]/", $password)
    ) {
        $message = "Password must be 8+ chars, include uppercase, lowercase, number, and symbol.";
    } else {

        // CHECK DUPLICATE USER
        $check = $conn->prepare("SELECT user_id FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Username already exists!";
        } else {

            // HASH PASSWORD
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // INSERT INTO DATABASE (lost_found)
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);

            if ($stmt->execute()) {
                $message = "Registered successfully! You can now login.";
            } else {
                $message = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Lost & Found System</title>

    <style>
        body{
            margin:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#0a0f2c;
            font-family:Arial;
            color:white;
        }

        .box{
            width:350px;
            padding:30px;
            background:rgba(255,255,255,0.08);
            backdrop-filter:blur(12px);
            border-radius:15px;
            box-shadow:0 0 20px rgba(0,229,255,0.3);
            text-align:center;
        }

        h2{
            color:#00e5ff;
        }

        input{
            width:100%;
            padding:12px;
            margin:10px 0;
            border:none;
            border-radius:8px;
            background:rgba(255,255,255,0.1);
            color:white;
        }

        button{
            width:100%;
            padding:12px;
            border:none;
            border-radius:8px;
            background:#00e5ff;
            font-weight:bold;
            cursor:pointer;
        }

        .msg{
            margin:10px 0;
            padding:10px;
            border-radius:8px;
            background:rgba(255,255,255,0.1);
            color:#00e5ff;
        }

        a{
            color:#00e5ff;
            display:block;
            margin-top:10px;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>REGISTER</h2>

    <?php if (!empty($message)): ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="register">Register</button>
    </form>

    <a href="index.php">Back to Login</a>

</div>

</body>
</html>