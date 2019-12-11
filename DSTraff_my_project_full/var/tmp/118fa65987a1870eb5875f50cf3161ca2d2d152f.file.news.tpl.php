<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-17 11:09:30
         compiled from "/var/www/html/core/view/tpl/user/news.tpl" */ ?>
<?php /*%%SmartyHeaderCode:158665743957b41bbaa86700-96839075%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '118fa65987a1870eb5875f50cf3161ca2d2d152f' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/news.tpl',
      1 => 1448464340,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '158665743957b41bbaa86700-96839075',
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
  'unifunc' => 'content_57b41bbaaaf6b7_14417231',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b41bbaaaf6b7_14417231')) {function content_57b41bbaaaf6b7_14417231($_smarty_tpl) {?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/news.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = 'Site' type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
'>
<title>DSTraff - Новости</title>

<div class = 'Conteiner'>

	<h1> Новости</h1> <hr>
	
<table class = "newsTable">


<tr>
<td width="20%">
<div class = "newsList">
 <?php if ($_smarty_tpl->tpl_vars['request']->value['news']=='none') {?> 
    <center><b>Список новостей пуст</b></center> 
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['news']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	  
	  <div class = "newsTime"><?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
</div>
	  <div id = "link<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" class = "newsLink" onclick = "newsshow('<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
');"><?php echo $_smarty_tpl->tpl_vars['item']->value['subject'];?>
</div>

   <?php } ?>
   <?php }?>




</div>
</td>

<td width="70%">
<div class = "newsDitail">
 <?php if ($_smarty_tpl->tpl_vars['request']->value['news']=='none') {?> 
    <center><b>Список новостей пуст</b></center> 
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['news']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	  
	<div class = "hidden" id = "message<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['item']->value['message'];?>
 </div>

   <?php } ?>
   <?php }?>

</div>
</td>
</tr>

</table>


</div><?php }} ?>
