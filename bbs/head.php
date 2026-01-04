<?php

require '../user-settings.php';

if (!isset($_GET['bbs'])) {
    exit('bbsが指定されていません。');
}

$bbs = basename($_GET['bbs']);

// ヘッダー指定
header('Content-Type: text/html; charset=Shift_JIS');
header('Connection: keep-alive');

// 過去ログデータを取得
$targetUrl = "{$TARGET_SCHEME}://{$DOMAIN}/{$bbs}/head.txt";
$data = @file_get_contents($targetUrl);

// 通信失敗
if ($data === false) {
    http_response_code(503);
    echo mb_convert_encoding('通信に失敗しました。', 'SJIS-win', 'UTF-8');
    exit;
}

// Last-Modified判定
require '../utils/check-last-modified.php';

// 最終返却
echo $data;
exit;
