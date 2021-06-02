<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-16 16:27:18
         compiled from "/var/www/html/core/view/tpl/user/adminTraffic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:150778191157b314b65c8d18-13089969%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '20eba0e22fea1cea3357fa490d57d7c1662d3f6f' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/adminTraffic.tpl',
      1 => 1471001920,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '150778191157b314b65c8d18-13089969',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
    'traffic' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b314b665bec9_80881124',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b314b665bec9_80881124')) {function content_57b314b665bec9_80881124($_smarty_tpl) {?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/admintraffic.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Администрирование - Трафик</title>

<div class = "Conteiner">

<h1> Администрирование - Трафик</h1> <hr>
 
<table class = "table" >

<tr>
<th> № </th>
<th> Тип </th>
<th> Название </th>
<th width = '50px;'> Nный </th>
<th> Тематика </th>
<th> Трекер </th>
<th> Настройка трафика </th>
<th class = 'smallAT'> Redirect </th>
<th class = 'smallAT'> Frame </th>
<th class = 'smallAT'> Banner </th>
<th class = 'smallAT'> Adspot </th>
<th class = 'smallAT'> Catfish</th>
<th class = 'smallAT'> Popunder</th>
<th class = 'smallAT'> Richmedia</th>
<th class = 'smallAT'> Topline</th>
<th class = 'smallAT'> VKMessage</th>
<th class = 'smallAT'> ClickUnder</th>
<th class = 'smallAT'> Fullscreen</th>
<th class = 'smallAT'> Fullvideo</th>
<th> Действие </th>
</tr>

<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminTraffic/digest/' method = 'POST'>
<tr>
<td width = '20px;'>0</td>
<td width = '50px;'> 
<select name = 'typeTraffic'>
<option value = 'wap'>WAP</option>
<option value = 'web'>WEB</option>
</select>
</td>
<td width = '150px;'> <input maxlength = '30' style = 'width:150px;' type = 'text' name = 'name' /></td>
<td width = '50px;'> <input maxlength = '10' style = 'width:50px;' type = 'text' name = 'clicks' /></td>
<td width = '100px;'>     	
	<select name = "theme" style = 'width: 100px;'>
	<option value = 'Весь' > Весь трафик </option>
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['theme']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
' > <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
	<?php } ?>
	</select> 
</td>
<td width = '100px;'> 
	<select name = "trackerId" style = 'width: 100px;'>
	<option value = ''>Все</option>
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['trackers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
' > (<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
)<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
	<?php } ?>
	</select>
</td>
<td width = '150px;'> <center>Доступны после создания правила</center> </td> 
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td width = '150px;'> <center> <input style = 'width:100px;'  type = 'submit' value = 'Создать' name = 'action'>  </center></td>
</tr>
</form>


   <tr>
		<td style = 'background-color:rgba(0,0,0,0.5);' colspan="22"></td>
   </tr>

<?php if (($_smarty_tpl->tpl_vars['request']->value['adminTraffic']!='none')) {?> 
<?php  $_smarty_tpl->tpl_vars['traffic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['traffic']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['request']->value['adminTraffic']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['traffic']->key => $_smarty_tpl->tpl_vars['traffic']->value) {
$_smarty_tpl->tpl_vars['traffic']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['traffic']->key;
?>
 
<form id = 'form_<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminTraffic/digest/' method = 'POST'>
<input type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' name = 'id'>

<tr class = '<?php if ($_smarty_tpl->tpl_vars['traffic']->value['typeTraffic']=='web') {?>blueTr<?php } else { ?>redTr<?php }?>'>
<td> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
 </td>
<td width = '50px;'> 
<select name = 'typeTraffic'>
<option value = 'web' <?php if ($_smarty_tpl->tpl_vars['traffic']->value['typeTraffic']=='web') {?>selected<?php }?>>WEB</option> 
<option value = 'wap' <?php if ($_smarty_tpl->tpl_vars['traffic']->value['typeTraffic']=='wap') {?>selected<?php }?>>WAP</option> 
</select>
</td>
<td>  <input maxlength = '30' style = 'width:150px;' type = 'text' name = 'name' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['name'];?>
' /></td>
<td > <input maxlength = '10' style = 'width:50px;' type = 'text' name = 'clicks' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['clicks'];?>
' /></td>
<td> 
<select name = "theme"  style = 'width: 100px;'>
<option value = 'Весь' > Весь трафик </option>
<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['theme']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
' <?php if ($_smarty_tpl->tpl_vars['traffic']->value['theme']==$_smarty_tpl->tpl_vars['item']->value['name']) {?> selected <?php }?>> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
<?php } ?>
</select> 
</td>

<td> 
	<select name = "trackerId"  style = 'width: 100px;'>
	<option value = ''>Все</option>
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['trackers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
' <?php if (($_smarty_tpl->tpl_vars['traffic']->value['trackerId']==$_smarty_tpl->tpl_vars['item']->value['id'])) {?> selected <?php }?>> (<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
)<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 </option>
	<?php } ?>
	</select> 
