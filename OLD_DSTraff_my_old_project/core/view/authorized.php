<?php 
if (!AuthID or !AuthLogin) {
		header("Location: ".Site);
		exit;
} 
?>

<!DOCTYPE HTML>
<html lang="ru">
<head>
<?php echo openCss ('normalize');?>
<?php echo openCss ('authorized');?>
<?php echo openCss ('sweet');?>
<?php echo openCss ('tips');?>
<script language='JavaScript' src='<?php echo jQuery;?>' type='text/javascript'></script>
<script language='JavaScript' src='<?php echo initJs;?>' type='text/javascript'></script>
<script language='JavaScript' src='<?php echo tipsJs;?>' type='text/javascript'></script>
<script language='JavaScript' src='<?php echo sweetJs;?>' type='text/javascript'></script>
<!-- <script language='JavaScript' src='../../../public/js/snowfall.jquery.min.js' type='text/javascript'></script> -->
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="ru">
<link href="<?php echo Site;?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
</head>

<body>
<input type = 'hidden' id = 'Site' value = '<?php echo Site; ?>'/>
<input type = 'hidden' id = 'AuthTips' value = '<?php echo AuthTips; ?>'/>

<table class = "mainTable">
	<tr>
		<td class = "mainTableCenter">
	
			<?php 
			if (ADMIN == 'true') {
			$DB = new DB();
			$cSupport = $DB->query("SELECT count(id) FROM support WHERE status = 'Расмотрение'");	
			$cSupport = $cSupport[0]['count(id)'];
			$cDomains = $DB->query("SELECT count(id) FROM domains WHERE status <> 'Припаркован'");	
			$cDomains = $cDomains[0]['count(id)'];
			$cTrackers = $DB->query("SELECT count(id) FROM trackers WHERE moderated = 'N'");	
			$cTrackers = $cTrackers[0]['count(id)'];
			$smarty->assign('cDomains', $cDomains); 
			$smarty->assign('cSupport', $cSupport);
			$smarty->assign('cTrackers', $cTrackers);
			} 
			
			echo $smarty->display('header.tpl'); ?>

		</td>
		
	</tr>

	<tr>
		<td class = "mainTableContent">
		
			<?php   
            			if ($request['category'] == 'faq') {
						$smarty->display('user/faq.tpl');
						} else if ($request['category'] == 'profile') {
						$smarty->display('user/profile.tpl');
						} else if ($request['category'] == 'domains') {
						$smarty->display('user/domains.tpl');
						} else if ($request['category'] == 'news') {
						$smarty->display('user/news.tpl');
						} else if ($request['category'] == 'listtrackers'){
						$smarty->display('user/trackers/listtrackers.tpl');
						} else if ($request['category'] == 'newtracker'){
						$smarty->display('user/trackers/actionstracker.tpl');
						} else if ($request['category'] == 'edittracker'){
						$smarty->display('user/trackers/actionstracker.tpl');
						} else if ($request['category'] == 'support'){
						$smarty->display('user/support.tpl');
						} else if ($request['category'] == 'statistic'){
						$smarty->display('user/statistic.tpl');
						} else if ($request['category'] == 'top'){
						$smarty->display('user/top.tpl');
						} else if ($request['category'] == 'adminTraffic'){
						$smarty->display('user/adminTraffic.tpl');
						} else if ($request['category'] == 'adminFiltres'){
						$smarty->display('user/adminFiltres.tpl');
						} else if ($request['category'] == 'adminMail'){
						$smarty->display('user/adminMail.tpl');
						} else if ($request['category'] == 'adminNews'){
						$smarty->display('user/adminNews.tpl');
						} else if ($request['category'] == 'adminDomains'){
						$smarty->display('user/adminDomains.tpl');
						} else if ($request['category'] == 'adminUsers'){
						$smarty->display('user/adminUsers.tpl');
						} else if ($request['category'] == 'adminFaq'){
						$smarty->display('user/adminFaq.tpl');
						}  			
			
						else {
						$smarty->display('user/index.tpl');
						}  
				?>
	
		</td>
	</tr>
	
	<tr>
		<td class = "mainTableCenter">
			
			<?php echo $smarty->display('footer.tpl'); ?>
			
		</td>
	</tr>

</table>

<div class = "clear"></div>
<div class = 'loadingLayer' id = 'loading'> <div class = 'loading'><img src = "<?php echo Site?>/public/img/loading.gif" /></div> </div>
<div class = 'popup hide' id = 'popup'> <div class = 'popupClose' id = 'popupClose'>х</div> <div class = 'popupText' id = 'popupText'></div></div>



</body>
</html>