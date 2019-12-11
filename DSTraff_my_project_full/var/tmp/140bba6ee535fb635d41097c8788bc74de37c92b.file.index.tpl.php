<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-03-06 16:39:35
         compiled from "/var/www/tds/core/view/tpl/user/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7388005575a423eae4ec987-15615624%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '140bba6ee535fb635d41097c8788bc74de37c92b' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/index.tpl',
      1 => 1520343506,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7388005575a423eae4ec987-15615624',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a423eae4f4bd4_48894337',
  'variables' => 
  array (
    'request' => 0,
    'ADMIN' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a423eae4f4bd4_48894337')) {function content_5a423eae4f4bd4_48894337($_smarty_tpl) {?><?php echo '<script'; ?>
 type="text/javascript" src="https://www.google.com/jsapi"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/jquery.gvChart-0.1.min.js' type='text/javascript'><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/index.js' type='text/javascript'><?php echo '</script'; ?>
>

<input id = 'Site' type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
'>
<title>TDSTraff - Главная</title>

<div class = 'Conteiner'>

	<h1> Главная</h1> <hr>

	<table style = 'display:none;' id = 'indexTable'>
	<tr>
	<td>
			<div class = 'diagram'>
			<?php echo $_smarty_tpl->tpl_vars['request']->value['statistic'];?>

			</div>
	</td>
	<td style = 'padding:38px 0px 0px 0px; vertical-align:top;line-height: 1.7;'>
			<div class = 'diagram_all'>
		<?php echo $_smarty_tpl->tpl_vars['request']->value['statistic_all'];?>

			</div>
	<td>
	</tr>
	<tr>
	<td><br>
			<div class = 'diagram'>	
			<?php echo $_smarty_tpl->tpl_vars['request']->value['admin_stat'];?>

			</div>
	</td>
	<td style = 'padding:53px 0px 0px 0px; vertical-align:top;line-height: 1.7;'>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><div class = 'diagram_all'>
			<?php echo '<script'; ?>
>
			jQuery('#graph_2').gvChart({
				chartType: 'LineChart',
				gvSettings: {
					vAxis: { title: 'Обьем Admin трафика' },
					hAxis: { title: 'День' },
					width: 900,
					height: 400,
					}
			});	
			<?php echo '</script'; ?>
>			
		<?php echo $_smarty_tpl->tpl_vars['request']->value['admin_stat_all'];?>

		</div><?php }?>
	<td>
	</tr>
	</table>

	
</div><?php }} ?>
