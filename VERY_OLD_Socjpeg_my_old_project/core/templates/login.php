<?php if (!defined('APP')) {die();} ?>

<section class="our-work">
<br>
		<h3 class="title">Вход / Регистрация</h3>
		<p>
		Если у вас нет логина, просто введите пароль и вы будете зарегестрированы, а также получите доступ ко всем изображениям с которыми работали ранее. <br>
		Если у вас есть логин, ведите его и пароль от него , и вы получите доступ ко всем изображениям с которыми работали ранее. 
		</p>


		
		<div class="clear"></div>
		
		<div>
		<form id = 'login' action = 'loginGo' method = 'POST'>
		<label>Вы можете зарегеистрироваться или войти</label><br>
		<input name = "nick" type = "text" value = "<?=$session->getSession()?>" size = "29" id = "nick" /><br>
		<label>Введите пароль</label><br>
		<input name = "pass" type = "password" size = "29" id = "pass" />
			<br>
			<a href="#" id = "formLoginGo" class="button13">Войти</a>
		</form>
		
		</div>


		<hr>

<?php 
$img = array('coast', 'island', 'balloon', 'mountain');

?>
		<ul class="grid">
			<li class="small" style="background-image: url(assets/img/<?php $rand = rand(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="large" style="background-image: url(assets/img/<?php $rand = rand(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="large" style="background-image: url(assets/img/<?php $rand = rand(0,3); echo $img[$rand]; ?>.jpg);"></li>
			<li class="small" style="background-image: url(assets/img/<?php $rand = rand(0,3); echo $img[$rand]; ?>.jpg);"></li>
		</div>
	</section>