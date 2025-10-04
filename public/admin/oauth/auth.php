<?php
require __DIR__ . '/config.secret.php';

session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// GitHub yetkilendirme sayfasına yönlendir
$params = [
  'client_id'     => GITHUB_CLIENT_ID,
  'redirect_uri'  => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
 
  'scope'         => 'repo,user',   
  'state'         => $state,
  'allow_signup'  => 'false',
];
$qs = http_build_query($params);
header("Location: https://github.com/login/oauth/authorize?$qs");
exit;
