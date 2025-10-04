<?php
require __DIR__ . '/config.secret.php';

session_start();
if (!isset($_GET['code'])) { $err = 'Missing code'; goto error; }
if (!isset($_GET['state']) || !hash_equals($_SESSION['oauth_state'] ?? '', $_GET['state'])) {
  $err = 'Invalid state';
  goto error;
}

$code        = $_GET['code'];
$clientId    = GITHUB_CLIENT_ID;
$clientSecret= GITHUB_CLIENT_SECRET;
$redirectUri = 'https://mustafaguler.me/blog/admin/oauth/callback.php';

$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => ['Accept: application/json'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS     => http_build_query([
    'client_id'     => $clientId,
    'client_secret' => $clientSecret,
    'code'          => $code,
    'redirect_uri'  => $redirectUri,
  ]),
]);
$resp = curl_exec($ch);
if ($resp === false) { $err = 'OAuth request failed'; goto error; }
$data = json_decode($resp, true);
$token = $data['access_token'] ?? null;
if (!$token) { $err = 'No access_token'; goto error; }

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<meta charset="utf-8" />
<script>
  (function () {
    try {
      var payload = 'authorization:github:success:' + <?= json_encode($token) ?>;
      // Decap beklenen mesaj:
      window.opener && window.opener.postMessage(payload, '*');
      window.close();
    } catch (e) {
      document.body.textContent = 'Login ok but could not notify opener. You can close this window.';
    }
  })();
</script>
<body>Logging you in…</body>
<?php
exit;

error:
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<meta charset="utf-8" />
<script>
  (function () {
    var payload = 'authorization:github:error:<?= htmlspecialchars($err ?? "unknown", ENT_QUOTES) ?>';
    window.opener && window.opener.postMessage(payload, '*');
  })();
</script>
<body>OAuth error: <?= htmlspecialchars($err ?? "unknown") ?></body>
