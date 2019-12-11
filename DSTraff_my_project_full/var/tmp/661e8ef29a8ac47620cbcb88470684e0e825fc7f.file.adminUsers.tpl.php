<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-09-15 22:00:23
         compiled from "/var/www/html/core/view/tpl/user/adminUsers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:111276220057daefc7235a32-76901689%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '661e8ef29a8ac47620cbcb88470684e0e825fc7f' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/adminUsers.tpl',
      1 => 1448464309,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '111276220057daefc7235a32-76901689',
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
  'unifunc' => 'content_57daefc727e473_55574627',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57daefc727e473_55574627')) {function content_57daefc727e473_55574627($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/html/core/smarty/plugins/function.counter.php';
?> <input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>DSTraff - Администрирование - Пользователи</title>

<div class = "Conteiner">

<h1> Администрирование - Пользователи</h1> <hr>
 
<table class = "table">
<tr>
<th> № </th>
<th> ID </th>
<th> Логин </th>
<th> Трафик </th>
<th> Активный </th>
<th> Ник в ТОПе </th>
<th> E-mail </th>
<th> Тема </th>
<th> ip </th>
<th> Посл. вход </th>
<th> Создан </th>
<th> Действия </th>
</tr>

 <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>

<tr>
<td width = '1%'> <?php echo smarty_function_counter(array(),$_smarty_tpl);?>
 </td>
<td width = '1%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
 </td>
<td width = '5%'><span class = 'selectOn'> <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
 </span> </td>
<td width = '1%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['stat'];?>
 </td>
<td width = '1%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['active'];?>
 </td>
<td width = '5%'> <span class = 'selectOn'><?php echo $_smarty_tpl->tpl_vars['item']->value['nick'];?>
 </span></td>
<td width = '5%'> <span class = 'selectOn'><?php echo $_smarty_tpl->tpl_vars['item']->value['email'];?>
 </span></td>
<td width = '5%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['theme'];?>
 </td>
<td width = '5%'> <span class = 'selectOn'> <?php echo $_smarty_tpl->tpl_vars['item']->value['ip'];?>
 </span></td>
<td width = '5%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
 </td>
<td width = '5%'> <?php echo $_smarty_tpl->tpl_vars['item']->value['created'];?>
 </td>
<td width = '5%'><?php if ($_smarty_tpl->tpl_vars['item']->value['id']!=1) {?><a href = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminUsers/ban/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/'> Ban</a> <a href = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/adminUsers/unban/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/'>UnBan</a> <?php }?> </td>
</tr>

<?php } ?>


</table>

</div><?php }} ?>
