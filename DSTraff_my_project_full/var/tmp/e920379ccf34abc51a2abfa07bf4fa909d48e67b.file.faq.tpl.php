<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-25 23:41:25
         compiled from "/var/www/html/core/view/tpl/user/faq.tpl" */ ?>
<?php /*%%SmartyHeaderCode:80339243557bf57f55fa4b0-50824369%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e920379ccf34abc51a2abfa07bf4fa909d48e67b' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/faq.tpl',
      1 => 1448464322,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '80339243557bf57f55fa4b0-50824369',
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
  'unifunc' => 'content_57bf57f564c571_71997114',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57bf57f564c571_71997114')) {function content_57bf57f564c571_71997114($_smarty_tpl) {?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/faq.js' type='text/javascript'><?php echo '</script'; ?>
>


<input id = 'Site' type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
'>
<title>DSTraff - FAQ</title>

<div class = 'Conteiner'>

	<h1> Часто Задаваемые Вопросы и Ответы</h1> <hr>
	
<table class = "faqTable">


<tr>
<td width="20%">
<div class = "faqList">
 <?php if ($_smarty_tpl->tpl_vars['request']->value['faq']=='none') {?> 
    <center><b>Список faq пуст</b></center> 
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['faq']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	  
	  <div id = "link<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" class = "faqLink" onclick = "faqshow('<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
');"><?php echo $_smarty_tpl->tpl_vars['item']->value['subject'];?>
</div>

   <?php } ?>
   <?php }?>




</div>
</td>

<td width="70%">
<div class = "faqDitail">

 <?php if ($_smarty_tpl->tpl_vars['request']->value['faq']=='none') {?> 
    <center><b>Список faq пуст</b></center> 
   <?php } else { ?>

   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['faq']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
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
