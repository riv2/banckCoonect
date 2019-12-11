<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-26 10:14:23
         compiled from "/var/www/html/core/view/tpl/user/adminNews.tpl" */ ?>
<?php /*%%SmartyHeaderCode:60149030157bfec4f7db315-43641493%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '645fa555853360979a8f789b5166e2763a7f1d9e' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/adminNews.tpl',
      1 => 1448464292,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '60149030157bfec4f7db315-43641493',
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
  'unifunc' => 'content_57bfec4f831a52_27121280',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57bfec4f831a52_27121280')) {function content_57bfec4f831a52_27121280($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/html/core/smarty/plugins/function.counter.php';
?><input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Администрирование - Новости</title>

<div class = "Conteiner">

<h1> Администрирование - Новости</h1> <hr>
 
<table class = "table">

<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminNews/digest/' method = 'POST'>
<tr>
<td colspan = '10' >
Тема
 <input type = 'text' name = 'subject'>
 Новость
 <textarea name = 'message'></textarea> 
 <input type = 'submit' value = 'Добавить' name = 'action'>
</td>
</tr>
</form>

<tr>
<th> № </th>
<th> ID </th>
<th> Тема </th>
<th> Новость </th>
<th> Время создания </th>
<th> Действия </th>
</tr>
<?php if ($_smarty_tpl->tpl_vars['request']->value['news']=='none') {?> 
   <tr>
		<td class = "tableNone" colspan="15"><center>Нет созданных новостей</center></td>
   </tr>
   
   <?php } else { ?>
 <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['news']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
 
<form action = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminNews/digest/' method = 'POST'>
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
