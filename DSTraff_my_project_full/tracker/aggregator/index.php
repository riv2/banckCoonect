<?php
//header('Cache-Control: no-cache, must-revalidate');
//header('Content-type: application/json');
$aggregator = array();
include ("SxGeo.php"); 
include ("mobile.php");

$geo = find($_SERVER['REMOTE_ADDR']);

$mobile_detect = new Mobile_Detect;
 
 if ($mobile_detect->isMobile() == true or $mobile_detect->isTablet() == true) {
 $aggregator['platform'] = 'wap';
 } else {
 $aggregator['platform'] = 'web';
 }
unset ($mobile_detect);
 
$aggregator['city'] = $geo['city']['name_ru'];
$aggregator['region'] = $geo['region']['name_ru'];
$aggregator['country'] = $geo['country']['name_ru'];
$aggregator['countryiso'] = $geo['country']['iso'];
$aggregator['ip'] = $_SERVER['REMOTE_ADDR'];
$aggregator['agent'] = '';
$aggregator['browser'] = '';
$aggregator['version'] = '';
$aggregator['operating_system'] = '';
$aggregator['is_robot'] = '';
$aggregator['robot'] = '';
$aggregator['is_mobile'] = '';
$aggregator['mobile'] = '';
$aggregator['language'] = '';
$list_admin_ips = array ();
$list_admin_country = array();
$aggregator['list_admin_ips'] = $list_admin_ips;
$aggregator['list_admin_country'] = $list_admin_country;

if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {$HTTP_ACCEPT_LANGUAGE = $_SERVER["HTTP_ACCEPT_LANGUAGE"];} else {$HTTP_ACCEPT_LANGUAGE = 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4';}
  preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', strtolower($HTTP_ACCEPT_LANGUAGE), $matches); // вычисляем соответствия с массивом $matches
  $langs = array_combine($matches[1], $matches[2]); // Создаём массив с ключами $matches[1] и значениями $matches[2]
  if (is_array($langs)) {
  foreach ($langs as $n => $v)
  $langs[$n] = $v ? $v : 1; // Если нет q, то ставим значение 1
  arsort($langs); // Сортируем по убыванию q
  $langs = key($langs); // Выводим язык по умолчанию
  if (isset ($langs[0]) and isset ($langs[1])) {
	$lang = $langs[0].$langs[1];
  } else {
	$lang = 'unknown';
  }
  $aggregator['language'] = $lang; } else {
	$aggregator['language'] = 'unknown';  
  }


$aggregator['OS'] = getOS();
$aggregator['browser'] = user_browser();


if (isset($_SERVER['HTTP_REFERER'])) {
	$aggregator['referer'] = $_SERVER['HTTP_REFERER'];
} else {
	$aggregator['referer'] = 'Direct';
}

function find($ip)
{
$SxGeo = new SxGeo('aggregator/SxGeo.dat', SXGEO_BATCH | SXGEO_MEMORY);
$result = $SxGeo->getCityFull($ip); 
unset ($SxGeo);
return $result;
}

function user_browser() {
	if (isset($_SERVER['HTTP_USER_AGENT'])) {$agent = $_SERVER['HTTP_USER_AGENT'];} else {$agent = 'Chrome';}
	preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info);
	if (empty($browser_info)) return 'IE';
	list(,$browser,$version) = $browser_info;
	if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera';
	if (preg_match("/OPR/i", $agent, $opera)) return 'Opera';
	if ($browser == 'MSIE') {
		preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie);
		if ($ie) return 'IE';
		return 'IE';
	}
	if ($browser == 'Firefox') {
		preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff);
		if ($ff) return $ff[1];
	}
	if ($browser == 'Opera' && $version == '9.80') return 'Opera';
	if ($browser == 'Version') return 'Safari';
	if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko';
	return $browser;
}



