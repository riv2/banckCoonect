<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-03-06 18:39:51
         compiled from "/var/www/tds/core/view/tpl/user/adminUsers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14477817945a43c294371d72-36878740%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '63e71f8a886cc35dcf2ebae1d699e8b9217abde0' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/adminUsers.tpl',
      1 => 1520343531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14477817945a43c294371d72-36878740',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a43c29438b490_14038286',
  'variables' => 
  array (
    'request' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a43c29438b490_14038286')) {function content_5a43c29438b490_14038286($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/tds/core/smarty/plugins/function.counter.php';
?> <input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>TDSTraff - Администрирование - Пользователи</title>

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
