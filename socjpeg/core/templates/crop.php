<div class="container">
<div class="row">
<div class="span12">
<div class="jc-demo-box">

<?php
$file = new Files($db);
$sessionId = $session->getSession();
$linkImg = $file->getFileBySession($sessionId);
$urlImg = $file->getUrlBySession($sessionId);
require_once($_SERVER['DOCUMENT_ROOT'].'/core/etc/SimpleImage.php');
$imager = new SimpleImage($urlImg);
$w = (int)$imager->get_width();
$h = (int)$imager->get_height();


?>
<br>
<center>
	<hr>
	
	<table class = "table_crop">
		<tr>
			<td colspan="4">
			<label>Параметры изображения</label>
			</td>
		</tr>
		<tr>
			<td>
			<label>Ширина</label>
			</td>
			<td>
			<label>Высота</label>
			</td>
			<td>
			<label>Новая ширина</label>
			</td>
			<td>
			<label>Новая высота</label>
			</td>
		</tr>
		<tr>
			<td>
			 <input type="text" size="4" value = "<?=$w?>" />
			</td>
			<td>
			   <input type="text" size="4" value = "<?=$h?>" />
			</td>
			<td>
			   <input type="text" size="4" id="w1" />
			</td>
			<td>
			   <input type="text" size="4" id="h1" />
			</td>
		</tr>
	</table>
	
   <br>
	
  <img src="<?=$linkImg?>" id="target" alt="[Jcrop Example]" />
  <br>
  <hr>
</center>

<div><center>
 <form  method = "POST" action="https://socjpeg.ru/crop/" id = "formElement1">
 <label>Свой размер: </label>	<br>
 <input type="number" size="4" name="sw" placeholder = "Ширина" maxlength="4" /><br>
 <input type="number" size="4" name="sh" placeholder = "Высота" maxlength="4" /><br>
 <label>Оставить оригинальный размер </label><input type="checkbox" name="original" value = 'Y'/><br>
 
 <a id = "formGo1" class="button14">Обрезать и создать</a>
<hr> 
</center>	

</div>	
	
 
    <input type = "hidden" name = "urlImg" value = "<?=$urlImg?>">
	 <input type = "hidden" name = "hash" value = "<?=$args[1]?>">
   <input type="hidden" size="4" id="x1" name="x1" />
   <input type="hidden" size="4" id="y1" name="y1" />
   <input type="hidden" size="4" id="x2" name="x2" />
   <input type="hidden" size="4" id="y2" name="y2" />
   <input type="hidden" size="4" id="w" name="w" />
   <input type="hidden" size="4" id="h" name="h" />
  </form>

  
<div class="clearfix"></div>

</div>
</div>
</div>
</div>