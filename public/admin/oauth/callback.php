<?php
require __DIR__ . '/config.secret.php';
session_start();

// --- 1) state doğrula
if (!isset($_GET['code']) || (($_GET['state'] ?? '') !== ($_SESSION['oauth_state'] ?? ''))) {
  header('Content-Type: text/html; charset=utf-8');
  echo "<!doctype html><meta charset=utf-8><body>STATE MISMATCH
<script>
  window.opener && window.opener.postMessage('authorization:github:denied', '*');
  // setTimeout(()=>window.close(), 2000); // <-- BU SATIRI DEVRE DIŞI BIRAKIN
</script>";
  exit;
}

// --- 2) token iste
// ... (curl kodları aynı kalacak) ...
$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => ['Accept: application/json'],
  CURLOPT_POSTFIELDS     => [
    'client_id'     => GITHUB_CLIENT_ID,
    'client_secret' => GITHUB_CLIENT_SECRET,
    'code'          => $_GET['code'],
    'redirect_uri'  => 'https://mustafaguler.me/blog/admin/oauth/callback.php',
  ],
]);
$res = curl_exec($ch);
curl_close($ch);

$data  = json_decode($res, true);
$token = $data['access_token'] ?? null;

// --- 3) çıktıyı tek sayfada göster + mesaj gönder
header('Content-Type: text/html; charset=utf-8');

if (!$token) {
  echo "<!doctype html><meta charset=utf-8><body>TOKEN YOK / HATA
<pre>".htmlspecialchars($res ?: 'bos cevap')."</pre>
<script>
  window.opener && window.opener.postMessage('authorization:github:denied', '*');
  // setTimeout(()=>window.close(), 4000); // <-- BU SATIRI DA DEVRE DIŞI BIRAKIN
</script>";
  exit;
}

// payload JS tarafında JSON.stringify ile tekrar oluşturulacak
$payload = ['token' => $token, 'provider' => 'github'];
echo "<!doctype html><meta charset=utf-8><body style='font:14px system-ui'>
<b>Callback aktif</b><br>
<div id='log'></div>
<script>
try {
  const payload = ".json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).";
  const msg = 'authorization:github:success:' + JSON.stringify(payload);

  const log = (t)=>document.getElementById('log').innerHTML += t+'<br>';

  // Teşhis: opener var mı?
  if (!window.opener) {
    log('opener: YOK (mesaj gonderemem).');
  } else {
    window.opener.postMessage(msg, '*');
    log('gonderildi: ' + msg.replace(payload.token, '***TOK***'));
  }

} catch (e) {
  document.getElementById('log').innerText = 'JS HATASI: '+ e.message;
}
// setTimeout(()=>window.close(), 3000); // <-- BU SATIRI DA DEVRE DIŞI BIRAKIN
</script>";