<?php if (!defined('APP')) {die();} 
$loginStatus = $session->getLoginStatus();

?>
	<section class="our-work">
		<p>				
		<div class="clear"></div>
		
		<div>
		<form id = 'newGalleryElement' enctype='multipart/form-data' action = 'upload' method = 'POST'>
		<label>Ваш ник (используется для личной галлереи)</label><br>
		<input name = "nick" type = "text" value = "<?=$session->getSession()?>" size = "29" id = "nick" <?php if ($loginStatus == 1) { ?> disabled  <?php } ?> /><br><br>
		<label>Выберите фаил картинки (Макс. размер 10Мб)</label><br>
			<input name = "file" class = "file_upload" type = "file" id = "file" accept="image/*" /> 
			<br>
			<a href="#" id = "formGo" class="button13">Закачать
												изображение на сервер</a>
		</form>
		
		</div>
		</p>

		<hr>

<?php 
$img = array('coast', 'island', 'balloon', 'mountain');
if ($loginStatus == 0) {
?>
		<ul class="grid">
			<li class="small" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="large" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="large" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="small" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			
<?php } else {
	$file = new Files($db);
	$imgs = $file->getMainFilesBySession($session->getSession());

if ($imgs) {
	
?>		
<div class = 'clear'></div>
<div class = 'mainConteiner'>

<?php
        foreach ($imgs as $img) {
?>

<div class = 'imgConteiner'>
<a href = "/view/<?=$img['hash']?>" >
<img class = "image" width = '280px;' height = '260px;' src = '<?=$img['link']?>'> </img>
</a> 
<br>
<div class = 'ImgName'> 
Название: <? echo substr($img['original_name'], 0, 30); ?> <br>
Дата создания: <?=$img['stamp']?> <br>
<center>
<a href = "/view/<?=$img['hash']?>" > Подробнее... </a> 
</center>
</div>
</div>
<?
    }
?>

</div>

<?	
} else { ?>

	<ul class="grid">
			<li class="small" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="large" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="large" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="small" style="background-image: url(assets/img/<?php $rand = random_int(0,3); echo $img[$rand]; ?>.jpg);"></li>
			
<?php	
}
}	
?>

			
		</div>
	</section>	
	
		<center><hr></center>