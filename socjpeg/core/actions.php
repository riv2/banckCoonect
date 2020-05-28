<?php 
if (!defined('APP')) {die();}

if (isset($args)) {
	if ($args[1] == 'logout') {
		$Actions = new Actions($session);
		$Actions->logOut();			
	} elseif ($args[1] == 'getHelp') {
		$Actions = new Actions($session);
		$Actions->getHelp();
	} else {
		goBack();
	}

} 

goBack();
	
class Actions {

	function __construct($clas) {
		$this->clas = $clas;
	}
	
	public function logOut() {
		$this->clas->delSession();
		dumpLog($this->clas);
		goBack();		
	}
	
	public function getHelp() {
	ob_start();	?>
	<div id = "rulesMainConteiner">
	<style> 
	.rulesLayer {
		position:fixed;
		width:100%;
		height:100%;
		z-index:3000;
		background-color:rgba(0,0,0,0.3);
		top:0px; left:0px;
	}

	.rulesConteiner {
		position:fixed;
		width:70%;
		height:88%;
		z-index:3100;
		background-color:rgba(255,255,255,1);
		top:5%; left:15%;
		
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		-webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);
		-moz-box-shadow:    0px 0px 5px 0px rgba(50, 50, 50, 0.75);
		box-shadow:         0px 0px 5px 0px rgba(50, 50, 50, 0.75);
		padding:15px;
		overflow: auto;
		font-size:13px;
		line-height: 1.5;
	}

	.rulesConteiner a {
		text-decoration:underline;
		font-size:16px;
		color: rgba(0,0,255,0.7);
		cursor:pointer;
	}

	.rulesClose {
	position:absolute;
	top:10px;
	right:10px;
	width:20px;
	height:20px;
	background-image: url("../../../assets/img/close_2.png");
	z-index:3200;
	cursor:pointer;
	}
	</style>
	<script>
		$('#rulesClose').click(function () {
		$('#rulesMainConteiner').remove();	
		});
	</script>
	<div class = "rulesLayer">
		<div class = "rulesConteiner">
			<div class = "rulesClose" id = "rulesClose"></div>

			 <center><h1>О системе SocJEPG.</h1> </center>
					<b> 1. Что такое SocJEPG ? </b> <br><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Проект SocJPEG является не коммерческим инструментом для работы с изображениями. <br>
					Через этот проект вы с легкостью можете подогнать ваше изображение под нужный размер для популярных социальных сетей и не только. <br><br>
					<b> 2. Как начать работать с SocJPG ? </b> <br><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Что того чтобы начать работать, вам необходимо придумать ваш ник (псевдоним), он же это индикационный номер в проекте SocJPG, ввести его в поле Ваш ник, и выбрать изображение для обработки через кнопку – выбрать файл, или перетащить файл на поле Файл не выбран.<br> 
					Ограничения по размеру файла – 5мб, по расширениям: 'jpg', 'gif', 'png', 'jpeg', 'swf', 'JPG', 'GIF', 'PNG', 'JPEG'.<br> 
					Далее нажмите на кнопку закачать, и вы попадете в раздел обработки изображения.<br> 
					Далее вы можете обрезать нужный участок вашего изображения, (вы можете ничего не трогать и тогда изображение пройдет на следующий этап как есть), также вы можете поставить галочку оставить оригинальный размер. <br> 
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Вы можете задать 1 изображение своего размера, указав либо длину (высота создастся автоматически пропорционально длине), либо высоту (длина создастся автоматически пропорционально высоте), либо свою длину и высоту.<br> 
					После нажмите кнопку обрезать, и вы попадете в меню готовых изображений для социальных сетей: Одноклассники, Вконтакте, Инстаграм, FaceBook, Твитер, Youtube, Google+, а также сам оригинальный файл, его обрезанная версия, и ваш собственный размер. <br> 
					Каждое изображение имеет превью нажав на которые в новой вкладке откроется это изображение, описание изображения – тип, разрешение, прямая ссылка на изображение, кнопка скачать, по которой вы можете скачать изображение. <br> 
					Также вы можете скачать одним архивом все изображения нажав на кнопку – скачать все или вернуться на главную.<br> <br> 
					<b> 2. Как зарегистрироваться в SocJPG ? </b> <br><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Регистрация в проекте SocJPEG происходит очень просто, для работы в нашем проекте вам не обязательно регистрироваться, но если вы хотите получить доступ к ранее загруженным изображениям,
					то нажмите на кнопку Вход (верхний правый угол проекта), и ведите пароль, нажмите войти,
					если вы ранее небыли зарегистрированы, то пароль привяжется к вашему нику, и под ним вы сможете заходить и получать доступ к ранее загруженным изображениям. <br><br>
					<b> 3. Куда обращатся если возникли вопросы ? </b> <br><br>
					Если есть предложения или пожелания, нашли баг?<br>
					Пишите нам на почту: <a style = "text-decoration:underline;" href="mailto:riv_1988@mail.ru">riv_1988@mail.ru</a> или Skype: <a style = "text-decoration:underline;" href="skype:Kornienko_rivigor?chat">Kornienko_rivigor</a> <br>
					Мы обязательно свяжемся с вами.
					
		</div>
	</div>

	</div>	
	<?php		
	$rules = ob_get_contents();
	ob_end_clean();  	
	echo $rules;
		die();
	}
	
	
	
	
	
	
	
	
	
	
	
}