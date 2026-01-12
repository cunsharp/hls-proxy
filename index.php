<?php
// HLS Stream Proxy with Referer and Origin support
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Origin, Referer, Cookie');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$url = isset($_GET['url']) ? $_GET['url'] : '';
$referer = isset($_GET['referer']) ? $_GET['referer'] : '';
$origin = isset($_GET['origin']) ? $_GET['origin'] : '';

if (empty($url)) {
    http_response_code(400);
    die('Error: URL parameter is required');
}

$url = urldecode($url);

// Determine content type
$contentType = 'application/vnd.apple.mpegurl';
if (strpos($url, '.ts') !== false) {
    $contentType = 'video/mp2t';
} elseif (strpos($url, '.m3u8') !== false) {
    $contentType = 'application/vnd.apple.mpegurl';
} elseif (strpos($url, '.mp4') !== false) {
    $contentType = 'video/mp4';
}

header('Content-Type: ' . $contentType);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_ENCODING, '');

$headers = array();

if (!empty($referer)) {
    $headers[] = 'Referer: ' . urldecode($referer);
}

if (!empty($origin)) {
    $headers[] = 'Origin: ' . urldecode($origin);
}

$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0';
$headers[] = 'Accept: */*';
$headers[] = 'Accept-Language: en-US,en;q=0.9';

if (!empty($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    die('Error fetching stream: ' . $error);
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    die('Error: HTTP ' . $httpCode);
}

echo $response;
?>