</td>

<td> <div class = 'traffic center' onclick = "showAll('<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');"> Показать/Скрыть Все настройки</div> </td>

<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapRedirect'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webRedirect'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapFrame'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webFrame'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapBanner'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webBanner'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapAdspot'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webAdspot'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapCatfish'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webCatfish'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapPopunder'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webPopunder'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapRichmedia'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webRichmedia'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapTopline'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webTopline'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapVKMessage'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webVKMessage'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapClickUnder'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webClickUnder'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapFullscreen'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webFullscreen'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td class = 'center'><span class = 'smallAT'><?php echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['wapFullvideo'])===null||$tmp==='' ? '' : $tmp);
echo (($tmp = @$_smarty_tpl->tpl_vars['traffic']->value['webFullvideo'])===null||$tmp==='' ? '' : $tmp);?>
</span></td>
<td>  <center> <input type = 'submit' style = 'width:100px;' value = 'Ред.' name = 'action'> <input onclick = "
		swal({
		title: 'Удалить правило?',
		text: 'Правило будет удалено',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Да, удалить'
		},
		function(){
		$('#del_<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').attr('name','action');
		$('#del_<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').attr('value','Удалить');
		$('#form_<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').submit();
		});  																				" type = 'button' style = 'width:100px;' value = 'Удалить' name = 'action'>
<input type = 'hidden' id = 'del_<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' >
 </center> </td>

</tr>

<tr id = 'tr_<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' class='hide'>
	<td  colspan="22" style = "background-color:rgba(255,255,255,0.9);">

		<table><tr>
		<td>
		<b> Banner URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Banner' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Banner'];?>
' />  </div><br>
		<div id = 'typeBanner<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:20px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
				
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Banner']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Banner'];?>
 <?php }?>"/>
		<object svg = 'Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Banner']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Banner'];?>
 <?php }?>'></object>
		<input banner_web = 'Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Banner' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Banner'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Banner'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Banner_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
			<td>
		<b> Adspot URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Adspot' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Adspot'];?>
' />  </div><br>
		<div id = 'typeAdspot<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Adspot']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addAdspot.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Adspot'];?>
 <?php }?>"/>
		<object svg = 'Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Adspot']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Adspot'];?>
 <?php }?>'></object>
		<input banner_web = 'Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Adspot' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Adspot'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Adspot'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Adspot_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
				<td>
		<b> Catfish URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Catfish' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Catfish'];?>
' />  </div><br>
		<div id = 'typeCatfish<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Catfish']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Catfish'];?>
 <?php }?>"/>
		<object svg = 'Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Catfish']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Catfish'];?>
 <?php }?>'></object>
		<input banner_web = 'Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Catfish' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Catfish'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Catfish'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Catfish_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
	
				<td>
		<b> Popunder URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Popunder' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Popunder'];?>
' />  </div><br>
		<div id = 'typePopunder<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Popunder']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Popunder'];?>
 <?php }?>"/>
		<object svg = 'Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Popunder']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Popunder'];?>
 <?php }?>'></object>
		<input banner_web = 'Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Popunder' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Popunder'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Popunder'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Popunder_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
				<td>
		<b> Richmedia URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Richmedia' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Richmedia'];?>
