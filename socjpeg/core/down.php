<?php if (!defined('APP')) {die();}

$sessionId = $session->getSession();
if (!$sessionId) {
 goBack();
}

if (!isset($args[1])) {
	goBack();
} else {
	$file = $args[1];
}

$Files = new Files($db);

if (strlen($file) == 32) {
$files = $Files->getFilesByHash($file);

if ($files[0]['rar'] == null) {
		require_once("core/etc/Tar.php"); 
		$back_name = $files[0]['folder'].'_'.date('Y-m-d_h-i-s').'.rar';
		$url = $files[0]['url'];
		$name = $files[0]['name'];
		$URL = str_replace($name, '', $url);
		$rarName = '/home/apapche/www/socjpeg/uploads/rars/'.$back_name;
		$tar_object = new Archive_Tar($rarName); 
		$tar_object->setErrorHandling(PEAR_ERROR_PRINT); 
		$var[0]=$URL; 
		$tar_object->create($var);
		$Files->addRarById($files[0]['id'], $rarName);
		
		$Files->file_force_download($rarName);
		goBack();


	} else {
		$Files->file_force_download($files[0]['rar']);
		goBack();	
}



} else {
	$file = $Files->getFileByName($file);
	$Files->file_force_download($file['url']);
	goBack();
}