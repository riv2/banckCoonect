<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-03-29 23:42:13
         compiled from "/var/www/tds/core/view/tpl/user/adminDomains.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8298722265a4387d6348bf8-92490714%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '30053bc5f1fd834f83e491bd4c22073ebc30436a' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/adminDomains.tpl',
      1 => 1520343569,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8298722265a4387d6348bf8-92490714',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a4387d63a4e01_31491729',
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a4387d63a4e01_31491729')) {function content_5a4387d63a4e01_31491729($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/tds/core/smarty/plugins/function.counter.php';
?><input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>TDSTraff - Администрирование - Домены</title>

<div class = "Conteiner">

<h1> Администрирование - Домены</h1> <hr>
 
<table class = "table">
<tr>
<th> № </th>
<th> ID </th>
<th> Пользователь </th>
<th> Домен </th>
<th> Статус </th>
<th> Время создания </th>

</tr>
<?php if ($_smarty_tpl->tpl_vars['request']->value['domains']=='none') {?> 
   <tr>
		<td class = "tableNone" colspan="15"><center>Нет созданных доменов</center></td>
   </tr>
   
   <?php } else { ?>
 <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['domains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>

<tr>
<td width = '1%'> <?php echo smarty_function_counter(array(),$_smarty_tpl);?>
 </td>
<td width = '1%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
 </td>
<td width = '15%'> (<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
</td>
<td width = '30%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
 </td>
<td width = '5%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['status'];?>
 </td>
<td width = '5%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
 </td>

</tr>
<?php if ($_smarty_tpl->tpl_vars['item']->value['status']!='Припаркован') {?>
<tr>	
<td colspan = '10'>
<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminDomains/digest/' method = 'POST'>
<input type = 'hidden' name = 'id' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
'>
<input type = 'hidden' name = 'uid' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
'>
Изменить статус: 
<select name = 'status'>
<option value = ''></option>
<option value = 'Припаркован'>Припаркован</option>
<option value = 'Удалить'>Удалить</option>
</select>
<input type = 'submit' value = 'Отправить' /> 
</form>
</td>	
</tr>	
<?php }?>
<?php } ?>
<?php }?>

</table>

</div><?php }} ?>
