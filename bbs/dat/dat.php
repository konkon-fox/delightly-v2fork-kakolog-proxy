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

// 過去ログdat
$dir1 = substr($thread, 0, 4);
$dir2 = substr($thread, 0, 5);
$kakoDir = "{$bbs}/kako/{$dir1}/{$dir2}";
$kakoFile = "{$kakoDir}/{$thread}.dat";

// 過去ログdatへリダイレクト
$targetUrl = "/{$bbs}/kako/{$dir1}/{$dir2}/{$thread}.dat";
header('Location: ' . $targetUrl, true, 302);
exit;
