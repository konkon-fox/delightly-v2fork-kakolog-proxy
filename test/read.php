<?php

require '../user-settings.php';

if (!isset($_GET['bbs'])) {
    exit('bbsが指定されていません。');
}
if (!isset($_GET['thread'])) {
    exit('threadが指定されていません。');
}
if (!isset($_GET['option'])) {
    exit('optionが指定されていません。');
}

$bbs = basename($_GET['bbs']);
$thread = basename($_GET['thread']);
$option = basename($_GET['option']);

$targetUrl = "{$TARGET_SCHEME}://{$DOMAIN}/test/read.cgi/{$bbs}/{$thread}/{$option}";

header('Location: ' . $targetUrl, true, 301);
exit;
