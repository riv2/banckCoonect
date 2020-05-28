<?php 
if (!defined('APP')) {die();}

function goBack($hash = '', $url = '') {
	$back = 'http://'.$_SERVER['SERVER_NAME'].'/'.$url.'#'.$hash;
	header("Location: ".$back);
	exit;
}

function getTypeSoc($type) {
	switch ($type) {
		case 'main':
			return 'Исходное изображение <img src="https://socjpeg.ru/assets/img/socjpeg.jpg" title="SocJPEG" alt="SocJPEG" />';
			break;
		case 'tumb':
			return 'Обрезаное изображение <img src="https://socjpeg.ru/assets/img/socjpeg.jpg" title="SocJPEG" alt="SocJPEG" />';
			break;
		case 'origin':
			return 'Свой размер <img src="https://socjpeg.ru/assets/img/socjpeg.jpg" title="SocJPEG" alt="SocJPEG" />';
			break;
		case 'vk':
			return 'Вконтакте <img src="https://socjpeg.ru/assets/img/vk.png" title="Вконтакте" alt="Вконтакте" />';
			break;
		case 'ok':
			return 'Одноклассники <img src="https://socjpeg.ru/assets/img/ok.png" title="Одноклассники" alt="Одноклассники" />';
			break;
		case 'tw':
			return 'Твитер <img src="https://socjpeg.ru/assets/img/tw.png" title="Твиттер" alt="Твиттер" />';
			break;
		case 'inst':
			return 'Инстаграмм <img src="https://socjpeg.ru/assets/img/inst.png" title="Инстаграм" alt="Инстаграм" />';
			break;
		case 'you':
			return 'Youtube <img src="https://socjpeg.ru/assets/img/you.png" title="Youtube" alt="Youtube" />';
			break;
		case 'fc':
			return 'Facebook <img src="https://socjpeg.ru/assets/img/fc.png" title="Facebook" alt="Facebook" />';
			break;
		case 'go':
			return 'Google+ <img src="https://socjpeg.ru/assets/img/go.png" title="Google+" alt="Google+" />';
			break;
		}
}