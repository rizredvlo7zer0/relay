<?php
// relay.php (DEPLOY DI RAILWAY)

http_response_code(200);
header("Content-Type: application/json");

// ambil body asli dari payment
$raw = file_get_contents("php://input");

// log untuk debug
file_put_contents(
    "relay.log",
    date('Y-m-d H:i:s') . " RAW: " . $raw . PHP_EOL,
    FILE_APPEND
);

// target hosting lama
$target_url = "https://viera.byethost7.com/neobank.php";

// kirim ulang ke hosting lama via CURL
$ch = curl_init($target_url);
curl_setopt_array($ch, [
    CURLOPT_POST            => true,
    CURLOPT_POSTFIELDS      => $raw,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_HTTPHEADER      => [
        "Content-Type: application/json",
        "User-Agent: Railway-Relay/1.0"
    ],
    CURLOPT_TIMEOUT         => 15,
    CURLOPT_SSL_VERIFYPEER  => false,
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// log response hosting lama
file_put_contents(
    "relay.log",
    date('Y-m-d H:i:s') . " RESP: " . $response . PHP_EOL,
    FILE_APPEND
);

// respon ke payment gateway
if ($error) {
    echo json_encode([
        "success" => false,
        "error" => $error
    ]);
} else {
    echo json_encode([
        "success" => true,
        "relay_http" => $httpcode
    ]);
}