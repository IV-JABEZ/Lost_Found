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
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that username.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ICAS — Lost &amp; Found</title>
<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --bg:        #f5f5f3;
    --surface:   #ffffff;
    --border:    rgba(0,0,0,0.10);
    --text:      #1a1a1a;
    --muted:     #6b7280;
    --accent:    #1a1a1a;
    --radius:    10px;
    --danger-bg: #fef2f2;
    --danger:    #b91c1c;
    --ok-bg:     #f0fdf4;
    --ok:        #15803d;
}

body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background:
        linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
        url('assets/img/ICAS.webp') center/cover no-repeat fixed;
    color: var(--text);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-size: 15px;
    padding: 1.5rem;
}

.school {
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.7);
    margin-bottom: 2rem;
    text-align: center;
}

.card {
    background: rgba(10, 25, 60, 0.55);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: var(--radius);
    padding: 2rem;
    width: 100%;
    max-width: 360px;
}

.card-tag {
    display: inline-block;
    font-size: 11px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.5);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 5px;
    padding: 2px 8px;
    margin-bottom: 1rem;
}

.card h1 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #fff;
}

.card > p {
    color: rgba(255,255,255,0.55);
    font-size: 14px;
    margin-bottom: 1.5rem;
}

.field { margin-bottom: 12px; }

.field label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    margin-bottom: 5px;
}

.field input {
    width: 100%;
    height: 38px;
    padding: 0 12px;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: calc(var(--radius) - 2px);
    background: rgba(255,255,255,0.08);
    color: #fff;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.field input::placeholder { color: rgba(255,255,255,0.3); }
.field input:focus { border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.12); }

.btn {
    width: 100%;
    height: 38px;
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: calc(var(--radius) - 2px);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    margin-top: 6px;
    transition: background 0.2s;
}

.btn:hover { background: rgba(255,255,255,0.25); }

.msg {
    font-size: 13px;
    padding: 8px 12px;
    border-radius: calc(var(--radius) - 2px);
    margin-bottom: 12px;
    border: 1px solid;
}

.msg.error {
    background: rgba(185,28,28,0.25);
    color: #fca5a5;
    border-color: rgba(239,68,68,0.4);
}

.msg.success {
    background: rgba(21,128,61,0.25);
    color: #86efac;
    border-color: rgba(34,197,94,0.4);
}

</style>
</head>
<body>

<p class="school">Inabanga College of Arts and Sciences</p>

<div class="card">
    <span class="card-tag">Lost &amp; Found System</span>
    <h1>Sign in</h1>
    <p>Enter your credentials to continue.</p>

    <?php if (isset($error)): ?>
        <div class="msg error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

<form method="POST">
        <div class="field">
            <label for="login-user">Username</label>
            <input id="login-user" type="text" name="username" placeholder="your_username" required autocomplete="username">
        </div>
        <div class="field">
            <label for="login-pass">Password</label>
            <input id="login-pass" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
        </div>
        <button class="btn" name="login">Sign in</button>
    </form>

</div>

</body>
</html>