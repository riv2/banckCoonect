<?php if (!defined('APP')) {die();}

$sessionId = $session->getSession();
if (!$sessionId) {
 goBack();
}

if (!isset($args[1]) && strlen($args[1]) != 32) {
	goBack();
} else {
	$hash = $args[1];
}

$Files = new Files($db);
$files = $Files->getFilesByHash($hash);



?>
<div class = "clear"> </div>

<br>
<div class = 'mainConteiner'>
<center><hr></center>
<center><h2>Результат на изображение: <?php echo $files[0]['original_name']; ?> от  <?php echo $files[0]['stamp']; ?></h2> 
<a style = "text-decoration:underline;" href = '/down/<?php echo $files[0]['hash']; ?>'> Скачать все архивом </a> </center>

<div class ='clear'></div>

<?php
        foreach ($files as $img) {

?>

<div class = 'imgConteiner'>
<a href = '<?php echo $img['link']; ?>' target = '_blank'>
<img class = 'img_prev' width = '280px;' height = '260px;' src = '<?php echo $img['link']; ?>'> </img>
</a>
<br>
<div class = 'ImgName'>
<b>Тип:</b> <?php echo getTypeSoc($img['type']); ?> <br>
<b>Разрешение:</b> <?php echo $img['resolution']; ?> (px)<br>
<?php if ($img['descr']) { ?>
<b>Описание:</b> <?php echo $img['descr']; ?><br>
<?php } ?>
<b>Сылка на изображение:</b> <input type = "text" value = "<?php echo $img['link']; ?>" size = "30"/>
<center> <a href = '/down/<?php echo $img['name']; ?>'> Скачать </a></center>


 </div>
</div>

<?
        }
?>

</div>

<div class = "clear"> </div>
<br><br>
<center><hr></center>