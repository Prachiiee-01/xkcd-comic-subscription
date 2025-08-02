<?php
session_start();
require_once 'functions.php';

$message = '';
$email = $_SESSION['email'] ?? '';

if (empty($email)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $code = trim(htmlspecialchars($_POST['verification_code']));

    if (verifyCode($email, $code)) {
        registerEmail($email);
        unset($_SESSION['email']);
        $message = "<span style='color:lightgreen;'>✅ Subscription successful! You'll now receive XKCD comics daily.</span>";
    } else {
        $message = "<span style='color:#f85149;'>❌ Invalid verification code. Please try again.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email - ComicConnect</title>
    <style>
        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0d1117 url('https://cdn.pixabay.com/photo/2021/03/26/07/28/security-6121694_1280.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 0 20px #58a6ff;
            width: 90%;
            max-width: 450px;
            text-align: center;
            color: #f0f0f0;
        }

        h1 {
            color: #58a6ff;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            font-size: 1rem;
            text-align: center;
            letter-spacing: 2px;
        }

        button {
            padding: 12px;
            width: 100%;
            background-color: #21262d;
            color: #f0f0f0;
            border: 2px solid #58a6ff;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }

        button:hover {
            background-color: #58a6ff;
            color: #0d1117;
        }

        .message {
            margin-top: 15px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Verify Your Email</h1>
        <form method="post">
            <input type="text" name="verification_code" placeholder="Enter 6-digit code" maxlength="6" required />
            <button id="submit-verification" type="submit">Verify</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
