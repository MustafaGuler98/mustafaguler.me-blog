<?php
// public/blog/admin/oauth/auth.php
declare(strict_types=1);
session_start();

require __DIR__ . '/config.secret.php'; // GITHUB_CLIENT_ID sabiti burada

// Rastgele state üret ve session'a yaz
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// GitHub authorize endpoint'ine yönlendir
$params = [
  'client_id'    => GITHUB_CLIENT_ID,
  'redirect_uri' => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  'scope'        => 'repo', // özel repo'ya erişim gerekiyorsa 'repo'; public ise 'public_repo' da olur
  'state'        => $state,
  'allow_signup' => 'false',
];

header('Cache-Control: no-store');
header('Location: https://github.com/login/oauth/authorize?' . http_build_query($params));
exit;
