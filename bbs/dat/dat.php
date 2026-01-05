<?php

require '../../user-settings.php';

if (!isset($_GET['bbs'])) {
    exit('bbsが指定されていません。');
}
if (!isset($_GET['thread'])) {
    exit('threadが指定されていません。');
}

$bbs = basename($_GET['bbs']);
$thread = basename($_GET['thread']);

// ヘッダー指定
header('Content-Type: text/plain; charset=Shift_JIS');
header('Connection: keep-alive');

// 特殊スレの場合
if ($thread === '1000000002') {
    $line = '過去ログの名無し<><>2001/09/09 (日) 10:46:41 ID:FAILED<>この板の過去ログ板は現在使えません<>この板の過去ログ板は現在使えません' . "\n";
    $errorData = mb_convert_encoding($line, 'SJIS-win', 'UTF-8');
    echo $errorData;
    exit;
}

// 過去ログデータを取得
$targetUrl = "{$TARGET_SCHEME}://{$DOMAIN}/{$bbs}/dat/{$thread}.dat";
$data = @file_get_contents($targetUrl);

// 通信失敗
if ($data === false) {
    $line = '通信に失敗しました。';
    $errorData = mb_convert_encoding($line, 'SJIS-win', 'UTF-8');
    http_response_code(503);
    echo $errorData;
    exit;
}

// Last-Modified判定
require '../../utils/check-last-modified.php';

// 最終返却
echo $data;
exit;
