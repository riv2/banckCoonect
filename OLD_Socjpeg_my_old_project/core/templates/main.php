<?php if (!defined('APP')) {die();} ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate" />
	<meta name="keywords" content="социальные изображения, обрезать изображения, работа с изображениями, размеры для изображений социальных сетей">
	<meta name="description" content="Сайт для работы, обрезанием, установкой размеров изображений и картинок для социальных сетей Одноклассники, Вконтакте, Инстаграм, FaceBook, Твитер, Youtube.">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Социальные изображения</title>
	<link rel="icon" href="https://socjpeg.ru/favicon.ico?v=<?php $rand = random_int(1,99999); echo $rand;?>"} type="image/x-icon">
	
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
	
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://socjpeg.ru/assets/css/styles.css">
	<link rel="stylesheet" href="https://socjpeg.ru/assets/css/sweet.css">
	<link rel="stylesheet" href="https://socjpeg.ru/assets/css/jquery.Jcrop.css">

</head>

<body style = "display:none;">

	<header>
	<img class = "beta" src = "https://socjpeg.ru/assets/img/beta.png" title = "Beta" />
		<a href="/"><img src = "https://socjpeg.ru/assets/img/logo.png" title = "Logo" /></a>
		<nav>
			<li><a href="/">Главная</a></li>
			<li><a id = "help" href="#">Помощь</a></li>
			<?php if ($session->getLoginStatus() != 1) { ?> <li><a href="https://socjpeg.ru/login">Войти</a></li> <?php } ?>
			<?php if ($session->getLoginStatus() == 1) { ?> <li><a href="https://socjpeg.ru/actions/logout/<?php $rand = random_int(1, 9999); echo $rand; ?>/">Выход</a></li> <?php } ?>
		</nav>
	</header>

	<section class="hero">
		<div class="background-image" style="background-image: url(https://socjpeg.ru/assets/img/hero_<?php $rand = random_int(1, 28); echo $rand; ?>.jpg);"></div>
		<h1>Работа с социальными изображениями</h1>
		<h2>Загружай, обрезай, сохраняй, получай результат.</h2>
	</section>
	<div class = "works">
	<center> <h2>Как это работает </h2>
	<img src = "https://socjpeg.ru/assets/img/work_1.jpg" title = "Logo" />
	<img src = "https://socjpeg.ru/assets/img/work_2.jpg" title = "Logo" />
	<img src = "https://socjpeg.ru/assets/img/work_3.jpg" title = "Logo" />
	</center>
	</div>
<center>
<h3>Для социальных сетей:</h3><br>
<img src="https://socjpeg.ru/assets/img/vk.png" title="Вконтакте" alt="Вконтакте" />
<img src="https://socjpeg.ru/assets/img/ok.png" title="Одноклассники" alt="Одноклассники" />
<img src="https://socjpeg.ru/assets/img/tw.png" title="Твиттер" alt="Твиттер" />
<img src="https://socjpeg.ru/assets/img/fc.png" title="Facebook" alt="Facebook" />
<img src="https://socjpeg.ru/assets/img/you.png" title="Youtube" alt="Youtube" />
<img src="https://socjpeg.ru/assets/img/go.png" title="Google+" alt="Google+" />
<img src="https://socjpeg.ru/assets/img/inst.png" title="Инстаграм" alt="Инстаграм" />
</center>

	<?php 
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/core/' . $template);	
	?>

	<section class="features">
		<h3 class="title">Что такое SocJPEG ? </h3>
		<p>
		Наш проект создан чтобы облегчить жизнь обычного пользователя, когда ему нужно загрузить изображение в ту или иную социальную сеть, так как у каждой социальный сети есть свои требования к размерам, вы можете через наш проект 
		легко и просто, без лишних телодвижений создать нужную вам миниатюру изображения для социальных сетей: Одноклассники, Вконтакте, Инстаграм, FaceBook, Твитер, Youtube, Google+, также задать свой размер изображения.
		На нашем проекте не обязательно регистрироваться, но если вы даже заходите зарегистрироваться, то это займет менее 10 секунд, просто введите пароль для своего ника в разделе Вход/Регистрация. (Кнопка Вход в правом верхнем углу).
		Изображения можно скачать по одному, все сразу одним архивом, или использовать на него ссылку. Мы работаем по защищенному https каналу 24 в сутки 7мь дней в неделю.
		<hr>

		<ul class="grid">
			<li>
				<i class="fa fa-camera-retro"></i>
				<h4>Изображения</h4>
				<p>Редактируй изображения онлайн под нужный формат для соц. сетей. Скачивайте онлайн, по ссылке, все вместе.</p>
			</li>
			<li>
				<i class="fa fa-cubes"></i>
				<h4>Постояная разработка</h4>
				<p>Ресурс постояно дорабатывается и обновляется, что позволяет ему быть актуальным для прогрессирующих социальных сетей.</p>
			</li>
			<li>
				<i class="fa fa-newspaper-o"></i>
				<h4>Своя галлерея</h4>
				<p>Имейте всегда доступ к своим изображениям с любой точки мира в любое время. Регистрация в 1 шаг.</p>
			</li>
		</div>
	</section>


	<section class="reviews">
	<br>
		<h3 class="title">Коментарии:</h3>

<div id="vk_comments"></div>
	<br>
	
<script type="text/javascript" src="//vk.com/js/api/openapi.js?127"></script>

<script type="text/javascript">
 VK.init({apiId: 5612052, onlyWidgets: true});

var width = '800px';
if (screen.width < 1601) {
	var width = '800px';
}
if (screen.width < 1001) {
	var width = '400px';
}
if (screen.width < 601) {
	var width = '200px';
}
VK.Widgets.Comments("vk_comments", {redesign: 1, limit: 10, width: width, attach: "*"});
</script>
	</section>


	<section class="contact">
	<br><br>
		<h2 class="title">Есть предложения или пожелания, нашли баг?</h2>	
		<p>Пишите нам на почту: <a style = "text-decoration:underline;" href="mailto:riv_1988@mail.ru">riv_1988@mail.ru</a> или Skype: <a style = "text-decoration:underline;" href="skype:Kornienko_rivigor?chat">Kornienko_rivigor</a> <br>
		Мы обязательно свяжемся с вами.
		<hr>
	</section>

	<footer>
	<!--
		<ul>
			<li><a href="#"><i class="fa fa-twitter-square"></i></a></li>
			<li><a href="#"><i class="fa fa-facebook-square"></i></a></li>
			<li><a href="#"><i class="fa fa-snapchat-square"></i></a></li>
			<li><a href="#"><i class="fa fa-pinterest-square"></i></a></li>
			<li><a href="#"><i class="fa fa-github-square""></i></a></li>
		</ul>
		-->
		<p>Сделано на  <a href="http://it39.ru/" target="_blank">it39.ru</a></p>
		<p>&copy; 2016 год.</p>
	</footer>

<div class = 'loadingLayer' id = 'loading'> 	<img src = "https://socjpeg.ru/assets/img/load.gif" /> </div>

</body>

	<script language="JavaScript" src="https://socjpeg.ru/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
	<script language="JavaScript" src="https://socjpeg.ru/assets/js/sweet.js" type="text/javascript"></script>
	<script language="JavaScript" src="https://socjpeg.ru/assets/js/jquery.Jcrop.js"></script>
    <script language="JavaScript" src="https://socjpeg.ru/assets/js/init.js" type="text/javascript"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-82819675-1', 'auto');
  ga('send', 'pageview');

</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter40546555 = new Ya.Metrika({
                    id:40546555,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/40546555" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->



</html>
