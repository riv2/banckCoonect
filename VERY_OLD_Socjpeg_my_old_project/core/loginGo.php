<?php 
if (!defined('APP')) {die();}

	if (isset($_POST['nick']) || $_POST['nick'] != '') {
	$nick = trim($_POST['nick']);
	} else {
		goBack();
	}
	
	if (isset($_POST['pass']) || $_POST['pass'] != '') {
	$pass = trim($_POST['pass']);
	} else {
		goBack();
	}
	
	$nickDB = $db->query("SELECT `nick` FROM `users` WHERE `nick` = '$nick'");
	$rows = $nickDB->fetchAll();
	$count = count($rows);

	if ($count == 0) {
		$db->query("INSERT INTO `users` (`id`, `nick`, `pass`) VALUES 
					            	    (NULL, '$nick', '$pass')");
		$session->setSession($nick);
		goBack('login_created');
	} else {
		$nickDB = $db->query("SELECT `nick` FROM `users` WHERE `nick` = '$nick' AND `pass` = '$pass'");
		$rows = $nickDB->fetchAll();
		$count = count($rows);
		if ($count != 0) {
			$session->setSession($nick);
			goBack('sussess_login');
		}
		$session->delSession();
		goBack('error_login', 'login');
	}
	
