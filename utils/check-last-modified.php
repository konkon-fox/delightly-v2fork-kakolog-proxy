<?php

// Last-Modified 取得
$lastModified = '';
// ※$http_response_headerはPHP8.5.0で非推奨
foreach ($http_response_header as $header) {
    if (stripos($header, 'Last-Modified:') !== false) {
        $lastModifiedOffset = strpos($header, ':') + 1;
        $lastModified = substr($header, $lastModifiedOffset);
        $lastModified = explode(';', $lastModified)[0];
        $lastModified = trim($lastModified);
        break;
    }
}
if (!empty($lastModified)) {
    header('Last-Modified: ' . $lastModified);
}

// If-Modified-Since 対応 ※最終変更日時が不変なら返さない
$ifModifiedSince = trim($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '');
if (!empty($lastModified) && $ifModifiedSince === $lastModified) {
    http_response_code(304);
    exit;
}
