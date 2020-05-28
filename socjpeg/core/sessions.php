<?php 
if (!defined('APP')) {die();}

class Session {
	
	function __construct() {
	}
	
	public function startSession($nick) {
		if (!isset($_COOKIE['session'])) {
			setcookie("session", $nick, time() + 3600, '/', 'socjpeg.ru', FALSE, TRUE);
			setcookie("login", '0', time() + 3600, '/', 'socjpeg.ru', FALSE, TRUE);
		}
	}
	
	public function setSession($nick) {
			setcookie("session", $nick, time() + 3600, '/', 'socjpeg.ru', FALSE, TRUE);
			setcookie("login", '1', time() + 3600, '/', 'socjpeg.ru', FALSE, TRUE);
	}
	
	public function getLoginStatus() {
		if (isset($_COOKIE['login'])) {
			return $_COOKIE['login'];
		} else {
			return 0;
		}
	}	
	
	public function getSession() {
		if (isset($_COOKIE['session'])) {
			return $_COOKIE['session'];
		} else {
			return '';
		}
	}
	
	public function delSession() {
		 if (isset($_COOKIE['session'])) {
            setcookie('session', '', time() - 3600, '/', 'socjpeg.ru', FALSE, TRUE);
			setcookie('login', '', time() - 3600, '/', 'socjpeg.ru', FALSE, TRUE);
         }
	}

	
}



