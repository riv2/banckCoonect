<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-16 16:30:50
         compiled from "/var/www/html/core/view/tpl/user/top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:172787808957b314c918aed4-06276214%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd2bdf62658756c3f384ed4bf98caf5df9c4aa15e' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/top.tpl',
      1 => 1471354248,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '172787808957b314c918aed4-06276214',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b314c91d05a0_67519258',
  'variables' => 
  array (
    'request' => 0,
    'ADMIN' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b314c91d05a0_67519258')) {function content_57b314c91d05a0_67519258($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/html/core/smarty/plugins/function.counter.php';
?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/top.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Топ</title>

<div class = "Conteiner">

	<h1> TOП <?php echo $_smarty_tpl->tpl_vars['request']->value['topLimit'];?>
 </h1> <hr>
	
		<table class = "table" style = "width:600px!important;">
	
	<colgroup>
	<col style="width: 1%">
	<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><col style="width: 20%"><?php }?>
	<col style="width: 50%">
	<col style="width: 50%">
	</colgroup>
	
	<tr class = "tableHeader">
        <th colspan = '4'><center>ТОП <?php echo $_smarty_tpl->tpl_vars['request']->value['topLimit'];?>
 по общему объему запросов за 2 месяца с <?php echo $_smarty_tpl->tpl_vars['request']->value['toDay'];?>
.</center></th>
    </tr>
	
    <tr class = "tableHeader">
        <th>№</th>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><th>Владелец</th> <?php }?>
        <th>Ник</th>
		<th>Объем запросов</th>
    </tr>
   
   <?php if ($_smarty_tpl->tpl_vars['request']->value['statistic']=='none') {?> 
   <tr>
		<td class = "tableNone textCenter" colspan="9">Потоков нет</td>
   </tr>
   
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['statistic']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	   <tr>
			<td class = 'textCenter'><?php echo smarty_function_counter(array(),$_smarty_tpl);?>
</td>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><td>(<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
</td> <?php }?>
			<td><?php echo $_smarty_tpl->tpl_vars['item']->value['nick'];?>
</td>
			<td class = 'textCenter'><?php echo $_smarty_tpl->tpl_vars['item']->value['statView'];?>
</td>
	
       </tr>
	 
   <?php } ?>
   <?php }?>

 
	</table>
	
	
</div><?php }} ?>
