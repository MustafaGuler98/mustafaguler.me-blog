<?php
// public/blog/admin/oauth/auth.php

// ---- Hata ayıklama (geçici) ----
ini_set('display_errors', '1');
error_reporting(E_ALL);
// --------------------------------

require __DIR__ . '/config.secret.php'; // GITHUB_CLIENT_ID / SECRET burada

// PHP session zorunlu
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// random_bytes yoksa fallback (çok eski PHP için)
if (!function_exists('random_bytes')) {
  function random_bytes($length) {
    $bytes = '';
    for ($i = 0; $i < $length; $i++) $bytes .= chr(mt_rand(0, 255));
    return $bytes;
  }
}

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Gerekli parametreler
$params = [
  'client_id'     => GITHUB_CLIENT_ID,
  'redirect_uri'  => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  // repo yetkisi Decap için güvenli tarafta:
  'scope'         => 'repo',
  'state'         => $state,
  'allow_signup'  => 'false',
];

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Location: https://github.com/login/oauth/authorize?' . http_build_query($params));
exit;
