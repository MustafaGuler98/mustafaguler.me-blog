<?php
// public/blog/admin/oauth/callback.php
declare(strict_types=1);
session_start();

require __DIR__ . '/config.secret.php'; // GITHUB_CLIENT_ID, GITHUB_CLIENT_SECRET

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

$code  = $_GET['code']  ?? null;
$state = $_GET['state'] ?? null;

if (!$code || !$state || !isset($_SESSION['oauth_state']) || $state !== $_SESSION['oauth_state']) {
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); setTimeout(()=>window.close(), 500);</script>";
  exit;
}

// access_token al
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
    'state'         => $state,
  ],
]);
$res = curl_exec($ch);
if ($res === false) {
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); setTimeout(()=>window.close(), 500);</script>";
  exit;
}
$data  = json_decode($res, true);
$token = $data['access_token'] ?? null;

if (!$token) {
  echo "<script>window.opener.postMessage('authorization:github:denied', '*'); setTimeout(()=>window.close(), 500);</script>";
  exit;
}

$payload = json_encode(['token' => $token, 'provider' => 'github']);
echo "<script>
  try {
    window.opener.postMessage('authorization:github:success:' + {$payload}, '*');
  } catch (e) {
    // Eski Decap biçimi:
    window.opener.postMessage('authorization:github:success', '*');
  }
  setTimeout(()=>window.close(), 500);
</script>";
