<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

$message = '';

// Handle email submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();

        // Save code to codes/ folder
        saveVerificationCode($email, $code);

        // Send verification email
        if (sendVerificationEmail($email, $code)) {
            $_SESSION['email'] = $email;
            header("Location: verify.php");
            exit;
        } else {
            $message = "❌ Failed to send verification email. Please try again.";
        }
    } else {
        $message = "⚠️ Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ComicConnect 🚀</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1740&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(0, 0, 0, 0.82);
            padding: 40px;
            width: 90%;
            max-width: 480px;
            border-radius: 20px;
            box-shadow: 0 0 20px #58a6ff;
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #58a6ff;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="email"] {
            padding: 12px;
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        button {
            padding: 12px;
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
            margin-top: 20px;
            font-size: 1.1rem;
            color: #f85149;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ComicConnect 🚀</h1>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email to subscribe" required />
            <button type="submit">Send Verification Code</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
