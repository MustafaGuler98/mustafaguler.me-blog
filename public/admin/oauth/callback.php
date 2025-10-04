<?php
// public/blog/admin/oauth/callback.php

// ---- Hata ayıklama (geçici) ----
ini_set('display_errors', '1');
error_reporting(E_ALL);
// --------------------------------

require __DIR__ . '/config.secret.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// state doğrulaması
$code  = $_GET['code']  ?? null;
$state = $_GET['state'] ?? null;

if (!$code || !$state || !isset($_SESSION['oauth_state']) || $state !== $_SESSION['oauth_state']) {
  header('Content-Type: text/html; charset=utf-8');
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); window.close();</script>";
  exit;
}

// access token al
$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => ['Accept: application/json'],
  CURLOPT_POSTFIELDS     => [
    'client_id'     => GITHUB_CLIENT_ID,
    'client_secret' => GITHUB_CLIENT_SECRET,
    'code'          => $code,
    'redirect_uri'  => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  ],
]);
$res = curl_exec($ch);
if ($res === false) {
  $err = curl_error($ch);
  curl_close($ch);
  header('Content-Type: text/plain; charset=utf-8');
  http_response_code(500);
  echo "cURL error: $err";
  exit;
}
curl_close($ch);

$data  = json_decode($res, true);
$token = $data['access_token'] ?? null;

header('Content-Type: text/html; charset=utf-8');
if (!$token) {
  // Hata detay için geçici çıktı (güvenlik gereği sonra kapatın)
  echo "<pre>".htmlspecialchars($res, ENT_QUOTES, 'UTF-8')."</pre>";
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); setTimeout(()=>window.close(), 2000);</script>";
  exit;
}

$payload = json_encode(['token' => $token, 'provider' => 'github']);
echo "<script>
  // Decap'in beklediği mesaj
  window.opener.postMessage('authorization:github:success:{$payload}', '*');
  setTimeout(() => window.close(), 200);
</script>";
