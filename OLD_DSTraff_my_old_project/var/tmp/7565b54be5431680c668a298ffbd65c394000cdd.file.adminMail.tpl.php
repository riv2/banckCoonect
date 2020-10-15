<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-04-08 19:32:40
         compiled from "/var/www/tds/core/view/tpl/user/adminMail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13198559275a58c1eaebd944-38595728%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7565b54be5431680c668a298ffbd65c394000cdd' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/adminMail.tpl',
      1 => 1520343555,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13198559275a58c1eaebd944-38595728',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a58c1eb064806_11822585',
  'variables' => 
  array (
    'request' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a58c1eb064806_11822585')) {function content_5a58c1eb064806_11822585($_smarty_tpl) {?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/adminmail.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>TDSTraff - Администрирование - Рассылка почты</title>

<div class = "Conteiner">

<h1> Администрирование - Рассылка почты</h1> <hr>
 
<table class = "table" >

<tr>
<th> E-mails пользователей в системе:</th>
<th> Письмо</th>
</tr>

<form id = 'sendForm'>
<tr>
<td width = '40%'>
E-mails:<br>
<textarea name = 'emails' style = 'width:400px; height:450px;' id = 'emails'>
<?php echo $_smarty_tpl->tpl_vars['request']->value['emails'];?>

</textarea>
</td>
<td width = '60%'>
<label>Тема</label><br>
<input type='text' name = 'subject' style = 'width: 400px;' maxlength='250'/> <br>
<label>Сообщение</label><br>
<textarea name = 'message' style = 'width:400px; height:450px;'>
</textarea>
</td>
</tr>
<tr>
<td colspan = '2'>
<center>
<input type = 'checkbox' id = 'sendOk'/> Я все заполнил правильно.
<input type = 'button' value = 'Отправить' disabled id = 'sendBtn'/>
</center>
</td>

</tr>


</form>

</table>







</div> <?php }} ?>
