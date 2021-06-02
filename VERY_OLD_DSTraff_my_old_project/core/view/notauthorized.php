<!DOCTYPE HTML>
<html lang="ru">
<head>
<link rel="stylesheet" type="text/css" href="public/css/normalize.css">
<link rel="stylesheet" type="text/css" href="public/css/notauthorized.css">
<script language='JavaScript' src='<?=jQuery;?>' type='text/javascript'></script>
<script language='JavaScript' src='<?=notauthorizedJs;?>' type='text/javascript'></script>
<!-- <script language='JavaScript' src='../public/js/snowfall.jquery.min.js' type='text/javascript'></script> -->

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Content-Language" content="ru">
<meta name=”robots” content="index, follow" />
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="description" content="DSTraff - Detailed Separation of Traffic (Развернутое Разделение Трафика)">
<meta name="keywords" content="фильтрация трафика, распределение трафика, трафик, трекер, добыча трафика, бесплатный трафик, web, wap, мобильный трафик, 
рекламные форматы, редирект, фрейм, реклама, как слить трафик с сайта, как выгодно слить трафик, как правильно слить трафик, монетизация трафика">
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

<title><?php echo LANG['notauthorized']['title']; ?></title>

<input id = "Site" type = "hidden" value = "<?php echo Site;?>">
<input id = "rule" type = "hidden" value = "">
</head>
<body>

<?php

	 $smarty->display('frontpage.tpl');

?>



</body>
</html>