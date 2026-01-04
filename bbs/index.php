<?php

require '../user-settings.php';

if (!isset($_GET['bbs'])) {
    exit('bbsが指定されていません。');
}

$bbs = basename($_GET['bbs']);

// ヘッダー指定
header('Content-Type: text/html; charset=Shift_JIS');
header('Connection: keep-alive');

// 板名ファイルを取得
$targetUrl = "{$TARGET_SCHEME}://{$DOMAIN}/{$bbs}/setting.json";
$settingData = @file_get_contents($targetUrl);

// 通信失敗
if ($settingData === false) {
    http_response_code(503);
    $html = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>通信に失敗しました</title>
</head>
<body>
通信に失敗しました
</body>
</html>
EOT;
    echo mb_convert_encoding($html, 'SJIS-win', 'UTF-8');
    exit;
}

// Last-Modified判定
require '../utils/check-last-modified.php';

// 板名を取得
$settingJson = json_decode($settingData, true);
if ($settingJson === null || !isset($settingJson['BBS_TITLE'])) {
    $bbsTitle = '過去ログ';
} else {
    $bbsTitle = $settingJson['BBS_TITLE'] . ' 過去ログ';
}

// 返すhtmlを作成
$html = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{$bbsTitle}</title>
</head>
<body>
この板は過去ログ板です。
</body>
</html>
EOT;

// shift-jisへ
$data = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');

// 最終返却
echo $data;
exit;
