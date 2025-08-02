<?php
require_once 'functions.php';

function fetchRandomXKCDComic() {
    // Get latest comic number
    $latestData = file_get_contents('https://xkcd.com/info.0.json');
    if (!$latestData) return null;
    $latest = json_decode($latestData, true);
    $latestNum = $latest['num'];

    // Generate a random comic ID
    $randomID = rand(1, $latestNum);

    // Fetch that random comic
    $data = file_get_contents("https://xkcd.com/$randomID/info.0.json");
    if (!$data) return null;
    $json = json_decode($data, true);
    if (!$json) return null;

    return [
        'title' => htmlspecialchars($json['title']),
        'img'   => htmlspecialchars($json['img']),
        'alt'   => htmlspecialchars($json['alt']),
        'num'   => $json['num']
    ];
}

function sendComicEmail($email, $comic) {
    $subject = "Your Daily XKCD Comic - {$comic['title']}";
    $unsubscribeLink = "http://localhost/xkcd-Prachiiee-01/src/unsubscribe.php?email=" . urlencode($email);

    $message = "
        <html>
        <head><title>XKCD Comic</title></head>
        <body style='font-family: Arial, sans-serif; background-color: #111; color: #eee; padding: 20px;'>
            <h2>Today's XKCD: {$comic['title']}</h2>
            <p><img src='{$comic['img']}' alt='{$comic['alt']}' style='max-width:100%; height:auto; border:1px solid #444; padding: 10px; background:#222;'></p>
            <p style='font-style: italic;'>{$comic['alt']}</p>
            <p><a href='https://xkcd.com/{$comic['num']}' style='color:#00bfff;'>View on XKCD.com</a></p>
            <hr style='border: none; border-top: 1px solid #444;'/>
            <p>If you no longer wish to receive these comics, <a href='$unsubscribeLink' style='color:#ff6666;'>click here to unsubscribe</a>.</p>
        </body>
        </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: ComicConnect <prachii2986@gmail.com>\r\n";

    mail($email, $subject, $message, $headers);
}

// Main flow
$emails = getRegisteredEmails();
$comic = fetchRandomXKCDComic();

if ($comic && !empty($emails)) {
    foreach ($emails as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendComicEmail($email, $comic);
        }
    }

    // Optional: log the sent emails
    file_put_contents(__DIR__ . '/cron_log.txt', "[" . date('Y-m-d H:i:s') . "] Sent comic #{$comic['num']} to: " . implode(', ', $emails) . "\n", FILE_APPEND);
}
?>
