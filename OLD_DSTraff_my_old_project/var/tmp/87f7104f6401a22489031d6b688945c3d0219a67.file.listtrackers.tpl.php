<?php /* Smarty version Smarty-3.1.21-dev, created on 2017-05-12 13:49:12
         compiled from "/var/www/html/core/view/tpl/user/trackers/listtrackers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:85003538957b31516c28741-55826415%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87f7104f6401a22489031d6b688945c3d0219a67' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/trackers/listtrackers.tpl',
      1 => 1494585916,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85003538957b31516c28741-55826415',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b31516c6a148_57915275',
  'variables' => 
  array (
    'request' => 0,
    'ADMIN' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b31516c6a148_57915275')) {function content_57b31516c6a148_57915275($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/html/core/smarty/plugins/function.counter.php';
?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/listtrackers.js' type='text/javascript'><?php echo '</script'; ?>
>

<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Список потоков</title>

<div class = "Conteiner">

	<h1> Мои Потоки</h1> <hr>
	
	<table class = "table">
	
	<colgroup>
	<col style="width: 1%">
	<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><col style="width: 1%"><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><col style="width: 1%"><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><col style="width: 1%"><?php }?>
	<col style="width: 15%">
	<col style="width: 1%">
	<col style="width: 8%">
	<col style="width: 40%">
	<col style="width: 8%">
	<col style="width: 10%">
	<col style="width: 1%">
	</colgroup>
	
    <tr class = "tableHeader">
        <th>№</th>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><th>Владелец</th><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><th>Типы трафика</th><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><th>Тематика</th><?php }?>
        <th>Назавание потока</th>
		<th>Запросы</th>
		<th>Модерация</th>
		<th>Коментарии Администратора</th>
        <th>Дата создания</th>
		<th>Код потока</th>
        <th>Действия</th>
    </tr>
   
   <?php if ($_smarty_tpl->tpl_vars['request']->value['trackers']=='none') {?> 
   <tr>
		<td class = "tableNone" colspan="10"><center>Потоков нет</center></td>
   </tr>
   
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['trackers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	   <tr>
			<td class = 'center'><?php echo smarty_function_counter(array(),$_smarty_tpl);?>
</td>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><td>(<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
)<?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
</td><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><td><?php echo $_smarty_tpl->tpl_vars['item']->value['trafficTypes'];?>
</td><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><td><?php echo $_smarty_tpl->tpl_vars['item']->value['theme'];?>
</td><?php }?>
			<td>(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['item']->value['stat'];?>
</td>
			<td><?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?>
			<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/trackers/moderate/' method = 'POST'>
			<input type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
' name = 'id'>
			<input type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
' name = 'uid'>
			<select name = 'moderated' style = "margin-top:8px;">
			<option style = 'color:red;' value = 'N' <?php if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='N') {?>selected <?php }?>>На Модерации</option>
			<option style = 'color:orange;' value = 'R' <?php if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='R') {?>selected <?php }?>>Исправить</option>
			<option style = 'color:green;' value = 'Y' <?php if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='Y') {?>selected <?php }?>>Проверен</option>
			</select>
			<?php } else { ?>
			<center><?php if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='N') {?><span style = 'color:red;'>На Модерации</span><?php }
if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='R') {?><span style = 'color:orange;'>Исправить</span><?php }
if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='Y') {?><span style = 'color:green;'>Проверен</span><?php }?> <?php }?>	</center>
			</td>
			<td>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?> 
			<input type = 'text' style = "width:350px;" name = 'comment' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['comment'];?>
'>
			<input type = 'submit' value = 'Отправить' /> 
			</form>
			<?php } else { ?>
			<?php echo $_smarty_tpl->tpl_vars['item']->value['comment'];
}?></td>
			<td class = 'center'><div class = 'w105'><?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
</div></td>
			<td><?php if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='Y'||$_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><div onclick= "showCode(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
)" class = "showCode">Получить коды</div><?php }?></td>
			<td class = "tEdit">
			<div class = 'w90'>
			<a href = 'edit/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/'><div class = "tPreviewBotton"></div></a>
			<div class = "tDelBotton" onclick= "DelSure(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
)"></div>
			</div>
            </td>
       </tr>
	   <?php if ($_smarty_tpl->tpl_vars['item']->value['moderated']=='Y'||$_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?>
	    <tr class = "trackerCode" id = "trackerCode<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
">
			<td colspan = "10">
			<div class = "trackerCodeItem">
			<span class = 'codeHeader'>&uarr; Код потока тип 1: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>&lt;script async src = &quot;<?php echo $_smarty_tpl->tpl_vars['item']->value['http'];
echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
&quot;&gt;&lt;/script&gt;</span><br>
			<br><span class = 'codeHeader'>&uarr; Код потока тип 2: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style = "padding-left:181px;"><span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>
			
			&nbsp;&lt;script async type="text/javascript"&gt;<br>
			&nbsp;&nbsp;&nbsp;&nbsp;   (function(w) {<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	 var script = document.createElement('script');<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	 var i = setInterval(function() {<br>
			&nbsp;&nbsp;&nbsp;&nbsp;	   if(typeof w.document.body !== 'undefined') {<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		   script.src = '<?php echo $_smarty_tpl->tpl_vars['item']->value['http'];
echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
' + '/<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
';<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	   w.document.getElementById('<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
').appendChild(script);<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		   clearInterval(i);<br>
			&nbsp;&nbsp;&nbsp;&nbsp;	   }<br>
			&nbsp;&nbsp;&nbsp;	 }, 100);<br>
			&nbsp;&nbsp;})(window);<br>
			&nbsp;&lt;/script&gt;
			&nbsp;&lt;div id = '<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
'&gt;&lt;/div&gt;
			
			</span></div> <br>
			<span class = 'codeHeader'>&uarr; Код потока тип 3: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style = "padding-left:181px;"><span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>
			
			&lt;label name = '<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
'/&gt; &lt;script async type='text/javascript'&gt;<br>
			&nbsp;function addCodeScript() { var codeScript, elScript; <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;codeScript = document.createElement('script'); <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;elScript = document.getElementsByName('<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
'); <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;codeScript.src = '<?php echo $_smarty_tpl->tpl_vars['item']->value['http'];
echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
'; <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;elScript[0].appendChild(codeScript); <br>
			} setTimeout(addCodeScript, 125); &lt;/script&gt;

			</span></div> <br>

			<span class = 'codeHeader'>&uarr; Прямая ссылка: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'><?php echo $_smarty_tpl->tpl_vars['item']->value['http'];
echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
/direct/</span>
			<br><br>
			<span class = 'codeHeader'>&uarr; Предпоказ ссылка: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'><?php echo $_smarty_tpl->tpl_vars['item']->value['http'];
echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
/preview/<?php echo $_smarty_tpl->tpl_vars['item']->value['trackerHash'];?>
/</span>
			
			<?php echo '<script'; ?>
>
			<?php echo '</script'; ?>
>
			</div>
			</td>
       </tr>
	   <?php }?>
   <?php } ?>
   <?php }?>

 
	</table>
 
    <div class = "ButtonA"><a href = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/trackers/new/">Добавить поток</a></div>



</div>

<div id = 'previewDiv' class = 'previewDiv'>
	     <div class = 'previewDivClose' id = 'previewDivClose'></div> 
				<div id = "previewContent" class = "previewContent">
				</div>	
</div>	
<?php }} ?>
