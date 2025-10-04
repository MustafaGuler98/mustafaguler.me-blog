<?php
require __DIR__ . '/config.secret.php';
session_start();

$_SESSION['oauth_state'] = bin2hex(random_bytes(16));

$params = http_build_query([
  'client_id'    => GITHUB_CLIENT_ID,
  'redirect_uri' => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  'scope'        => 'repo',
  'state'        => $_SESSION['oauth_state'],
]);

header("Location: https://github.com/login/oauth/authorize?$params");
exit;
