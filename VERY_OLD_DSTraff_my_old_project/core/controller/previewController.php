<?php
class previewController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        if (strlen($request[1]) != 32) {
            header("Location: " . Site);
            exit;
        }
		
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', FALSE);
        header('Pragma: no-cache');   
        
        echo openCss('preview');
?>
<head>
<title>DSTraff - Предосмотр </title>
<script language='JavaScript' src='<?php echo Site; ?>/public/js/jquery-2.1.3.min.js' type='text/javascript'></script>
<script language='JavaScript' src='<?php echo Site; ?>/public/js/preview.js' type='text/javascript'></script>
</head>

<body>
<center><h1> Предосмотр и тестирование потока. </h1> </center>

<div class = "refresh" id = "refresh">Обновить<br>страницу</div>


<table width="100%" height = "800px;" border="1" cellspacing="10" cellpadding="10"> 
<tr> 
<td colspan="3">Шапка сайта</td>
</tr>
<tr> 
<td width="320">
<p>Меню сайта</p>
</td>
<td>Контент</td>
<td width="300">
<p>Банер</p>
<div id = "<?php echo $request[1]; ?>" src = "https://tds.i-cdm.ru/tracker/<?php echo $request[1]; ?>"></div>
<script src = "https://tds.i-cdm.ru/tracker/<?php echo $request[1]; ?>/preview/"></script>
</td>
</tr>
<tr> 
<td colspan="3">Футер</td>
</tr>
</table>




</body>
<?php
    }
}
?>
