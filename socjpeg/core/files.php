<?php if (!defined('APP')) {die();}

class Files {
	
	function __construct($db) {
		$this->db = $db;
	}
	
	function getFileBySession($session) {
		$file = $this->db->query("SELECT link FROM `storage` WHERE `nick` = '$session' AND `new` = 'Y'");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	  
		while($row = $file->fetch()) {  
		 $link = $row['link'];
		}
		
		if (isset($link)) {
			return $link;
		} else {
			return false;
		}
	}
	
	function getUrlBySession($session) {
		$file = $this->db->query("SELECT url FROM `storage` WHERE `nick` = '$session' AND `new` = 'Y'");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	  
		while($row = $file->fetch()) {  
		 $url = $row['url'];
		}
		
		if (isset($url)) {
			return $url;
		} else {
			return false;
		}
	}
	
	function getFilesBySession($session) {
		$file = $this->db->query("SELECT link FROM `storage` WHERE `nick` = '$session'");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	    $links = array();
		while($row = $file->fetch()) {  
		 $links[] = $row['link'];
		}
		
		if (empty($links)) {
			return false;
		} else {
			return $links;
		}
	}
	
	function getFileByName($name) {
		$file = $this->db->query("SELECT * FROM `storage` WHERE `name` = '$name'");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	    $files = array();
		while($row = $file->fetch()) {  
		 $files = $row;
		}
		
		if (empty($files)) {
			return false;
		} else {
			return $files;
		}
	}
	
	function getFilesByHash($hash) {
		$file = $this->db->query("SELECT * FROM `storage` WHERE `hash` = '$hash'");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	    $files = array();
		while($row = $file->fetch()) {  
		$files[] = $row;
		}		
		if (empty($files)) {
			return false;
		} else {
			return $files;
		}
	}
	
	function getMainFilesBySession($session) {
		$file = $this->db->query("SELECT * FROM `storage` WHERE `nick` = '$session' and `main` = 'Y' ORDER BY `stamp` DESC");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	    $files = array();
		while($row = $file->fetch()) {  
		$files[] = $row;
		}		
		if (empty($files)) {
			return false;
		} else {
			return $files;
		}
	}
	
	
	function getNickBySession($session) {
		$file = $this->db->query("SELECT nick FROM `storage` WHERE `nick` = '$session'");
		$file->setFetchMode(PDO::FETCH_ASSOC);  
	  
		while($row = $file->fetch()) {  
		 $nick = $row['nick'];
		}
		
		if (isset($nick)) {
			return $nick;
		} else {
			return $session;
		}
	}
	
	
	function addRarById($id, $rar) {
		$file = $this->db->query("UPDATE `storage` SET `rar` = '$rar' WHERE `id` = '$id'");
	}
	
	
	function file_force_download($file) {
	  if (file_exists($file)) {
		// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
		// если этого не сделать файл будет читаться в память полностью!
		if (ob_get_level()) {
		  ob_end_clean();
		}
		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		// читаем файл и отправляем его пользователю
		readfile($file);
		exit;
	  }
	}
	
	
	
	
}

