<?php
$url = $_GET['url'];
$filename = uniqid().'.jpg';
exec('phantomjs p.js "'.$_GET['url'].'" "'.$filename.'"' );
$file = __DIR__.'/'.$filename;
$type = 'image/png';
header('Content-Type:'.$type);
header('Cache-Control: no-cache, no-store, must-revalidate');

header('Pragma: no-cache');
header('Expires: 0');


header('Content-Length: ' . filesize($file));
readfile($file);
die();