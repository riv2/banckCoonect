<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-09-14 23:35:28
         compiled from "/var/www/html/core/view/tpl/user/profile.tpl" */ ?>
<?php /*%%SmartyHeaderCode:90400302957d9b49056cb70-61142125%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ad60b95632d1bf287a23097fba48280d944bf43' => 
    array (
      0 => '/var/www/html/core/view/tpl/user/profile.tpl',
      1 => 1450867482,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '90400302957d9b49056cb70-61142125',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'countInvites' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57d9b4905edbf5_44531470',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57d9b4905edbf5_44531470')) {function content_57d9b4905edbf5_44531470($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/var/www/html/core/smarty/plugins/function.counter.php';
?><?php echo '<script'; ?>
 language='JavaScript' src='<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
/public/js/profile.js' type='text/javascript'><?php echo '</script'; ?>
>
<input id = 'Site' type = 'hidden' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['Site'];?>
'>
<title>DSTraff - Профиль</title>

<div class = 'Conteiner'>

	<h1> Профиль</h1> <hr>
	
<div class = 'profileInfo'>
<form id = 'profileInfoForm' method = 'POST' action = 'update'>
	<div class = 'profileItem'><h2>Информация об аккаунте</h2><hr></div>
	
	<div class = 'profileItem'><b>Ваш ID:</b> <?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['id'];?>
 </div>
	
	<div class = 'profileItem'><b>Ваш Логин:</b> <?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['login'];?>
  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Регистрационный инвайт:</span> <div class = 'pInvite'><?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['invite'];?>
</div>  </div>

	<div class = 'profileItem'><span class = 'profileLabel'>Инвайт выдан:</span> <div class = 'pInvite'><?php echo $_smarty_tpl->tpl_vars['request']->value['inviteOwner'];?>
</div>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Новый пароль: </span><br>
	<input type= 'password' name = 'User[pass]' id = 'pass' maxlength = '100' >  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Подвердить пароль:</span><br>
	<input type= 'password' name = 'User[passConfirm]' id = 'passConfirm' maxlength = '100'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваш e-mail: </span><br>
	<input type= 'text' name = 'User[email]' id = 'email' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['email'];?>
'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваш Skype: </span><br>
	<input type= 'text' name = 'User[skype]' id = 'skype' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['skype'];?>
'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваш ICQ: </span><br>
	<input type= 'text' name = 'User[icq]' id = 'icq' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['icq'];?>
'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваша страница Вконтакте: </span><br>
	<input type= 'text' name = 'User[vk]' id = 'vk' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['vk'];?>
'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваша страница Одноклассники: </span><br>
	<input type= 'text' name = 'User[ok]' id = 'ok' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['ok'];?>
'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваша страница Facebook : </span><br>
	<input type= 'text' name = 'User[fb]' id = 'fb' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['fb'];?>
'>  </div>

	<div class = 'profileItem'><span class = 'profileLabel'>Ваш ник в ТОПе: </span><br> 
	<input type= 'text' name = 'User[nick]' id = 'nick' maxlength = '100' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['nick'];?>
'> </div>
	
	<div class = 'profileItemRadio'><span class = 'profileLabel' style = 'margin-right:2px;'>Не показывать в ТОПе: </span> 
 	<input type="radio" name="User[hidden]" value="1" <?php if ($_smarty_tpl->tpl_vars['request']->value['profile'][0]['hidden']=='1') {?> checked <?php }?>><span class = 'profileLabel'> Да</span> 
	<input type="radio" name="User[hidden]" value="0" <?php if ($_smarty_tpl->tpl_vars['request']->value['profile'][0]['hidden']=='0') {?> checked <?php }?>><span class = 'profileLabel'> Нет</span> 
	</div>
	
	<div class = 'profileItemRadio'><span class = 'profileLabel'>Показывать подсказки: </span> 
 	<input type="radio" name="User[tips]" value="1" <?php if ($_smarty_tpl->tpl_vars['request']->value['profile'][0]['tips']=='1') {?> checked <?php }?>><span class = 'profileLabel'> Да</span> 
	<input type="radio" name="User[tips]" value="0" <?php if ($_smarty_tpl->tpl_vars['request']->value['profile'][0]['tips']=='0') {?> checked <?php }?>><span class = 'profileLabel'> Нет</span> 
	</div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Заметки: </span><br> 
<textarea id = 'ipRangeEdit' name = 'User[notes]' value = '<?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['notes'];?>
' ><?php echo $_smarty_tpl->tpl_vars['request']->value['profile'][0]['notes'];?>
</textarea> </div>
<br>
	<div class = 'profileItem'><input id = 'profileSubmit' type = 'button' value = 'Обновить данные профиля'></div>
</form>	
</div>


<div class = 'profileInvite'>	 
	<div class = 'profileItem'><h2>Инвайты (максимум <?php echo $_smarty_tpl->tpl_vars['countInvites']->value;?>
)</h2><hr></div>
	
   <?php if ($_smarty_tpl->tpl_vars['request']->value['addinvite']=='true') {?> 
	<form method = 'POST' action = 'invite'>
	<div class = 'profileItem' style = 'padding-left:10px;'><input id = 'profileInviteAdd' type = 'submit' value = 'Создать инвайт'></div>
	</form>
   <?php }?>
	

   <?php if ($_smarty_tpl->tpl_vars['request']->value['invites']>1) {?>
   <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['request']->value['invites']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	  
	<div class = "inviteCount"><?php echo smarty_function_counter(array(),$_smarty_tpl);?>
</div>
	<div class = "profileInviteItem <?php if ($_smarty_tpl->tpl_vars['item']->value['used']=='N') {?>selectOn <?php } else { ?> inviteUsed <?php }?>"><?php echo $_smarty_tpl->tpl_vars['item']->value['invite'];?>
</div>
	<?php if ($_smarty_tpl->tpl_vars['item']->value['used']!='N') {?><div class = "profileWhoUsedLogin ">Использовал: <?php echo $_smarty_tpl->tpl_vars['item']->value['whoUsedLogin'];?>
</div><?php }?>
	<div class = "clear"></div>
   <?php } ?>
   <?php }?>
  
	  

</div>	

<div class = 'pColorSwitcher'>	
<h2> Цветовая тема</h2> <hr>
<div class = 'pColorItems'> 
<a href = 'color/blue/'><div class = 'pColorItem pColorBlue <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='blue') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/green/'><div class = 'pColorItem pColorGreen <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='green') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/red/'><div class = 'pColorItem pColorRed <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='red') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/grey/'><div class = 'pColorItem pColorGrey <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='grey') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/orange/'><div class = 'pColorItem pColorOrange <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='orange') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/cyan/'><div class = 'pColorItem pColorCyan <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='cyan') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/peru/'><div class = 'pColorItem pColorPeru <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='peru') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/orchid/'><div class = 'pColorItem pColorOrchid <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='orchid') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/brown/'><div class = 'pColorItem pColorBrown <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='brown') {?>pColorActive<?php }?>'></div></a>
<a href = 'color/black/'><div class = 'pColorItem pColorBlack <?php if ($_smarty_tpl->tpl_vars['request']->value['theme']=='black') {?>pColorActive<?php }?>'></div></a>
</div>
</div>


</div><?php }} ?>
