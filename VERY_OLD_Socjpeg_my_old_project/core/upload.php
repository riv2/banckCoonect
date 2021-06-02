<?php 
if (!defined('APP')) {die();}
 if (isset($_FILES['file']) and $_FILES['file']['name'] != '') {

	     if ((($_FILES["file"]["type"] != "image/gif") && 
			($_FILES["file"]["type"] != "image/jpeg") &&
			($_FILES["file"]["type"] != "image/png") && 
			($_FILES["file"]["size"] > 10485760))) {			
                goBack();
            }
			
			 $sessionId = $session->getSession();
			 $loginStatus = $session->getLoginStatus();
			 
			 if ($loginStatus == 1) {
				 $nick = $sessionId;
			 } else {
				if (!isset($_POST['nick']) || $_POST['nick'] == '') {
					goBack();
				} else {
					$nick = trim($_POST['nick']);
				}
			 }
			 

				
			$nickDB = $db->query("SELECT `nick` FROM `users` WHERE `nick` = '$nick'");
			$rows = $nickDB->fetchAll();
			$count = count($rows);
			
			if ($count != 0 && $loginStatus == 0) {
				goBack('nick_busy');
			}
			 
			$session-> startSession($nick);
			
			$galleryDir = $_SERVER['DOCUMENT_ROOT'] . 'uploads/' . $nick;
			$indexFileName  = $galleryDir . '/index.php';

			if (!file_exists($indexFileName)) {
            mkdir($galleryDir, 0777, true);
            $indexFile = '<?php 
					$back = "http://".$_SERVER["SERVER_NAME"];
					header("Location: ".$back);
					exit;';
            $fp = fopen($indexFileName, "w");
            fwrite($fp, $indexFile);
            fclose($fp);
            }
			
			require_once($_SERVER['DOCUMENT_ROOT'].'/core/etc/SimpleImage.php');
			$imager = new SimpleImage($_FILES["file"]["tmp_name"]);
			$width      = $imager->get_width();
			$height     = $imager->get_height();
		    $banner_tmp = explode('.', $_FILES["file"]["name"]);
			$name       = $banner_tmp[0];
			$ext        = array_pop($banner_tmp);
			$ext        = trim ($ext);
			$sold = $hash = md5(rand(1,99999).rand(1,99999).rand(1,99999).$name);
			$banner_file_name_floder = $sold;
			$resolution = '('.$width . 'x' . $height .')';	
			$banner_file_name = $sold . '_'.$resolution.'_main'.'.'.$ext;
	
			
			
			$dirFile = $galleryDir . '/' . $banner_file_name_floder;
			$urlFile = $galleryDir . '/' . $banner_file_name_floder . '/' . $banner_file_name;
			$linkFile = 'https://' . $_SERVER['SERVER_NAME'] . '/uploads/'. $nick . '/' . $banner_file_name_floder . '/' . $banner_file_name;
			 if (!file_exists($urlFile)) {
				  mkdir($dirFile, 0777, true);				 
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $urlFile)) {
						$db->query("UPDATE `storage` SET `new` = 'N' WHERE `nick` = '$nick'");
						$db->query("INSERT INTO `storage` (`id`, `folder`, `original_name`, `name`, `url`, `link`, `ext`, `stamp`, `new`, `nick`, `resolution`, `hash`, `main`, `platform`, `city`, `region`, `country`, `ip`, `agent`) VALUES 
						(NULL, '$banner_file_name_floder', '".$_FILES['file']['name']."', '$banner_file_name', '$urlFile', '$linkFile', '$ext', CURRENT_TIMESTAMP, 'Y', '$nick', '$resolution', '$hash', 'Y', '".$aggregator['platform']."', '".$aggregator['city']."', '".$aggregator['region']."', '".$aggregator['country']."', '".$aggregator['ip']."', '".$aggregator['agent']."')");
					}
			 }
 } 
 
	$back = 'http://'.$_SERVER['SERVER_NAME'].'/croper/'.$hash;
	header("Location: ".$back);
	exit;
