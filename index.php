<?php
session_start();
include "includes/db.php";


if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found!";
    }
}

/* modal */
if (isset($_POST['register'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {

        $register_error = "All fields are required!";

    } else {

        // check duplicate user
        $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $register_error = "Username already exists!";

        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);

            if ($stmt->execute()) {

                $register_success = "Account registered successfully! You can now login.";

            } else {
                $register_error = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ICAS Lost & Found System</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Inter:wght@300;400;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Inter;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#0a0f2c;
    overflow:hidden;
    color:white;
}

/* GRID BACKGROUND */
body::before{
    content:"";
    position:absolute;
    width:200%;
    height:200%;
    background:
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size:50px 50px;
    animation: move 20s linear infinite;
    z-index:-1;
}

@keyframes move{
    0%{transform:translate(0,0);}
    100%{transform:translate(-50px,-50px);}
}

/* CONTAINER */
.container{
    width:90%;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* LEFT SIDE */
.left{
    width:50%;
}

.left h1{
    font-family:Orbitron;
    color:#00e5ff;
    font-size:38px;
}

.left p{
    color:#aaa;
    margin-top:10px;
}

/* LOGIN BOX */
.box{
    width:400px;
    background:rgba(255,255,255,0.08);
    padding:30px;
    border-radius:15px;
    backdrop-filter:blur(12px);
    box-shadow:0 0 20px rgba(0,229,255,0.3);
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
    background:#00e5ff;
    border:none;
    border-radius:8px;
    font-weight:bold;
    cursor:pointer;
}

.error{
    background:red;
    padding:8px;
    margin:10px 0;
    border-radius:6px;
    text-align:center;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.8);
}

.modal-content{
    width:350px;
    margin:10% auto;
    background:rgba(255,255,255,0.1);
    padding:20px;
    border-radius:12px;
    backdrop-filter:blur(12px);
}

.close{
    float:right;
    cursor:pointer;
    color:#00e5ff;
}

/* SUCCESS MESSAGE */
.success{
    color:#00e5ff;
    text-align:center;
    margin:10px 0;
}
</style>

</head>

<body>
<div style="position:absolute; top:20px; width:100%; text-align:center;">
<h1 style="font-family:Orbitron;color:#00e5ff;">
INABANGA COLLEGE OF ARTS AND SCIENCES (ICAS)
</h1>
</div>
<div class="container">
        
    <!-- LEFT -->
    <div class="left">
        <h1>LOST AND FOUND INVENTORY SYSTEM</h1>
        <p>ICAS - Lost & Found Tracking System</p>
    </div>

    <!-- LOGIN BOX -->
    <div class="box">

        <h2>LOGIN</h2>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>

        
       
    </div>

</div>

