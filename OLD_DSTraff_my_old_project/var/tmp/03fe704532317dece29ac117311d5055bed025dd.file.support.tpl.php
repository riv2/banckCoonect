<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-03-25 22:06:40
         compiled from "/var/www/tds/core/view/tpl/user/support.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14206653015a423fef7dbe83-40117447%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '03fe704532317dece29ac117311d5055bed025dd' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/support.tpl',
      1 => 1520343477,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14206653015a423fef7dbe83-40117447',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a423fef802b91_92147994',
  'variables' => 
  array (
    'request' => 0,
    'ADMIN' => 0,
    'item' => 0,
    'one' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a423fef802b91_92147994')) {function content_5a423fef802b91_92147994($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/tds/core/smarty/plugins/function.counter.php';
?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/support.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>TDSTraff - Тех. Поддержка</title>

<div class = "Conteiner">

	<h1> Мои Тикеты</h1> <hr>
	
	<div class = "sList" id = "sList"> 

	<table class = "table">
	
	<colgroup>
	<col style="width: 1%">
	<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><col style="width: 10%"><?php }?>
	<col style="width: 55%">
	<col style="width: 10%">
	<col style="width: 10%">
	<col style="width: 5%">
	</colgroup>
	
    <tr class = "tableHeader">
        <th>№</th>
		<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><th>Владелец</th><?php }?>
        <th>Тикет</th>
        <th>Статус</th>
        <th>Дата создания</th>
        <th>Действия</th>
    </tr>
   
   <?php if ($_smarty_tpl->tpl_vars['request']->value['support']=='none') {?> 
   <tr>
		<td class = "tableNone" colspan="6"><center>Нет созданных тикетов в тех. поддержку</center></td>
   </tr>
   
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['support']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	  
       <tr <?php if ($_smarty_tpl->tpl_vars['item']->value['answer']!='') {?>class = "answerTr" <?php }?>>
			<td><center><?php echo smarty_function_counter(array(),$_smarty_tpl);?>
</center></td>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><td>(<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
</td><?php }?>
			<td><?php echo $_smarty_tpl->tpl_vars['item']->value['subject'];?>
</td>
			<td <?php if ($_smarty_tpl->tpl_vars['item']->value['answer']!='') {?>class = "greenText" <?php }?>><center><?php echo $_smarty_tpl->tpl_vars['item']->value['status'];?>
</center></td>
			<td><center><?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
</center></td>
			<td class = "tEdit">
			<div id = "msg_<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" class = "tPreviewBotton" onclick = "showMessage(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
);"></div>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><div class = "tDelBotton" onclick= "DelSure(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
)"></div><?php }?>
            </td>
       </tr>
	  
	  <tr class = "sMessageTr" id = "sMessageTr<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
">
		<td class = "sMessageTd" colspan="6">
		 <?php  $_smarty_tpl->tpl_vars['one'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['one']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['item']->value['msg']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['one']->key => $_smarty_tpl->tpl_vars['one']->value) {
$_smarty_tpl->tpl_vars['one']->_loop = true;
?>
		 <div class = "sMessageMain"><h3>(<?php echo $_smarty_tpl->tpl_vars['one']->value['uTime'];?>
)<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?> Cообщение Пользователя: <?php } else { ?> Мое сообщение: <?php }?></h3><hr> <?php echo $_smarty_tpl->tpl_vars['one']->value['uMsg'];?>
</div>
		 <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['one']->value['aMsg'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1!='') {?> <div class = "sAnswerMain"><h3>(<?php echo $_smarty_tpl->tpl_vars['one']->value['aTime'];?>
) Ответ Администрации:</h3><hr>  <?php echo $_smarty_tpl->tpl_vars['one']->value['aMsg'];?>
  </div> <?php }?>
		 <?php } ?>
		  <br>
		 <div class = "sMessageMain"><h3>Мой ответ:</h3><hr> 
		<form method = "POST" action = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/support/answer/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/">
		 <div class = 'filedA'> <textarea id = 'ans_<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
' name = 'Support[message]' placeholder = 'Введите ваш ответ'></textarea></div>
		 <div class = 'filedA'> <input type = "submit" value="Отправить ответ" /></div>
	    </form>
		 </div>
		</td>
      </tr>
	  
   <?php } ?>
   <?php }?>

	</table>

	</div>

	<div class = "sNew" id = "sNew">
	<h2>Создать Тикет:</h2> 
	<hr>
    <form id = "sNewForm" method = "POST" action = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/support/new/">
	<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?>
	<div class = 'filed'> 
	 	<select name = 'Support[user]'>
		<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<option value = '<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
'> (<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
 </option>
		<?php } ?>
		</select> 
	</div>
	<?php }?>
	<div class = 'filed'> <input maxlength = "150" type = "text" name = "Support[subject]" id = 'sNewSubject' placeholder = 'Название Тикета' /></div>
	<div class = 'filed'> <textarea id = 'sNewMessage' name = 'Support[message]' placeholder = 'Введите вашу проблему \ пожелание \ сообщение'></textarea></div>
	
	<div class = 'filed'> <input id = "sNewFormGo" type = "button" value="Отправить Тикет" /></div>
	
	</form>
	</div>
	

    <div class = "ButtonDiv" id = "newSupport">Создать Тикет</div>

</div>








































<?php }} ?>
