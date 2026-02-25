<?php

require '../user-settings.php';
require '../utils/safe-file-get-contents.php';

if (!isset($_GET['bbs'])) {
    exit('bbsが指定されていません。');
}

$bbs = basename($_GET['bbs']);

// ヘッダー指定
header('Content-Type: text/plain; charset=Shift_JIS');
header('Connection: keep-alive');

// 除外板の場合
if (in_array($bbs, $excludeBbs, true)) {
    $line = '1000000002.dat<>この板の過去ログ板は現在使えません (1)' . "\n";
    $errorData = mb_convert_encoding($line, 'SJIS-win', 'UTF-8');
    echo $errorData;
    exit;
}

// キャッシュチェック
$cacheFile = "../tmp/cache-{$bbs}.txt";
function checkChache($cacheFile)
{
    $cacheTime = 60;
    // ファイルがなければ終了
    if (!is_file($cacheFile)) {
        return;
    }
    $fileTime = filemtime($cacheFile);
    // 60秒以上なら終了
    if (time() > $fileTime + $cacheTime) {
        return;
    }
    // キャッシュファイル取得
    $cacheData = safe_file_get_contents($cacheFile);
    if ($cacheData === false) {
        return;
    }
    // キャッシュを返却
    echo $cacheData;
    exit;
}
checkChache($cacheFile);

// 署名作成
$time = time();
$sig = hash_hmac('sha256', $time, $YOUR_SECRET_KEY);

// 過去ログデータを取得
$targetUrl = "{$TARGET_SCHEME}://{$DOMAIN}/test/kakolog-subject.php?bbs={$bbs}&time={$time}&sig={$sig}";
$headers = '';
$ifModifiedSince = trim($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '');
$headers .= 'If-Modified-Since: ' . $ifModifiedSince . "\r\n";
$context = stream_context_create([
    'http' => ['header' => $headers],
]);
$data = @file_get_contents($targetUrl, false, $context);

// レスポンスヘッダーをチェック
$statusCode = '';
$lastModified = '';
$subjectTruncated = '';
if ($http_response_header) { // ※$http_response_headerはPHP8.5.0で非推奨
    // 最初の要素はステータス
    $statusLine = $http_response_header[0];
    // ステータスコードを取得
    preg_match('/[0-9]{3}/', $statusLine, $matches);
    $statusCode = $matches[0];
    // ヘッダーから各種値を取得
    foreach ($http_response_header as $header) {
        if (stripos($header, 'Last-Modified:') !== false) {
            // Last-Modified取得
            $lastModifiedOffset = strpos($header, ':') + 1;
            $lastModified = substr($header, $lastModifiedOffset);
            $lastModified = trim($lastModified);
        } elseif (stripos($header, 'Delightly-Subject-Truncated:') !== false) {
            // Delightly-Subject-Truncated取得
            $subjectTruncatedOffset = strpos($header, ':') + 1;
            $subjectTruncated = substr($header, $subjectTruncatedOffset);
            $subjectTruncated = trim($subjectTruncated);
        }
    }
}

// 通信失敗
if ($data === false) {
    http_response_code(503);
    $line = "Origin Server Error: $statusCode";
    $errorData = mb_convert_encoding($line, 'SJIS-win', 'UTF-8');
    echo $errorData;
    exit;
}

// Last-Modified 設定
if (!empty($lastModified)) {
    header('Last-Modified: ' . $lastModified);
}

// 304返却
if ($statusCode === '304') {
    if (is_file($cacheFile)) {
        touch($cacheFile);
    }
    http_response_code(304);
    exit;
}

// 一度utf-8に戻す
$utf8Str = mb_convert_encoding($data, 'UTF-8', 'SJIS-win');

// 部分返却の場合ゴミを取り除く
if ($subjectTruncated === 'true') {
    $utf8Str = preg_replace('/\A.*\n/', '', $utf8Str);
}

// 配列化
$utf8Array = explode("\n", $utf8Str);
$utf8Array = array_filter($utf8Array);

// 文字列化
$utf8Str = implode("\n", $utf8Array) . "\n";

// 再度shift-jisへ
$data = mb_convert_encoding($utf8Str, 'SJIS-win', 'UTF-8');

// キャッシュに保存
file_put_contents($cacheFile, $data, LOCK_EX);

// 最終返却
echo $data;
exit;
