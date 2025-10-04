<?php
// public/blog/admin/oauth/callback.php
require __DIR__ . '/config.secret.php';
session_start();

// 1) state doğrulaması
if (!isset($_GET['code']) || (($_GET['state'] ?? '') !== ($_SESSION['oauth_state'] ?? ''))) {
  header('Content-Type: text/html; charset=utf-8');
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); window.close();</script>";
  exit;
}

$code = $_GET['code'];

// 2) GitHub access token al
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

// 3) Decap'in beklediği mesaj formatı
header('Content-Type: text/html; charset=utf-8');

if (!$token) {
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); window.close();</script>";
  exit;
}

$payload = json_encode(['token' => $token, 'provider' => 'github']);

// ÖNEMLİ: Aşağıdaki echo + çift tırnak kullanımı sayesinde {$payload} PHP tarafından stringe gömülür
echo "<script>
  window.opener.postMessage('authorization:github:success:{$payload}', '*');
  // içeriği görmek istersen şimdilik 3sn geciktirebilirsin:
  setTimeout(() => window.close(), 3000);
</script>";
