<?php

// ヘッダー指定
header('Content-Type: text/html; charset=Shift_JIS');
header('Connection: keep-alive');

$html = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Error</title>
</head>
<body>
Error: この板は<b>閲覧専用の過去ログ板</b>です。
</body>
</html>
EOT;

// shift-jisへ
$data = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');

// 最終返却
echo $data;
exit;
