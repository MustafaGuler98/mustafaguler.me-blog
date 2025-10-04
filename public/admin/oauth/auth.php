<?php
require __DIR__ . '/config.secret.php';
session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = http_build_query([
  'client_id'    => GITHUB_CLIENT_ID,
  'redirect_uri' => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  'scope'        => 'repo,user',
  'state'        => $state,
]);

header('Location: https://github.com/login/oauth/authorize?' . $params);
exit;
