<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-16 16:29:04
         compiled from "/var/www/html/core/view/tpl/user/statistic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5782847857b31520a53d81-36821960%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '624cd39a79350ca46e91fb5c772232aa57d708bc' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/statistic.tpl',
      1 => 1456919311,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5782847857b31520a53d81-36821960',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
    'ADMIN' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b31520a88067_64471444',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b31520a88067_64471444')) {function content_57b31520a88067_64471444($_smarty_tpl) {?><?php echo '<script'; ?>
 type="text/javascript" src="https://www.google.com/jsapi"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/jquery.gvChart-0.1.min.js' type='text/javascript'><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/statistic.js' type='text/javascript'><?php echo '</script'; ?>
>
<link rel='stylesheet' type='text/css' href='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/css/calendar.css'>
<?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/calendar.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Анализ трафика</title>

<div class = "Conteiner">
	<h1> Анализ трафика</h1> <hr>
<table class = 'sTable'>

	<tr>
		<th>Выбирите поток</th>
		<th><?php if ($_smarty_tpl->tpl_vars['request']->value['rules_1']=='none') {
} else { ?>Правило<?php }?></th>
		<th>Показать 10 дней до:</th> 
	</tr>
	<tr>
	<form id = 'stat_form' action = '' method = 'POST'>
	   
	   <td class = 'sTrackers'> 
		<select id = 'trackers_1' name = 'trackers_1'> 
		<option value = ''>Все</option>
		<?php if ($_smarty_tpl->tpl_vars['request']->value['trackers']=='none') {
} else { ?>
		<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['trackers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
' <?php if ($_smarty_tpl->tpl_vars['request']->value['selected_tracker_1']=='none') {
} else { ?> <?php if ($_smarty_tpl->tpl_vars['request']->value['selected_tracker_1']==$_smarty_tpl->tpl_vars['item']->value['id']) {?>selected<?php }
}?>> <b>(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</b> <?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?> | ((<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
)<?php }?></option>
		<?php } ?> <?php }?>
		</select> 
	   </td>
	   
	   <td class = 'sRules'>
	   	<?php if ($_smarty_tpl->tpl_vars['request']->value['rules_1']=='none') {
} else { ?>
			<?php echo $_smarty_tpl->tpl_vars['request']->value['rules_1'];?>

		<?php }?>
	   </td>
	   
	   <td>	   
		   <input maxlength = "10"  id="calendar-field" size="10" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['toDay'];?>
" disabled /> <button id="calendar-trigger">...</button>
		   <input type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['toDay'];?>
" name = "goDate" id = "goDate"/> 
			<?php echo '<script'; ?>
>
			Calendar.setup({
				inputField : "calendar-field",
				trigger    : "calendar-trigger",
				onSelect   : function() { this.hide() },
				min: <?php echo $_smarty_tpl->tpl_vars['request']->value['cal_min'];?>
,
				max: <?php echo $_smarty_tpl->tpl_vars['request']->value['cal_max'];?>
,
				onBlur   : function(cal) {
						$('#loading').show();
						var date = this.selection.get();
						date = Calendar.intToDate(date);
						date = Calendar.printDate(date, "%Y-%m-%d");
						$('#dateReport').html('').html(date);
						$('#goDate').val(date);
						$('#stat_form').submit();
					}
			});
			<?php echo '</script'; ?>
>
	   </td>
 
	   </form>
	</tr>

	<tr>
		<td colspan = '5'><br>
		<div class = 'diagram bg_white'>
		<?php if ($_smarty_tpl->tpl_vars['request']->value['diagram_1']=='none') {
} else { ?>
			<?php echo $_smarty_tpl->tpl_vars['request']->value['diagram_1'];?>

		<?php }?>
		</div>
		</td>
	</tr>
	
	<tr>
		<td colspan = '5'><br>
		<div class = 'diagram'>
			<div style = 'padding:10px;'>
				<table class = 'prev_table'>
				<tr>
					<th colspan = '11'><center> Количество уникальных отправленных запросов за <?php echo $_smarty_tpl->tpl_vars['request']->value['goDate'];?>
 по потокам.</center></th>
				</tr>
				<tr>
					<th>Название потока</th> <th>Редиректы</th> <th>Системе</th> <th>Показы</th>  <th>Системе</th> <th>Клики</th>  <th>Системе</th> <th>Фреймы</th>  <th>Системе</th> <th>Переходы</th> <th>Системе</th>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['potoks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
				<tr>
				<td><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</td>
				<td class = 'bg_blue'><?php echo $_smarty_tpl->tpl_vars['item']->value['redirects'];?>
</td>
				<td class = 'bg_red'><?php echo $_smarty_tpl->tpl_vars['item']->value['redirects_dst'];?>
</td>
				<td class = 'bg_blue'><?php echo $_smarty_tpl->tpl_vars['item']->value['view'];?>
</td>
				<td class = 'bg_red'><?php echo $_smarty_tpl->tpl_vars['item']->value['view_dst'];?>
</td>
				<td class = 'bg_blue'><?php echo $_smarty_tpl->tpl_vars['item']->value['click'];?>
</td>
				<td class = 'bg_red'><?php echo $_smarty_tpl->tpl_vars['item']->value['click_dst'];?>
</td>
				<td class = 'bg_blue'><?php echo $_smarty_tpl->tpl_vars['item']->value['frame'];?>
</td>
				<td class = 'bg_red'><?php echo $_smarty_tpl->tpl_vars['item']->value['frame_dst'];?>
</td>
				<td class = 'bg_blue'><?php echo $_smarty_tpl->tpl_vars['item']->value['direct'];?>
</td>
				<td class = 'bg_red'><?php echo $_smarty_tpl->tpl_vars['item']->value['direct_dst'];?>
</td>
				</tr>
				<?php } ?>
				</table>
			</div>
		</div>
		</td>
	</tr>
	
</table>
	
</div>
































<?php }} ?>
