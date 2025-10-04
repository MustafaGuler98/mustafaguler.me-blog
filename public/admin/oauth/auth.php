<?php
require __DIR__ . '/config.secret.php'; 

session_start();
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$clientId    = GITHUB_CLIENT_ID;
$redirectUri = 'https://mustafaguler.me/blog/admin/oauth/callback.php';
$scope       = 'repo';

$authorizeUrl = 'https://github.com/login/oauth/authorize'
  . '?client_id=' . urlencode($clientId)
  . '&redirect_uri=' . urlencode($redirectUri)
  . '&scope=' . urlencode($scope)
  . '&state=' . urlencode($state);

header('Location: ' . $authorizeUrl);
exit;
