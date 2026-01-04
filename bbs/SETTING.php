<?php

require '../user-settings.php';

if (!isset($_GET['bbs'])) {
    exit('bbsが指定されていません。');
}

$bbs = basename($_GET['bbs']);

// ヘッダー指定
header('Content-Type: text/plain; charset=Shift_JIS');
header('Connection: keep-alive');

// 過去ログデータを取得
$targetUrl = "{$TARGET_SCHEME}://{$DOMAIN}/{$bbs}/SETTING.TXT";
$data = @file_get_contents($targetUrl);

// 通信失敗
if ($data === false) {
    http_response_code(503);
    exit('通信に失敗しました。');
}

// Last-Modified判定
require '../utils/check-last-modified.php';

// 一度utf-8に戻す
$data = mb_convert_encoding($data, 'UTF-8', 'SJIS-win');
$data = preg_replace('/BBS_TITLE=(.*)/', 'BBS_TITLE=$1 過去ログ', $data);

// 再度shift-jisへ
$data = mb_convert_encoding($data, 'SJIS-win', 'UTF-8');

// 最終返却
echo $data;
exit;
