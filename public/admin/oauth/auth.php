<?php
// public/blog/admin/oauth/auth.php
session_start();
require __DIR__ . '/config.secret.php';

// CSRF koruması için random state üret
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// GitHub authorize URL
$params = [
  'client_id'    => GITHUB_CLIENT_ID,
  'redirect_uri' => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  'scope'        => GITHUB_SCOPE,
  'state'        => $state,
  'allow_signup' => 'false',
];

$location = 'https://github.com/login/oauth/authorize?' . http_build_query($params);
header('Location: ' . $location);
exit;
