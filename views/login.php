<?php
session_start();
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEVARİE - Login</title>
    <style>
        body {
            background: #151515;
            color: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 400px;
            margin: 80px auto 0 auto;
            background: #181818;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.2);
            padding: 40px 32px 32px 32px;
            text-align: center;
        }
        h1 {
            font-size: 2.2rem;
            margin-bottom: 8px;
        }
        .brand {
            color: #ffb396;
            letter-spacing: 2px;
        }
        .desc {
            color: #bdbdbd;
            font-size: 1.1rem;
            margin-bottom: 32px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: none;
            border-radius: 6px;
            background: #232323;
            color: #fff;
            font-size: 1rem;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #ffb396;
            color: #222;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #ffa07a;
        }
        .error {
            color: #ff4d4d;
            background: #2a1818;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 18px;
            font-size: 1rem;
        }
        .footer {
            margin-top: 32px;
            color: #bdbdbd;
            font-size: 0.95rem;
        }
        .footer a {
            color: #ffb396;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to <span class="brand">MEVARİE</span></h1>
        <div class="desc">Thank you for signing up. To start enjoying the benefits, let's set up your account.</div>
        <?php if ($error): ?>
            <div class="error">E-posta veya şifre hatalı!</div>
        <?php endif; ?>
        <form method="post" action="../includes/auth.php">
            <input type="email" name="email" placeholder="account email" required autofocus>
            <input type="password" name="password" placeholder="password" required>
            <button class="btn" type="submit">Continue &rarr;</button>
        </form>
        <div class="footer">
            By signing up, you agree to our <a href="#">Terms of Service</a>.
        </div>
    </div>
</body>
</html>
