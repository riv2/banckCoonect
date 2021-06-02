<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-04-15 03:19:22
         compiled from "/var/www/tds/core/view/tpl/user/domains.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5031381575a4386adee7815-64423956%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d05298db5ed3bdf80f14aa03309cdcf3c94e2b3' => 
    array (
      0 => '/var/www/tds/core/view/tpl/user/domains.tpl',
      1 => 1523747951,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5031381575a4386adee7815-64423956',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a4386ae0295c7_06682948',
  'variables' => 
  array (
    'request' => 0,
    'ADMIN' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a4386ae0295c7_06682948')) {function content_5a4386ae0295c7_06682948($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/tds/core/smarty/plugins/function.counter.php';
?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/domains.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = "Site" type = "hidden" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
">
<title>TDSTraff - Домены</title>

<div class = "Conteiner">

	<h1> Мои припаркованые домены</h1> <hr>
	
	<div class = "dList" id = "dList"> 

		<div class = "dTip"> 
	<h2>Это важно.</h2><hr>
	Чтобы настройки вступили в силу, Вам необходима прописать у держателя домена (Там где вы купили \ приобрели домен) ваш домен и создать A запись на IP: <span class = 'selectOn'> <b> 194.58.107.149 .</b> </span>
	Полная парковка домена происходит до 3х суток. (Обычно в течении 1го дня). Как припарковать домен Вы можете узнать в разделе FAQ.
	</div> 
	<hr>
	
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
        <th>Домен</th>
		<th>Был использован</th>
        <th>Статус</th>
        <th>Дата создания</th>
        <th>Действия</th>
    </tr>
   
   <?php if ($_smarty_tpl->tpl_vars['request']->value['domains']=='none') {?> 
   <tr>
		<td class = "tableNone" colspan="6"><center>Нет припаркованых доменов</center></td>
   </tr>
   
   <?php } else { ?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['domains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	  
       <tr <?php if ($_smarty_tpl->tpl_vars['item']->value['status']=='Припаркован') {?>class = "domainOkTr" <?php }?> <?php if ($_smarty_tpl->tpl_vars['item']->value['status']=='Ожидание') {?>class = "domainWaitTr" <?php }?> <?php if ($_smarty_tpl->tpl_vars['item']->value['status']=='Удаление') {?>class = "domainRemoveTr" <?php }?>>
			<td><center><?php echo smarty_function_counter(array(),$_smarty_tpl);?>
</center></td>
			<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><td>(<?php echo $_smarty_tpl->tpl_vars['item']->value['uid'];?>
) <?php echo $_smarty_tpl->tpl_vars['item']->value['login'];?>
</td><?php }?>
			<td><?php echo $_smarty_tpl->tpl_vars['item']->value['domain'];?>
 <?php if ($_smarty_tpl->tpl_vars['item']->value['for_all']=='1') {?> <b>(Общий)</b> <?php }?></td>
			<td><?php echo $_smarty_tpl->tpl_vars['item']->value['trackers'];?>
</td>
			<td <?php if ($_smarty_tpl->tpl_vars['item']->value['status']=='Припаркован') {?>class = "greenText" <?php }?> <?php if ($_smarty_tpl->tpl_vars['item']->value['status']=='Ожидание') {?>class = "grayText" <?php }?> <?php if ($_smarty_tpl->tpl_vars['item']->value['status']=='Удаление') {?>class = "redText" <?php }?>><center><?php echo $_smarty_tpl->tpl_vars['item']->value['status'];?>
</center></td>
			<td><center><?php echo $_smarty_tpl->tpl_vars['item']->value['timestamp'];?>
</center></td>
			<td class = "tEdit">
			<?php if ($_smarty_tpl->tpl_vars['item']->value['status']!='Удаление'&&$_smarty_tpl->tpl_vars['item']->value['status']!='Удален') {?><div class = "tDelBotton" onclick= "DelSure(<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
)"><?php }?></div>
            </td>
       </tr>
	   
   <?php } ?>
   <?php }?>

	</table>

	
	</div>

	<div class = "dNew" id = "dNew">
	<h2>Припарковать домен:</h2> 
	<hr>
	<div class = "dTipNew"> Отправить запрос Администрации сайта на паркование домена.</div>
    <form id = "dNewForm" method = "POST" action = "<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/domains/new/">
	
	<div class = 'filed'> <input maxlength = "150" type = "text" name = "Domains[name]" id = 'dNewDomain' placeholder = 'Домен' /></div>
	
	<div class = 'filed'> <input id = "dNewFormGo" type = "button" value="Отправить запрос" /></div>
	
	</form>
	</div>
	
    <div class = "ButtonDiv" id = "newDomain">Добавить домен</div>

</div>

<div class = "dErrorMsg" id = "dErrorMsg"></div>






































<?php }} ?>
