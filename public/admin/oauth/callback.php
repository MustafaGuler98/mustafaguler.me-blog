<?php
// public/blog/admin/oauth/callback.php
session_start();
require __DIR__ . '/config.secret.php';

// 1) state doğrulaması
$code  = $_GET['code']  ?? null;
$state = $_GET['state'] ?? null;

if (!$code || !$state || ($state !== ($_SESSION['oauth_state'] ?? ''))) {
  header('Content-Type: text/html; charset=utf-8');
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); window.close();</script>";
  exit;
}

// 2) GitHub access_token iste
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
curl_close($ch);

$data  = json_decode($res, true);
$token = $data['access_token'] ?? null;

// 3) Decap'e sonucu bildir
header('Content-Type: text/html; charset=utf-8');

if (!$token) {
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); window.close();</script>";
  exit;
}

$payload = json_encode(['token' => $token, 'provider' => 'github']);
echo "<script>
  window.opener.postMessage('authorization:github:success:{$payload}', '*');
  window.close();
</script>";