function getOS() {

if (isset ($_SERVER['HTTP_USER_AGENT'])) {$userAgent = $_SERVER['HTTP_USER_AGENT'];} else {$userAgent = '(Windows NT 6.3)|(Windows 8)';}
 $oses = array (
        // Mircrosoft Windows Operating Systems
'Windows' => '(Win16)',
'Windows' => '(Windows 95)|(Win95)|(Windows_95)',
'Windows' => '(Windows 98)|(Win98)',
'Windows Server' => '(Windows NT 5.0)|(Windows 2000)',
'Windows Server' => '(Windows NT 5.01)',
'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
'Windows Server' => '(Windows NT 5.2)',
'Windows' => '(Windows NT 6.0)|(Windows Vista)',
'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
'Windows 8' => '(Windows NT 6.3)|(Windows 8)',
'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
'Windows' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
'Windows Mobile' => '(Windows CE)',
// UNIX Like Operating Systems
'Mac OS' => '(Mac OS X beta)',
'Mac OS' => '(Mac OS X 10.0)',
'Mac OS' => '(Mac OS X 10.1)',
'Mac OS' => '(Mac OS X 10.2)',
'Mac OS' => '(Mac OS X 10.3)',
'Mac OS' => '(Mac OS X 10.4)',
'Mac OS' => '(Mac OS X 10.5)',
'Mac OS' => '(Mac OS X 10.6)',
'Mac OS' => '(Mac OS X 10.7)',
'Mac OS' => '(Mac OS X)',
'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
'Open BSD' => '(OpenBSD)',
'SunOS' => '(SunOS)',
'Solaris' => '(Solaris\/11)|(Solaris11)',
'Solaris' => '((Solaris\/10)|(Solaris10))',
'Solaris' => '((Solaris\/9)|(Solaris9))',
'CentOS' => '(CentOS)',
'QNX' => '(QNX)',
// Kernels
'UNIX' => '(UNIX)',
// Linux Operating Systems
'Ubuntu' => '(Ubuntu\/12.10)|(Ubuntu 12.10)',
'Ubuntu' => '(Ubuntu\/12.04)|(Ubuntu 12.04)',
'Ubuntu' => '(Ubuntu\/11.10)|(Ubuntu 11.10)',
'Ubuntu' => '(Ubuntu\/11.04)|(Ubuntu 11.04)',
'Ubuntu' => '(Ubuntu\/10.10)|(Ubuntu 10.10)',
'Ubuntu' => '(Ubuntu\/10.04)|(Ubuntu 10.04)',
'Ubuntu' => '(Ubuntu\/9.10)|(Ubuntu 9.10)',
'Ubuntu' => '(Ubuntu\/9.04)|(Ubuntu 9.04)',
'Ubuntu' => '(Ubuntu\/8.10)|(Ubuntu 8.10)',
'Ubuntu' => '(Ubuntu\/8.04)|(Ubuntu 8.04)',
'Ubuntu' => '(Ubuntu\/6.06)|(Ubuntu 6.06)',
'Red Hat' => '(Red Hat)',
'Red Hat' => '(Red Hat Enterprise)',
'Fedora' => '(Fedora\/17)|(Fedora 17)',
'Fedora' => '(Fedora\/16)|(Fedora 16)',
'Fedora' => '(Fedora\/15)|(Fedora 15)',
'Fedora' => '(Fedora\/14)|(Fedora 14)',
'Chromium' => '(ChromiumOS)',
'Google' => '(ChromeOS)',
// BSD Operating Systems
'OpenBSD' => '(OpenBSD)',
'FreeBSD' => '(FreeBSD)',
'NetBSD' => '(NetBSD)',
// Mobile Devices
'Android 4' => '(Android 4)',
'Android 5' => '(Android 5)',
'Android' => '(Android)',
'iPod' => '(iPod)',
'iPhone' => '(iPhone)',
'iPad' => '(iPad)',
//DEC Operating Systems
'OS/8' => '(OS\/8)|(OS8)',
'Older DEC OS' => '(DEC)|(RSTS)|(RSTS\/E)',
'WPS-8' => '(WPS-8)|(WPS8)',
// BeOS Like Operating Systems
'BeOS' => '(BeOS)|(BeOS r5)',
'BeIA' => '(BeIA)',
// Kernel
'Linux' => '(Linux)|(X11)',
// OS/2 Operating Systems
'OS/2' => '(OS\/220)|(OS\/2 2.0)',
'OS/2' => '(OS\/2)|(OS2)',
// Search engines
'Search' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves\/Teoma)|(ia_archiver)'
    );
 
    foreach($oses as $os=>$pattern){
        if(preg_match("/$pattern/i", "/$userAgent/")) { 
            return $os;
        }
    }
    return 'Unknown'; 
}







?>
