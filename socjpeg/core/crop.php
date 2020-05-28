<?php 
if (!defined('APP')) {die();}

$sessionId = $session->getSession();
if (!$sessionId) {
 goBack();
}
$file = new Files($db);
$loginStatus = $session->getLoginStatus();
$linkImg = $file->getFileBySession($sessionId);
$urlImg = $file->getUrlBySession($sessionId);
$nick = $sessionId;


if (!isset($_POST['hash']) && strlen($_POST['hash']) != 32) {
	goBack();
} else {
	$hash = $_POST['hash'];
}

if (!$linkImg) { goBack(); } 

if (isset($_POST['urlImg'])) {
	
if ($_POST['urlImg'] != $urlImg) {
	goBack();
}

if (isset($_POST['original']) && $_POST['original'] == 'Y') {
	$original = true;
} else {
	$original = false;
}

$socseti = array(
	'ok' => array (
		'one' => array('w' => '1680', 'h' => '1680', 'descr' => 'Картинка для поста <img src="https://socjpeg.ru/assets/img/ok_one.png" title="Одноклассники" alt="Одноклассники" />'), 
		'two' => array('w' => '1340', 'h' => '320', 'descr' => 'Обложка <img src="https://socjpeg.ru/assets/img/ok_two.png" title="Одноклассники" alt="Одноклассники" />'), 
		'three' => array('w' => '190', 'h' => '190', 'descr' => 'Фото профиля <img src="https://socjpeg.ru/assets/img/ok_three.png" title="Одноклассники" alt="Одноклассники" />')
	),
	'vk' => array (
		'one' => array('w' => '200', 'h' => '300', 'descr' => 'Аватар <img src="https://socjpeg.ru/assets/img/vk_one.png" title="Вконтакте" alt="Вконтакте" />'), 
		'two' => array('w' => '200', 'h' => '200', 'descr' => 'Миниатюра аватара <img src="https://socjpeg.ru/assets/img/vk_one.png" title="Вконтакте" alt="Вконтакте" />'), 
		'three' => array('w' => '510', 'h' => '271', 'descr' => 'Картинка баннера <img src="https://socjpeg.ru/assets/img/vk_three.png" title="Вконтакте" alt="Вконтакте" />')
	),
	'tw' => array (
		'one' => array('w' => '1024', 'h' => '512', 'descr' => 'Картинка для твита <img src="https://socjpeg.ru/assets/img/tw_one.png" title="Твиттер" alt="Твиттер" />'), 
		'two' => array('w' => '1500', 'h' => '500', 'descr' => 'Обложка <img src="https://socjpeg.ru/assets/img/tw_two.png" title="Твиттер" alt="Твиттер" />'), 
		'three' => array('w' => '400', 'h' => '400', 'descr' => 'Фото профиля <img src="https://socjpeg.ru/assets/img/tw_three.png" title="Твиттер" alt="Твиттер" />')
	),
	'fc' => array (
		'one' => array('w' => '1200', 'h' => '630', 'descr' => 'Картинка для поста <img src="https://socjpeg.ru/assets/img/fc_one.png" title="Facebook" alt="Facebook" />'), 
		'two' => array('w' => '851', 'h' => '315', 'descr' => 'Обложка <img src="https://socjpeg.ru/assets/img/fc_two.png" title="Facebook" alt="Facebook" />'), 
		'three' => array('w' => '180', 'h' => '180', 'descr' => 'Фото профиля <img src="https://socjpeg.ru/assets/img/fc_three.png" title="Facebook" alt="Facebook" />')
	),
	'you' => array (
		'one' => array('w' => '1280', 'h' => '720', 'descr' => 'Картинка поверх видео <img src="https://socjpeg.ru/assets/img/you_one.png" title="Youtube" alt="Youtube" />'), 
		'two' => array('w' => '2560', 'h' => '1440', 'descr' => 'Обложка канала <img src="https://socjpeg.ru/assets/img/you_two.png" title="Youtube" alt="Youtube" />'), 
		'three' => array('w' => '800', 'h' => '800', 'descr' => 'Фото профиля <img src="https://socjpeg.ru/assets/img/you_three.png" title="Youtube" alt="Youtube" />')
	),
	'inst' => array (
		'one' => array('w' => '1080', 'h' => '1080', 'descr' => 'Картинка для поста <img src="https://socjpeg.ru/assets/img/inst_one.png" title="Инстаграм" alt="Инстаграм" />'), 
		'two' => array('w' => '110', 'h' => '110', 'descr' => 'Фото профиля <img src="https://socjpeg.ru/assets/img/inst_two.png" title="Инстаграм" alt="Инстаграм" />')
	),
	'go' => array (
		'one' => array('w' => '2120', 'h' => '1192', 'descr' => 'Обложка <img src="https://socjpeg.ru/assets/img/go_one.png" title="Google+" alt="Google+" />'), 
		'two' => array('w' => '250', 'h' => '250', 'descr' => 'Фото профиля <img src="https://socjpeg.ru/assets/img/go_two.png" title="Google+" alt="Google+" />')
	)	
);


  $x1 = $_POST['x1'] ?? false;
  $y1 = $_POST['y1'] ?? false;
  $x2 = $_POST['x2'] ?? false;
  $y2 = $_POST['y2'] ?? false;
  $w  = $_POST['w'] ?? false;
  $h  = $_POST['h'] ?? false;
  
  if ($x1 == false) {
	  $original = true;
  }
  
  $sw = $_POST['sw'] ?? '';
  $sh = $_POST['sh'] ?? '';
  
  $urlImgNew = explode('/',$urlImg);
  $name = array_pop($urlImgNew);
  $ext = explode('.', $name);
  $banner_file_name_floder = $hash;
  $ext = array_pop($ext);
  $ext = trim($ext);
  $urlImgNew = implode('/',$urlImgNew);

  require_once($_SERVER['DOCUMENT_ROOT'].'/core/etc/SimpleImage.php');
  
	$imager = new SimpleImage($urlImg);
	
	if ($original == false) {
	$imager->crop($x1, $y1, $x2, $y2);
	}	
	
	$new_w     = (int)$imager->get_width();
	$new_h     = (int)$imager->get_height();
	$resolution = '('.$new_w . 'x' . $new_h.')';
	$banner_file_name = md5(rand(0,9999).rand(0,9999).rand(0,9999).$name).'_'.$resolution.'_tumb'.'.'.$ext;
    $linkFile = 'https://' . $_SERVER['SERVER_NAME'] . '/uploads/'. $sessionId . '/' . $banner_file_name_floder . '/' . $banner_file_name;
    $urlFile = $urlImgNew . '/' . $banner_file_name;
	
	
	$imager->save($urlFile);
	$originalurlFile = $urlFile;

	$db->query("INSERT INTO `storage` (`id`, `folder`,                  `original_name`, `name`,              `url`,      `link`,      `ext`, `stamp`,           `new`, `nick`,  `resolution`,  `hash`,  `main`, `type`) VALUES 
		                              (NULL, '$hash', '',              '$banner_file_name', '$urlFile', '$linkFile', '$ext', CURRENT_TIMESTAMP, 'N',  '$nick', '$resolution', '$hash', 'N',    'tumb')");
	
	unset($imager);

	if ($sw != '' || $sh != '') {
		if ($sw > 4000) $sw = $new_w; 
		if ($sh > 4000) $sh = $new_h; 

		$imager = new SimpleImage($originalurlFile);
		
		if ($sw == '') {

			$imager->fit_to_height($sh);
		} elseif ($sh == '') {

			$imager->fit_to_width($sw);
		} else {

			$imager->resize($sw, $sh);
		}
		$new_w     = (int)$imager->get_width();
		$new_h     = (int)$imager->get_height();
		$resolution = '('.$new_w . 'x' . $new_h.')';
		$banner_file_name = md5(rand(0,9999).rand(0,9999).rand(0,9999).$name).'_'.$resolution.'_origin'.'.'.$ext;
		$linkFile = 'https://' . $_SERVER['SERVER_NAME'] . '/uploads/'. $sessionId . '/' . $banner_file_name_floder . '/' . $banner_file_name;
		$urlFile = $urlImgNew . '/' . $banner_file_name;
		$imager->save($urlFile);
		$db->query("INSERT INTO `storage` (`id`, `folder`,                   `original_name`,   `name`,              `url`,      `link`,      `ext`, `stamp`,           `new`, `nick`,  `resolution`,  `hash`,  `main`, `type`) VALUES 
										  (NULL, '$hash',  '',                '$banner_file_name', '$urlFile', '$linkFile', '$ext', CURRENT_TIMESTAMP, 'N',  '$nick', '$resolution', '$hash', 'N',    'origin')");
		unset($imager);
	}

	
	foreach ($socseti as $key => $values) {
		foreach ($values as $value) {
			$imager = new SimpleImage($originalurlFile);
			$imager->best_fit($value['w'], $value['h']);
			$new_w     = (int)$imager->get_width();
			$new_h     = (int)$imager->get_height();
			$resolution = '('.$new_w . 'x' . $new_h.')';
			$descr = $value['descr'];
			$banner_file_name = md5(rand(0,9999).rand(0,9999).rand(0,9999).$name).'_'.$resolution.'_'.$key.'.'.$ext;
			$linkFile = 'https://' . $_SERVER['SERVER_NAME'] . '/uploads/'. $sessionId . '/' . $banner_file_name_floder . '/' . $banner_file_name;
			$urlFile = $urlImgNew . '/' . $banner_file_name;
			$imager->save($urlFile);
			$db->query("INSERT INTO `storage` (`id`, `folder`, `original_name`,   `name`, `url`, `link`, `ext`, `stamp`, `new`, `nick`,  `resolution`,  `hash`,  `main`, `type`, `descr`) VALUES 
											  (NULL, '$hash', '', '$banner_file_name', '$urlFile', '$linkFile', '$ext', CURRENT_TIMESTAMP, 'N',  '$nick', '$resolution', '$hash', 'N', '$key', '$descr')");
			unset($imager);
		}		
	}
  
	$back = 'http://'.$_SERVER['SERVER_NAME'].'/view/'.$hash;
	header("Location: ".$back);
	exit;

} else {
	
	goBack();
}
