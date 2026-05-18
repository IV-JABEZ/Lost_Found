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
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Segoe+UI:wght@300;400;600&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

/* BACKGROUND — same as dashboard */
body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #e6edf3;
    overflow: hidden;

    background:
        radial-gradient(circle at 20% 20%, rgba(0,255,255,0.08), transparent 40%),
        radial-gradient(circle at 80% 80%, rgba(0,150,255,0.08), transparent 40%),
        linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
        url('assets/img/ICAS.webp');

    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

/* SCHOOL NAME BANNER */
.school-banner {
    text-align: center;
    margin-bottom: 250px;
}

.school-banner h1 {
    font-family: Orbitron, sans-serif;
    color: #38bdf8;
    font-size: 1.3em;
    letter-spacing: 2px;
    text-shadow: 0 0 12px rgba(56,189,248,0.6);
}

/* LEFT + BOX WRAPPER */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 60px;
    width: 90%;
    max-width: 900px;
}

/* LEFT SIDE */
.left {
    flex: 5;
}

.left h1 {
    font-family: Orbitron, sans-serif;
    color: #38bdf8;
    font-size: 2em;
    letter-spacing: 1px;
    text-shadow: 0 0 10px rgba(56,189,248,0.6);
    line-height: 1.3;
}

.left p {
    color: #94a3b8;
    margin-top: 12px;
    font-size: 0.95em;
}

/* LOGIN BOX — glass card matching dashboard style */
.box {
    width: 380px;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(10px);
    padding: 35px 30px;
    border-radius: 20px;
    border: 1px solid rgba(56,189,248,0.2);
    box-shadow:
        0 0 25px rgba(0, 191, 255, 0.25),
        0 20px 50px rgba(0,0,0,0.5);
}

.box h2 {
    color: #38bdf8;
    font-family: Orbitron, sans-serif;
    font-size: 1.2em;
    letter-spacing: 2px;
    margin-bottom: 20px;
    text-shadow: 0 0 8px rgba(56,189,248,0.5);
}

input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid rgba(56,189,248,0.3);
    background: #020617;
    color: #e6edf3;
    font-size: 0.95em;
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 8px rgba(56,189,248,0.3);
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 12px;
    background: #0ea5e9;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1em;
    cursor: pointer;
    transition: 0.3s;
    letter-spacing: 1px;
}

button:hover {
    background: #38bdf8;
    box-shadow: 0 0 14px rgba(56,189,248,0.6);
}

/* ERROR / SUCCESS */
.error {
    background: rgba(239,68,68,0.2);
    border: 1px solid #ef4444;
    color: #fca5a5;
    padding: 8px 12px;
    margin: 10px 0;
    border-radius: 8px;
    text-align: center;
    font-size: 0.9em;
}

.success {
    background: rgba(56,189,248,0.1);
    border: 1px solid rgba(56,189,248,0.4);
    color: #38bdf8;
    padding: 8px 12px;
    margin: 10px 0;
    border-radius: 8px;
    text-align: center;
    font-size: 0.9em;
}

/* MODAL */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.75);
    z-index: 999;
}

.modal-content {
    background: #0f172a;
    color: #e6edf3;
    width: 380px;
    margin: 8% auto;
    padding: 30px;
    border-radius: 15px;
    border: 1px solid rgba(56,189,248,0.3);
    box-shadow: 0 0 20px rgba(56,189,248,0.2);
}

.modal-content h3 {
    color: #38bdf8;
    font-family: Orbitron, sans-serif;
    font-size: 1em;
    letter-spacing: 2px;
    margin-bottom: 10px;
}

.close {
    float: right;
    font-size: 22px;
    cursor: pointer;
    color: #38bdf8;
    line-height: 1;
}

.close:hover {
    color: #7dd3fc;
}

/* REGISTER LINK */
.register-link {
    text-align: center;
    margin-top: 16px;
    font-size: 0.88em;
    color: #94a3b8;
}

.register-link a {
    color: #38bdf8;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
}

.register-link a:hover {
    text-shadow: 0 0 8px rgba(56,189,248,0.5);
}
</style>

</head>

<body>

    <!-- SCHOOL BANNER -->
    <div class="school-banner">
        <h1>INABANGA COLLEGE OF ARTS AND SCIENCES (ICAS)</h1>
    </div>

    <div class="container">

        <!-- LEFT -->
        <div class="left">
            <h1>LOST AND FOUND INVENTORY SYSTEM</h1>
            <p>ICAS — Lost &amp; Found Tracking System</p>
        </div>

        <!-- LOGIN BOX -->
        <div class="box">

            <h2>LOGIN</h2>

            <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
            <?php if(isset($register_success)) echo "<div class='success'>$register_success</div>"; ?>

            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button name="login">Login</button>
            </form>

            <div class="register-link">
                Don't have an account? <a onclick="document.getElementById('registerModal').style.display='block'">Register</a>
            </div>

        </div>

    </div>

    <!-- REGISTER MODAL -->
    <div class="modal" id="registerModal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('registerModal').style.display='none'">&times;</span>
            <h3>REGISTER</h3>

            <?php if(isset($register_error)) echo "<div class='error'>$register_error</div>"; ?>

            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button name="register">Register</button>
            </form>
        </div>
    </div>

    <script>
        // Auto-open modal if there was a register error
        <?php if(isset($register_error)): ?>
        document.getElementById('registerModal').style.display = 'block';
        <?php endif; ?>
    </script>

</body>
</html>