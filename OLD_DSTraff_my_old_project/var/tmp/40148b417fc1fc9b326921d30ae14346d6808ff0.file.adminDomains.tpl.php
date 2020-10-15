<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-25 23:04:16
         compiled from "/var/www/html/core/view/tpl/user/adminDomains.tpl" */ ?>
<?php /*%%SmartyHeaderCode:140444400357bf4f4035d104-90711219%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '40148b417fc1fc9b326921d30ae14346d6808ff0' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/adminDomains.tpl',
      1 => 1448464270,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '140444400357bf4f4035d104-90711219',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57bf4f403bf471_90967363',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57bf4f403bf471_90967363')) {function content_57bf4f403bf471_90967363($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/html/core/smarty/plugins/function.counter.php';
?><input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Администрирование - Домены</title>

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
