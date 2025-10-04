<?php
require __DIR__ . '/config.secret.php';
session_start();
if (!isset($_GET['code'])) {
  http_response_code(400);
  echo 'Missing code';
  exit;
}
if (!isset($_GET['state']) || !hash_equals($_SESSION['oauth_state'] ?? '', $_GET['state'])) {
  http_response_code(400);
  echo 'Invalid state';
  exit;
}

$code        = $_GET['code'];
$clientId    = GITHUB_CLIENT_ID;
$clientSecret= GITHUB_CLIENT_SECRET;
$redirectUri = 'https://mustafaguler.me/blog/admin/oauth/callback.php';

// GitHub'dan access_token al
$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
  CURLOPT_POST       => true,
  CURLOPT_HTTPHEADER => ['Accept: application/json'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS => http_build_query([
    'client_id'     => $clientId,
    'client_secret' => $clientSecret,
    'code'          => $code,
    'redirect_uri'  => $redirectUri,
  ]),
]);
$resp = curl_exec($ch);
if ($resp === false) {
  http_response_code(500);
  echo 'OAuth request failed';
  exit;
}
$data = json_decode($resp, true);
$token = $data['access_token'] ?? null;
if (!$token) {
  http_response_code(500);
  echo 'No access_token';
  exit;
}

// Decap JSON bekler:
header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: https://mustafaguler.me');
echo json_encode(['token' => $token]);
