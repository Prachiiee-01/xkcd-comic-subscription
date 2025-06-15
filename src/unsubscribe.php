<?php
require_once 'functions.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Send unsubscribe verification code
    if (isset($_POST['unsubscribe_email']) && !isset($_POST['verification_code'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = generateVerificationCode();

        if (!file_exists(__DIR__ . '/codes')) {
            mkdir(__DIR__ . '/codes');
        }

        $filename = __DIR__ . "/codes/" . sanitizeEmailFilename($email) . ".txt";
        file_put_contents($filename, $code);
        sendUnsubscribeEmail($email, $code);

        $message = "<p style='color: #58a6ff;'>📩 Verification code sent to your email.</p>";
    }

    // Step 2: Verify code and remove email
    if (isset($_POST['unsubscribe_email']) && isset($_POST['verification_code'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = trim($_POST['verification_code']);
        $filename = __DIR__ . "/codes/" . sanitizeEmailFilename($email) . ".txt";

        if (file_exists($filename) && $code === trim(file_get_contents($filename))) {
            $emails = getRegisteredEmails();
            $updated = array_filter($emails, fn($line) => trim($line) !== $email);
            file_put_contents(__DIR__ . '/registered_emails.txt', implode(PHP_EOL, $updated) . PHP_EOL);
            unlink($filename);
            $message = "<p style='color: #58a6ff;'>🚫 <strong>$email</strong> has been unsubscribed.</p>";
        } else {
            $message = "<p style='color: #f85149;'>❌ Invalid code. Please try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe - ComicConnect</title>
    <style>
        body {
            background: #0d1117;
            color: #c9d1d9;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        h1 {
            color: #ff7b72;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #161b22;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
            width: 100%;
            max-width: 400px;
        }

        input {
            padding: 10px;
            border: none;
            border-radius: 6px;
            outline: none;
            background-color: #0d1117;
            color: #c9d1d9;
            border: 1px solid #30363d;
        }

        button {
            background-color: #21262d;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #30363d;
        }

        .message {
            margin-top: 20px;
            font-size: 1.1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>🔕 Unsubscribe from Comics</h1>

    <form method="POST">
        <input type="email" name="unsubscribe_email" placeholder="Enter your email" required>
        <button type="submit" id="submit-unsubscribe">Unsubscribe</button>
    </form>

    <form method="POST" style="margin-top: 20px;">
        <input type="email" name="unsubscribe_email" placeholder="Enter your email again" required>
        <input type="text" name="verification_code" placeholder="Enter verification code" maxlength="6" required>
        <button type="submit" id="submit-verification">Verify</button>
    </form>

    <div class="message"><?= $message ?></div>
</body>
</html>
