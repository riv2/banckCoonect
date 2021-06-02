<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-16 16:20:56
         compiled from "/var/www/html/core/view/tpl/user/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175494632757b31338c8cac3-73254818%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5926d3cf32f2625deadf7fd6ef53618b602a2d62' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/index.tpl',
      1 => 1453312031,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175494632757b31338c8cac3-73254818',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'ADMIN' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b31338c91ba8_98235663',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b31338c91ba8_98235663')) {function content_57b31338c91ba8_98235663($_smarty_tpl) {?><?php echo '<script'; ?>
 type="text/javascript" src="http://www.google.com/jsapi"><?php echo '</script'; ?>
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
<title>DSTraff - Главная</title>

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
