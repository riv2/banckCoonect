<?php
if (!defined('APP')) {die();}

$type = 'access';

    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Yandex'))        {        $type = 'bot';    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Google'))        {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'bingbot'))       {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'MJ12bot'))       {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'SputnikBot'))    {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'FeedlyBot'))     {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'curl'))          {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Accoona'))       {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'ia_archiver'))   {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Jeeves'))        {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'EltaIndexer'))   {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'baidu'))         {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'crawler'))       {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator')) {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Wget'))          {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'WebAlta'))       {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Yahoo'))         {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Rambler'))       {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Ask'))           {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Turtle'))        {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Nigma'))         {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Robot'))         {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'proximic'))      {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'bot'))           {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'mail'))          {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'spider'))        {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Bond'))          {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Linux; U; Android 3.1; en-us; GT-P7510 Build/HMJ37) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13')) {        $type = 'bot';    }
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.9.2b5) Gecko/20091204 Firefox/3.6b5')) {        $type = 'bot';    }
	
	$hash_day = md5(date('d').$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].$aggregator['platform'].$aggregator['city'].$aggregator['country'].$aggregator['browser'].$aggregator['language'].$aggregator['OS']);
	$hash = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].$aggregator['platform'].$aggregator['city'].$aggregator['country'].$aggregator['browser'].$aggregator['language'].$aggregator['OS']);
	$sessionId = $session->getSession();

	$db->query("INSERT INTO `stat` (`id`, `domain`,	`request`, `client`, `ip`, `type`, `time`, `memory`, `platform`, `city`, `region`, `country`, `countryiso`, `browser`, `language`, `OS`, `referer`, `stamp`, `hash_day`, `hash`, `session`) 
	VALUES (NULL,'".$_SERVER['SERVER_NAME']."','".$_SERVER['REQUEST_URI']."','".$_SERVER['HTTP_USER_AGENT']."','".$_SERVER['REMOTE_ADDR']."','".$type."','".$Time."','".$Memory."','".$aggregator['platform']."',
	'".$aggregator['city']."','".$aggregator['region']."','".$aggregator['country']."','".$aggregator['countryiso']."','".$aggregator['browser']."','".$aggregator['language']."','".$aggregator['OS']."',
	'".$aggregator['referer']."',CURRENT_TIMESTAMP, '".$hash_day."', '".$hash."', '".$sessionId."');");
