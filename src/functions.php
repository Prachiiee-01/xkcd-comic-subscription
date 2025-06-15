<?php

function sanitizeEmailFilename($email) {
    $email = trim(strtolower($email));
    return str_replace(['@', '.'], '_', $email);
}

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sendVerificationEmail($email, $code) {
    $subject = "ComicConnect Email Verification Code";
    $message = "
        <html>
        <body style='background:#0d1117; color:#eee; font-family:Segoe UI, sans-serif;'>
            <h2 style='color:#58a6ff;'>🚀 Verify Your Email</h2>
            <p>Use this code to verify your subscription:</p>
            <p style='font-size: 24px; font-weight: bold;'>$code</p>
        </body>
        </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: ComicConnect <prachii2986@gmail.com>\r\n";

    return mail($email, $subject, $message, $headers); // ✅ RETURN added
}

function saveVerificationCode($email, $code) {
    $filename = __DIR__ . '/codes/' . sanitizeEmailFilename($email) . '.txt';
    if (!file_exists(__DIR__ . '/codes')) {
        mkdir(__DIR__ . '/codes', 0777, true);
    }
    file_put_contents($filename, $code);
}

function verifyCode($email, $code) {
    $filename = __DIR__ . '/codes/' . sanitizeEmailFilename($email) . '.txt';
    return file_exists($filename) && trim(file_get_contents($filename)) === $code;
}

function registerEmail($email) {
    $email = trim($email);
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        file_put_contents($file, $email . PHP_EOL);
    } else {
        $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!in_array($email, $emails)) {
            file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
        }
    }
}

function getRegisteredEmails() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return [];
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function sendUnsubscribeEmail($email, $code) {
    $subject = "ComicConnect Unsubscription Code";
    $message = "
        <html>
        <body style='background:#0d1117; color:#eee; font-family:Segoe UI, sans-serif;'>
            <p>To confirm un-subscription, use this code: <strong>$code</strong></p>
        </body>
        </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: ComicConnect <prachii2986@gmail.com>\r\n";

    return mail($email, $subject, $message, $headers); // ✅ added return for unsubscribe too
}
