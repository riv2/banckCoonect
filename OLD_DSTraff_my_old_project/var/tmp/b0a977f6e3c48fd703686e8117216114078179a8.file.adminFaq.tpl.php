<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-03-30 18:50:06
         compiled from "/var/www/tds/core/view/tpl/user/adminFaq.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5496504715a6ddadd923683-92924684%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b0a977f6e3c48fd703686e8117216114078179a8' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/adminFaq.tpl',
      1 => 1520343562,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5496504715a6ddadd923683-92924684',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a6ddadda5e060_40926041',
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a6ddadda5e060_40926041')) {function content_5a6ddadda5e060_40926041($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/tds/core/smarty/plugins/function.counter.php';
?><input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>TDSTraff - Администрирование - FAQ</title>

<div class = "Conteiner">

<h1> Администрирование - FAQ</h1> <hr>
 
<table class = "table">

<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminFaq/digest/' method = 'POST'>
<tr>
<td colspan = '10' >
Тема
 <input type = 'text' name = 'subject'>
 FAQ
 <textarea name = 'message'></textarea> 
 <input type = 'submit' value = 'Добавить' name = 'action'>
</td>
</tr>
</form>

<tr>
<th> № </th>
<th> ID </th>
<th> Тема </th>
<th> FAQ </th>
<th> Время создания </th>
<th> Действия </th>
</tr>
<?php if ($_smarty_tpl->tpl_vars['request']->value['faq']=='none') {?> 
   <tr>
		<td class = "tableNone" colspan="15"><center>Нет созданных FAQ</center></td>
   </tr>
   
   <?php } else { ?>
 <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['faq']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
 
<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminFaq/digest/' method = 'POST'>
<input type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
' name = 'id'>
<tr>
<td width = '1%'> <?php echo smarty_function_counter(array(),$_smarty_tpl);?>
 </td>
<td width = '1%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
 </td>
<td width = '15%'> <input style = 'width:100%;' type = 'text' name = 'subject' value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['subject'];?>
' />  </td>
<td width = '30%'>  <textarea style = 'width:400px; height:150px;'name = 'message'> <?php echo $_smarty_tpl->tpl_vars['item']->value['message'];?>
</textarea> </td>
<td width = '5%'><?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
 </td>
<td width = '5%'> <input type = 'submit' value = 'Редактировать' name = 'action'> <br><br><input type = 'submit' value = 'Удалить' name = 'action'> </td>
</tr>
</form>	

<?php } ?>
<?php }?>
</table>

</div><?php }} ?>