' />  </div><br>
		<div id = 'typeRichmedia<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Richmedia']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Richmedia'];?>
 <?php }?>"/>
		<object svg = 'Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Richmedia']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Richmedia'];?>
 <?php }?>'></object>
		<input banner_web = 'Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Richmedia' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Richmedia'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Richmedia'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Richmedia_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
				<td>
		<b> Topline URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Topline' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Topline'];?>
' />  </div><br>
		<div id = 'typeTopline<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Topline']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Topline'];?>
 <?php }?>"/>
		<object svg = 'Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Topline']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Topline'];?>
 <?php }?>'></object>
		<input banner_web = 'Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Topline' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Topline'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Topline'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Topline_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
		<td>
		<b> VKMessage URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_VKMessage' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_VKMessage'];?>
' />  </div><br>
		<div id = 'typeVKMessage<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_VKMessage']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_VKMessage'];?>
 <?php }?>"/>
		<object svg = 'VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_VKMessage']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_VKMessage'];?>
 <?php }?>'></object>
		<input banner_web = 'VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_VKMessage' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_VKMessage'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_VKMessage'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#VKMessage_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
		<td>
		<b> Fullscreen URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Fullscreen' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Fullscreen'];?>
' />  </div><br>
		<div id = 'typeFullscreen<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' src = "<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Fullscreen']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Fullscreen'];?>
 <?php }?>"/>
		<object svg = 'Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='<?php if ($_smarty_tpl->tpl_vars['traffic']->value['img_Fullscreen']=='') {?> <?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/img/addBanner.jpg <?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Fullscreen'];?>
 <?php }?>'></object>
		<input banner_web = 'Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
' type = 'hidden' name = 'img_Fullscreen' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Fullscreen'];?>
' /></div>
		<?php echo '<script'; ?>
> var img_src = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Fullscreen'];?>
'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
]').show(); $('#Fullscreen_img<?php echo $_smarty_tpl->tpl_vars['traffic']->value['id'];?>
').hide(); } <?php echo '</script'; ?>
>
		</div>
		</td>
		
						<td>
		<b> Fullvideo URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Fullvideo' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Fullvideo'];?>
' />  </div><br>
		<b> ID Youtube: </b><br>
		<input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);' type = 'text' name = 'img_Fullvideo' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['img_Fullvideo'];?>
' maxlength = '15'/></div>
		</td>
		
				<td>
		<b> ClickUnder URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_ClickUnder' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_ClickUnder'];?>
' />  </div><br>
		</td>
		
				<td>
		<b> Redirect URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Redirect' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Redirect'];?>
' />  </div><br>
		</td>
		
				<td>
		<b> Frame URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Frame' value = '<?php echo $_smarty_tpl->tpl_vars['traffic']->value['url_Frame'];?>
' />  </div><br>
		</td>
				<td>
		<b> Convert Format: </b><br>
		<div>
		<select name = 'convertFormat' style = 'width:100%;'>
		<option value = 'N' <?php if ($_smarty_tpl->tpl_vars['traffic']->value['convertFormat']=='N') {?>selected<?php }?>>Нет</option>
		<option value = 'Y' <?php if ($_smarty_tpl->tpl_vars['traffic']->value['convertFormat']=='Y') {?>selected<?php }?>>Да</option>
		</select>
		</div><br>
		<b> IP Filter White: </b><br>
		<div>
		<textarea name = 'ipFilterWhite' style = 'height:90px;width:200px;font-size:10px;'><?php echo $_smarty_tpl->tpl_vars['traffic']->value['ipFilterWhite'];?>
</textarea>
		</div>
		</td>
		
		</tr></table>
			
		
	</td>
</tr>

</form>	
 <tr>
		<td  colspan="22" style = "background-color:rgba(0,0,0,0.2);"></td>
 </tr>
<?php } ?>
<?php }?>
</table>


<div id = 'galleryDiv' class = 'galleryDiv'>
	     <div class = 'galleryDivClose' id = 'galleryDivClose'></div> 
		<div id = "galleryDivContent" class = "galleryDivContent">
	 <iframe id = "iframeGallery" src="" width="100%" height="90%" align="center" frameborder="no"></iframe>
		</div>	
</div>	




</div> <?php }} ?>
