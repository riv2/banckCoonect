<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-17 11:30:30
         compiled from "/var/www/html/core/view/tpl/user/trackers/actionstracker.tpl" */ ?>
<?php /*%%SmartyHeaderCode:36601492257b420a6501e85-01873575%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '141126702a2be4b5c51125e7d59bcc57d1e446e9' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/trackers/actionstracker.tpl',
      1 => 1469803580,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '36601492257b420a6501e85-01873575',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
    'k' => 0,
    'rule' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b420a65b06d9_18018193',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b420a65b06d9_18018193')) {function content_57b420a65b06d9_18018193($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/var/www/html/core/smarty/plugins/modifier.replace.php';
?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/newtracker.js' type='text/javascript'><?php echo '</script'; ?>
>
<?php if ($_smarty_tpl->tpl_vars['request']->value['action']=='edit') {?>
<?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/edittracker.js' type='text/javascript'><?php echo '</script'; ?>
>
<?php }?>
<input id = 'Site' type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
'>
<title>DSTraff - <?php if ($_smarty_tpl->tpl_vars['request']->value['action']=='edit') {?> Просмотр (редактирование) потока <?php } else { ?> Добавить новый поток <?php }?></title>


<div class = 'Conteiner'>

<div class = 'notice'>
<label style = 'font-size:14px;'>Памятка</label>
<hr>
Рекомендуемый размер рекламного изображения для данных форматов:<br><br>
<label>Rich-media</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>AdSpot</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>TopLine</label><br>
640x480&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;300x250&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;730х90<br><br>
<label>PopUnder</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>CatFish </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>Fullscreen</label><br>
468х75&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;730х90&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1024x600(WEB)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;360x640(WAP)<br><br>

</div>

	<h1><?php if ($_smarty_tpl->tpl_vars['request']->value['action']=='edit') {?> Просмотр (редактирование) потока <?php } else { ?> Добавить новый поток <?php }?></h1> <hr>
<form id = 'newTrackerForm' enctype='multipart/form-data' action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/trackers/digest/' method = 'POST'>
<input id = 'trackerId' type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['id'];?>
' name = 'tracker[trackerId]'>
	<table>
	<tr>
	<td valign="top" style = "width: 284px;position:relative;" >
	<div style = 'width:275px;'></div> 
	<div id = 'primaryConteiner' class = 'primaryConteiner'> 
	<label id = 'tipName'> Введите наименование потока: </label>
	<div class = 'filed'> <input maxlength = '100' type = 'text' name = "tracker[name]" autocomplete='off' value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['name'];?>
" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?> disabled <?php }?>/></div>
	<label id = 'tipTheme'> Выберите тематику трафика: </label>
		<div class = 'filed'> 
    	<select name = "tracker[theme]" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?> disabled <?php }?> >
		<option value = ''></option>
		<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['theme']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
' <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['theme']==$_smarty_tpl->tpl_vars['item']->value['name']) {?> selected <?php }?> > <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
		<?php } ?>
		</select> 
		</div>
		
		<label id = 'tipThemeURL'> Укажите источники трафика, через запятую: </label>
		<div class = 'filed'> <input maxlength = '200' type = 'text' name = "tracker[themeURL]"  placeholder='http://' value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['themeURL'];?>
" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?> disabled <?php }?> /></div>
		
		<label id = 'tipDomain' > Выберите домен в коде потока: </label>
		<div class = 'filed'> 
    	<select name = "tracker[domain]" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?> disabled <?php }?>>
		<option value = ''></option>
		<?php if ($_smarty_tpl->tpl_vars['request']->value['domains']=='none') {?> <?php } else { ?>
		<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['domains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
' <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['domain']==$_smarty_tpl->tpl_vars['item']->value['domain']) {?> selected <?php }?> > <?php echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
 </option>
		<?php } ?> <?php }?>
		</select> 
		<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?> <input type = 'hidden' name = 'tracker[domain]' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['domain'];?>
'> <?php }?>
		</div>
		
		<br>
		<div id = 'tipPreview' class = 'filed'> <input id = 'preview' type = 'button' value="Предосмотр" /></div>
		<div id = 'tipTrackerSend' class = 'filed'> <input id = 'newTrackerSend' type = 'button' value="<?php if ($_smarty_tpl->tpl_vars['request']->value['action']=='edit') {?> Редактировать поток <?php } else { ?> Создать поток <?php }?>" /></div>
		
	 </div>
	 	</td>
		<td valign="top">
			<div id = 'advansedConteiner' class = 'advansedConteiner'> 
			
			<div class = 'advansedSwitcher' id = 'advansedSwitcher'>
			<div class = 'addBanner' id = 'addBanner'>+</div>
			<div id = 'tipTraffickBack' class = 'ItemBanner switcherActive traffickBack' banner = '0' onclick = 'switchBanner(0);' >&rarr;Traffic Back<span class = 'traficPercent'></span></div>
			

			<?php  $_smarty_tpl->tpl_vars['rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rule']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rule']->key => $_smarty_tpl->tpl_vars['rule']->value) {
$_smarty_tpl->tpl_vars['rule']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['rule']->key;
?>
			<?php if ($_smarty_tpl->tpl_vars['k']->value!='_0') {?> 
			
			<div class = 'ItemBanner' banner = '<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['k']->value,'_','');?>
' onclick = 'switchBanner("<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['k']->value,'_','');?>
");' >&nbsp;&nbsp;&nbsp;<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['k']->value,'_','');?>
&rarr;&nbsp;&nbsp;&nbsp;
			<?php if ($_smarty_tpl->tpl_vars['k']->value!='_1') {?> <div class = 'removeBanner' onclick = 'removeBanner("<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['k']->value,'_','');?>
");'>x</div> <?php }?>
			<div class = 'ruleName' id = 'ruleName<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>First rule</div>
			</div>
			<?php }?> 
			<?php } ?>
			
			</div>
			<div class = 'clear'></div>
			<div id = 'ConteinerBannerMain'>

			<div class = 'ConteinerBanner' id = 'ConteinerBanner_0'>
			<div style = 'width:710px;padding:20px;'>
				<div class = 'textCenter'>
				<h2>&darr; Настройка Traffic Back &darr;</h2>
				</div> 
		
			<div class = 'firstColl'>
				
				<br>
				<br>

				<center><span style = 'color:blue;' id = 'tipWEB' >WEB трафик</span></center>
				<br>
				
				<div class = 'filedFilter'>
				<center><label id = 'tipType' > &darr; Тип Traffic Back: &darr; </label>	</center>			
				    	<select name = 'rule[_0][trafficBackType_1]' onchange="trafficRuleType(this,'banner_rule_0_1','redirect_rule_0_1');">
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['formats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
						<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
						<?php } ?>
						</select> 
				
				</div>
				
				<br>
				
				<label id = 'tipURLTraffic' > URL Traffic Back: </label>
				<div class = 'filedUrl'> <input id = '' maxlength = '200' type = 'text' name = 'rule[_0][trafficBackURL_1]'  placeholder='http://' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackURL_1'];?>
' /></div>
				<div id = 'typeBannerStyle_1' class = 'filedUrl <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?>hide<?php }?>'>
				<label id = 'tipBannerStyle'> Стиль баннера: </label>
				<div class = 'filed'>
				<label>Ширина: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['bannerWidth_1'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerWidth_1]' onchange = "redirectNumber(this);" />
				<label>Высота: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['bannerHeight_1'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerHeight_1]' onchange = "redirectNumber(this);" />
				</div>
				</div>
			
				<div id = 'banner_rule_0_1'><div id = 'typeBanners_0_1'>
  		        <label id = 'tipBanner' > Баннер Traffic Back: </label><br>
				
				<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
				<div style = 'width:150px;;height:150px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('banner_web_img_0_1');" ></div>
				<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('banner_web_img_0_1');" ></div>
				
				<img width = "150px;"  style = "max-height:300px;" id = 'banner_web_img_0_1' src = "<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_1']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_1'];?>
 <?php }?>"/>
				<object svg = 'banner_web_img_0_1' style = 'width:150px;;height:150px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_1']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_1'];?>
 <?php }?>'></object>

				<input banner_web = 'banner_web_img_0_1' type = 'hidden' name = 'rule[_0][trafficBackBanner_1]' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_1'];?>
'/></div>
				
				<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_1'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = banner_web_img_0_1]').show(); $('#banner_web_img_0_1').hide(); } <?php echo '</script'; ?>
>
				</div></div>
				
				<div id = 'typeFullvideo_0_1'>
				<label> ID видео с Youtube: </label><br>
				<input type = 'text' name = 'rule[_0][trafficBackVideo_1]' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackVideo_1'])===null||$tmp==='' ? '' : $tmp);?>
' maxlength = '15'/><br><br>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']=='') {?> <?php echo '<script'; ?>
> $('#typeFullvideo_0_1').hide(); <?php echo '</script'; ?>
> <?php }?>
				
				<div class = 'clear'></div><br>
				<label id = 'tipUnTracker'> Отключить поток: </label>
				<input type="radio" name="tracker[unTracker]" value="N" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['unTracker']=='N') {?>checked<?php }?>> Нет
			    <input type="radio" name="tracker[unTracker]" value="Y" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['unTracker']=='Y') {?>checked<?php }?>> Да
				<br><br>
				<label id = 'tipUnPreview'> Отключить предпоказ: </label>
				<input type="radio" name="tracker[unPreview]" value="N" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['unPreview']=='N') {?>checked<?php }?>> Нет
			    <input type="radio" name="tracker[unPreview]" value="Y" <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['unPreview']=='Y') {?>checked<?php }?>> Да
			</div>
			
			<div class = 'secondColl' style = "width:290px;">
					<br>
				<br>

				<center><span style = 'color:red;' id = 'tipWAP'>WAP трафик</span></center>
				<br>
				
				<div class = 'filedFilter'>
				<center><label id = 'tipType' > &darr; Тип Traffic Back: &darr; </label>	</center>			
				    	<select name = 'rule[_0][trafficBackType_2]' onchange="trafficRuleType(this,'banner_rule_0_2','redirect_rule_0_2');">
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['formats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
						<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
						<?php } ?>
						</select> 
				</div>
				
				<br>
				
				<label id = 'tipURLTraffic' > URL Traffic Back: </label>
				<div class = 'filedUrl'> <input id = '' maxlength = '200' type = 'text' name = 'rule[_0][trafficBackURL_2]'  placeholder='http://' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackURL_2'];?>
' /></div>
				<div id = 'typeBannerStyle_2' class = 'filedUrl <?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?>hide<?php }?>'>
				<label id = 'tipBannerStyle'> Стиль баннера: </label>
				<div class = 'filed'>
				<label>Ширина: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['bannerWidth_2'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerWidth_2]' onchange = "redirectNumber(this);" />
				<label>Высота: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['bannerHeight_2'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerHeight_2]' onchange = "redirectNumber(this);" />
				</div>
				</div>
				<div id = 'banner_rule_0_2'><div id = 'typeBanners_0_2'>
  		        <label id = 'tipBanner' > Баннер Traffic Back: </label><br>
				
				<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
				<div style = 'width:150px;height:150px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('banner_web_img_0_2');" ></div>
				<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('banner_web_img_0_2');" ></div>
				
				<img width = "150px;"  style = "max-height:300px;" id = 'banner_web_img_0_2' src = "<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_2']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_2'];?>
 <?php }?>"/>
				<object svg = 'banner_web_img_0_2' style = 'width:150px;;height:150px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_2']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_2'];?>
 <?php }?>'></object>

				<input banner_web = 'banner_web_img_0_2' type = 'hidden' name = 'rule[_0][trafficBackBanner_2]' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_2'];?>
'/></div>
				
				<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackBanner_2'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = banner_web_img_0_2]').show(); $('#banner_web_img_0_2').hide(); } <?php echo '</script'; ?>
>
				</div></div>
				
				<div id = 'typeFullvideo_0_2'>
				<label> ID видео с Youtube: </label><br>
				<input type = 'text' name = 'rule[_0][trafficBackVideo_2]' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackVideo_2'])===null||$tmp==='' ? '' : $tmp);?>
' maxlength = '15'/><br><br>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']=='') {?> <?php echo '<script'; ?>
> $('#typeFullvideo_0_2').hide(); <?php echo '</script'; ?>
> <?php }?>
				
				<div class = 'clear'></div>
				
				<div class = 'filedRedirectSec' style = 'float:left;'><label id = 'tipBannersTimer'>Один показ формата каждые: </label><input value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['bannersTimer'];?>
' id = '' maxlength = '6' type = 'text' name = 'rule[_0][bannersTimer]' onchange = "redirectNumber(this);" /><label> секунд (кроме форматов Banner,Redirect,Frame)</label></div>
					
			</div>	
			</div>	
			

		
			
			
	</div>
	<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?><?php echo '<script'; ?>
> $('[name = "rule[_0][trafficBackType_2]"]').val('<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackType_2'];?>
').change(); <?php echo '</script'; ?>
><?php }?>	
	<?php if ($_smarty_tpl->tpl_vars['request']->value['tracker']['id']!='') {?><?php echo '<script'; ?>
> $('[name = "rule[_0][trafficBackType_1]"]').val('<?php echo $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']['_0']['trafficBackType_1'];?>
').change(); <?php echo '</script'; ?>
><?php }?>

<?php  $_smarty_tpl->tpl_vars['rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rule']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['request']->value['tracker']['rule']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rule']->key => $_smarty_tpl->tpl_vars['rule']->value) {
$_smarty_tpl->tpl_vars['rule']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['rule']->key;
?>
<?php if ($_smarty_tpl->tpl_vars['k']->value!='_0') {?> 
		
<div class = 'ConteinerBanner hide' id = 'ConteinerBanner<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
			
			<div style = 'width:710px;padding:20px;'>
				<div>
				<h2> &darr; Настройка правила №<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
 &darr; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
				<span id = 'tipNameRule'>Название:</span><input style = 'color:black;' size = '9' maxlength = '10' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][name]' value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['name'];?>
' onkeypress = "ruleName(this.value,'<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" onchange = "ruleName(this.value,'<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');"/></h3>
				<div id = 'tipFlush' class = 'flush' onclick = 'flush ("<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
");'>&bull; Очистить &bull;</div>
				</div> 
			<?php echo '<script'; ?>
>$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][name]"]').val('<?php echo $_smarty_tpl->tpl_vars['rule']->value['name'];?>
').change(); <?php echo '</script'; ?>
>
			<div class = 'firstColl'>
				
				<br>
				<div class = 'filedFilter'>
				<center><label id = 'tipTypeTrafic'> &darr; Выберите тип трафика &darr; </label>	</center>			
				    	<select name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][trafficType]" onchange="trafficType(this,'traffictype<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" >
						<option value = 'web' style = 'color:blue;'>WEB трафик</option>
						<option value = 'wap' style = 'color:red;'>WAP трафик</option>
						</select> 
				</div>	
				
				
				<br>
				<div class = 'filedFilter'>
				<center><label id = 'tipType'> &darr; Выберите формат передачи трафика &darr; </label>	</center>			
				    	<select name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][trafficTypeView]' onchange="trafficRuleType(this,'banner_rule<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
','redirect_rule<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');">
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['formats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
						<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
						<?php } ?>
						</select> 
				</div>
	
				<br>
				
				<label id = 'tipURLTraffic'> URL отдачи трафика: </label>
				<div class = 'filedUrl'> <input id = '' maxlength = '200' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][trafficURL]'  placeholder='http://' value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['trafficURL'];?>
'/></div>
			
			     			   <span id = "blank<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" style = 'float:right;'><label id = 'tipBlank'>Новая вкладка </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['blank']=='on') {?>checked<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][blank]'/></span>
				<div id = 'banner_rule<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
				
				<div class = 'filedRedirectSec'><label id = 'tipLimitView'>Укажите лимит показов: </label><input value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['limitView'];?>
' id = 'limitView<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' maxlength = '4' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][limitView]' onchange = "redirectNumber(this);limit(this,'limitClick<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" /></div>
				<div class = 'filedRedirectSec'><label id = 'tipLimitClicks'>Укажите лимит кликов: </label><input value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['limitClick'];?>
' id = 'limitClick<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' maxlength = '4' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][limitClick]' onchange = "redirectNumber(this);limit(this,'limitView<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" /></div>
					<div id = 'typeVK<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' class = 'hide filedUrl'>
					<label id = 'tipVkTitle'> Заголовок сообщения VK: </label>
					<div class = 'filed'> <input maxlength = '15' type = 'text' name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][vkTitle]" value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['vkTitle'];?>
'/></div>
					<label id = 'tipVkMsg'> Текст сообщения VK: </label>
					<div class = 'filed'> <input maxlength = '30' type = 'text' name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][vkMsg]" value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['vkMsg'];?>
'/></div>
					</div>
					
					<div id = 'typeBanner<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' class = 'hide filedUrl'>
					<label id = 'tipBannerStyle'> Стиль баннера: </label>
						<div class = 'filed'>
						<br>
						<label>Ширина: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['rule']->value['bannerWidth'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '4' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][bannerWidth]' onchange = "redirectNumber(this);" />
						<label>Высота: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['rule']->value['bannerHeight'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '4' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][bannerHeight]' onchange = "redirectNumber(this);" />
						</div>
					</div>
				<div id = 'typeBanners<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
  		        <label id = 'tipBanner'> Баннер: </label><br>
				<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
				<div style = 'width:150px;height:150px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" ></div>
				<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" ></div>
				
				<img width = "150px;"  style = "max-height:300px;" height = "150px;" id = 'banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' src = "<?php if ($_smarty_tpl->tpl_vars['rule']->value['banner']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['rule']->value['banner'];?>
 <?php }?>"/>
				<object svg = 'banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' style = 'width:150px;;height:150px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['rule']->value['banner']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['rule']->value['banner'];?>
 <?php }?>'></object>
				<input banner_web = 'banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' type = 'hidden' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][banner]' value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['banner'];?>
' /></div>
				
				<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['banner'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]').show(); $('#banner_web_img<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
').hide(); } <?php echo '</script'; ?>
>
				</div>
				<div class = 'filedFilterSelect'></div>
				</div>
				
				<div id = 'typeFullvideo<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
				<label> ID видео с Youtube: </label><br>
				<input type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][video]' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['rule']->value['video'])===null||$tmp==='' ? '' : $tmp);?>
' maxlength = '15'/>
				</div>
				<div id = 'typeFullparams<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'>
				<div class = 'clear'></div><br>
				<label>Закрыть через: </label><input style = 'width:50px;' value = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['rule']->value['videoTimer'])===null||$tmp==='' ? '' : $tmp);?>
' id = '' maxlength = '6' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][videoTimer]' onchange = "redirectNumber(this);" /> <label>сек.</label><br><br>
				<label>Принудительный переход </label><input type = 'checkbox' <?php if ((($tmp = @$_smarty_tpl->tpl_vars['rule']->value['videoGo'])===null||$tmp==='' ? '' : $tmp)=='on') {?>checked<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][videoGo]'/>
				</div>
				
				<div id = 'redirect_rule<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' class = 'redirect_rule'>
				<div class = 'filedRedirectSec'><label id = 'tipRedirectTimer'>Сработать через: </label><input value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['redirectTimer'];?>
' id = '' maxlength = '6' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][redirectTimer]' onchange = "redirectNumber(this);" /><label> Милисекунд</label></div>
				<div class = 'filedRedirectSec'><label id = 'tipRedirectLimitView'>Укажите лимит показов: </label><input value = '<?php echo $_smarty_tpl->tpl_vars['rule']->value['redirectLimitView'];?>
' id = 'redirectLimitView<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' maxlength = '6' type = 'text' name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][redirectLimitView]' onchange = "redirectNumber(this);" /></div>

				</div>
			</div>
			
			<div class = 'secondColl'>
			<br> 
			<table> <tr> <td>
			<div class = 'filedFilterSelect'>
				<label id = 'tipСountry'> Страна: </label><br>				
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][country][]' onchange = "referenceCity(this,'referenceCity<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
');" multiple=true >
								<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['country']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>

									<?php if ($_smarty_tpl->tpl_vars['item']->value['name']=='') {
} else { ?>
									<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
									<?php }?>
									
								<?php } ?>
						</select> 
						
				<?php if (count($_smarty_tpl->tpl_vars['rule']->value['filter']['country'])<2) {?>
					<?php echo '<script'; ?>
>$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][country][]"]').val('<?php echo $_smarty_tpl->tpl_vars['rule']->value['filter']['country'][0];?>
').change(); <?php echo '</script'; ?>
>
				<?php } else { ?>	
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rule']->value['filter']['country']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
					<?php echo '<script'; ?>
>$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][country][]"] [value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"]').attr("selected", "selected"); <?php echo '</script'; ?>
>
				<?php } ?>
				   <?php echo '<script'; ?>
>$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][country][]"]').change(); <?php echo '</script'; ?>
>
				<?php }?>
				
				</div>
			  </td><td>
				<div class = 'filedFilterSelect'>		
				<label id = 'tipCity'> Город: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][city][]' id = 'referenceCity<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' multiple=true >
						</select> 
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rule']->value['filter']['city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
					<?php echo '<script'; ?>
>setTimeout(function() { $('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][city][]"] [value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"]').attr("selected", "selected");},900); <?php echo '</script'; ?>
>
				<?php } ?>			
				</div>
				 </td></tr>
				 <tr><td>
				
				<div class = 'filedFilterSelect'>	
				<label id = 'tipOS'> ОС: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][OS][]' selecter = 'traffictype<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' multiple=true >
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['OS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
								<option style = 'display:none;' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['data'];?>
' traffictype<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
 = '<?php echo $_smarty_tpl->tpl_vars['item']->value['type'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
						<?php } ?>
						</select>
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rule']->value['filter']['OS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
					<?php echo '<script'; ?>
>setTimeout(function() { $('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][OS][]"] [value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"]').attr("selected", "selected");},300); <?php echo '</script'; ?>
>
				<?php } ?>							
				</div>
				</td><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipBrowser'> Браузер: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][browser][]' selecter = 'traffictype<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' multiple=true >
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['browser']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
								<option style = 'display:none;' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['data'];?>
' traffictype<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
 = '<?php echo $_smarty_tpl->tpl_vars['item']->value['type'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
						<?php } ?>
						</select> 
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rule']->value['filter']['browser']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
					<?php echo '<script'; ?>
>setTimeout(function() { $('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][browser][]"] [value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"]').attr("selected", "selected");},300); <?php echo '</script'; ?>
>
				<?php } ?>
				</div>	
				</td></tr>
				<tr><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipLanguage'> Язык: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][language][]' multiple=true >
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['language']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
								<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['data'];?>
'> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
						<?php } ?>
						</select>
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rule']->value['filter']['language']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
					<?php echo '<script'; ?>
>$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][filter][language][]"] [value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"]').attr("selected", "selected"); <?php echo '</script'; ?>
>
				<?php } ?>		
				</div>	
				 </td><td>
				
				<div class = 'filedFilterSelect'>
				<label id = 'tipReferals'> URL`s переходов: </label><br>					
				    	<textarea name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][referals]'><?php echo $_smarty_tpl->tpl_vars['rule']->value['referals'];?>
</textarea>
				</div>	
				</td></tr>

			  <tr><td>
			  
				<div class = 'filedFilterSelect'>
				<label id = 'tipIpfiterWhite'> Белый IP список: </label><br>					
				    	<textarea name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][ipfiterWhite]'><?php echo $_smarty_tpl->tpl_vars['rule']->value['ipfiterWhite'];?>
</textarea>
				</div>	
				</td><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipIpfiterBlack'> Черный IP список: </label><br>					
				    	<textarea name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][ipfiterBlack]'><?php echo $_smarty_tpl->tpl_vars['rule']->value['ipfiterBlack'];?>
</textarea>
				</div>
				</td></tr>
			    <tr><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipTime'> Время работы (МСК): </label><br>
				<textarea name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][time]'><?php echo $_smarty_tpl->tpl_vars['rule']->value['time'];?>
</textarea>
			  </div>
				</td><td>
				
		
			  <div class = 'filedFilterSelect'>
				<label id = 'tipDay'> Дни работы: </label><br>
					<label>Пн </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day1']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day1]'/>	
					<label>Вт </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day2']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day2]'/>	
					<label>Ср </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day3']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day3]'/>	
					<label>Чт </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day4']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day4]'/>	
					<label>Пт </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day5']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day5]'/><br>	
					<label>Сб </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day6']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day6]'/>	
					<label>Вс </label><input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['day0']=='on') {?>checked="checked"<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][day0]'/>
				
				<br>	
				    
					<!-- <input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['unreferal']=='on') {?>checked<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][unreferal]'/>	<label>Убрать referer</label> <br> -->
					<input type = 'checkbox' <?php if ($_smarty_tpl->tpl_vars['rule']->value['unrule']=='on') {?>checked<?php }?> name = 'rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][unrule]'/> <label id = 'tipUnrule'>Отключить Правило</label>
					

   			  </div>
				</td></tr>
				</table>
			</div>
			</div>	
			
			
</div>		

	
				<?php echo '<script'; ?>
>
				$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][trafficType]"]').val('<?php echo $_smarty_tpl->tpl_vars['rule']->value['trafficType'];?>
').change();
				$('[name = "rule[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
][trafficTypeView]"]').val('<?php echo $_smarty_tpl->tpl_vars['rule']->value['trafficTypeView'];?>
').change();
				<?php echo '</script'; ?>
>
				
	
<?php }?>
<?php } ?>	


			

</div>
		</td>
		</tr>
		</table>
</form>
	 
	 
	 	 
	 
	 
<div id = 'galleryDiv' class = 'galleryDiv'>
	     <div class = 'galleryDivClose' id = 'galleryDivClose'></div> 
		<div id = "galleryDivContent" class = "galleryDivContent">
	 <iframe id = "iframeGallery" src="" width="100%" height="90%" align="center" frameborder="no"></iframe>
		</div>	
</div>	 
	 
	 

	
<div class = 'ButtonA'><a href = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/trackers/'>Список потоков</a></div>	 
		 
</div>
	 





<?php }} ?>
