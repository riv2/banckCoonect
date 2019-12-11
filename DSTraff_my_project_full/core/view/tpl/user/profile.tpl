<script language='JavaScript' src='{$request.Site}/public/js/profile.js' type='text/javascript'></script>
<input id = 'Site' type = 'hidden' value = '{$request.Site}'>
<title>TDSTraff - Профиль</title>

<div class = 'Conteiner'>

	<h1> Профиль</h1> <hr>
	
<div class = 'profileInfo'>
<form id = 'profileInfoForm' method = 'POST' action = 'update'>
	<div class = 'profileItem'><h2>Информация об аккаунте</h2><hr></div>
	
	<div class = 'profileItem'><b>Ваш ID:</b> {$request.profile.0.id} </div>
	
	<div class = 'profileItem'><b>Ваш Логин:</b> {$request.profile.0.login}  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Регистрационный инвайт:</span> <div class = 'pInvite'>{$request.profile.0.invite}</div>  </div>

	<div class = 'profileItem'><span class = 'profileLabel'>Инвайт выдан:</span> <div class = 'pInvite'>{$request.inviteOwner}</div>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Новый пароль: </span><br>
	<input type= 'password' name = 'User[pass]' id = 'pass' maxlength = '100' >  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Подвердить пароль:</span><br>
	<input type= 'password' name = 'User[passConfirm]' id = 'passConfirm' maxlength = '100'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваш e-mail: </span><br>
	<input type= 'text' name = 'User[email]' id = 'email' maxlength = '100' value = '{$request.profile.0.email}'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваш Skype: </span><br>
	<input type= 'text' name = 'User[skype]' id = 'skype' maxlength = '100' value = '{$request.profile.0.skype}'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваш ICQ: </span><br>
	<input type= 'text' name = 'User[icq]' id = 'icq' maxlength = '100' value = '{$request.profile.0.icq}'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваша страница Вконтакте: </span><br>
	<input type= 'text' name = 'User[vk]' id = 'vk' maxlength = '100' value = '{$request.profile.0.vk}'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваша страница Одноклассники: </span><br>
	<input type= 'text' name = 'User[ok]' id = 'ok' maxlength = '100' value = '{$request.profile.0.ok}'>  </div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Ваша страница Facebook : </span><br>
	<input type= 'text' name = 'User[fb]' id = 'fb' maxlength = '100' value = '{$request.profile.0.fb}'>  </div>

	<div class = 'profileItem'><span class = 'profileLabel'>Ваш ник в ТОПе: </span><br> 
	<input type= 'text' name = 'User[nick]' id = 'nick' maxlength = '100' value = '{$request.profile.0.nick}'> </div>
	
	<div class = 'profileItemRadio'><span class = 'profileLabel' style = 'margin-right:2px;'>Не показывать в ТОПе: </span> 
 	<input type="radio" name="User[hidden]" value="1" {if $request.profile.0.hidden eq '1'} checked {/if}><span class = 'profileLabel'> Да</span> 
	<input type="radio" name="User[hidden]" value="0" {if $request.profile.0.hidden eq '0'} checked {/if}><span class = 'profileLabel'> Нет</span> 
	</div>
	
	<div class = 'profileItemRadio'><span class = 'profileLabel'>Показывать подсказки: </span> 
 	<input type="radio" name="User[tips]" value="1" {if $request.profile.0.tips eq '1'} checked {/if}><span class = 'profileLabel'> Да</span> 
	<input type="radio" name="User[tips]" value="0" {if $request.profile.0.tips eq '0'} checked {/if}><span class = 'profileLabel'> Нет</span> 
	</div>
	
	<div class = 'profileItem'><span class = 'profileLabel'>Заметки: </span><br> 
<textarea id = 'ipRangeEdit' name = 'User[notes]' value = '{$request.profile.0.notes}' >{$request.profile.0.notes}</textarea> </div>
<br>
	<div class = 'profileItem'><input id = 'profileSubmit' type = 'button' value = 'Обновить данные профиля'></div>
</form>	
</div>


<div class = 'profileInvite'>	 
	<div class = 'profileItem'><h2>Инвайты (максимум {$countInvites})</h2><hr></div>
	
   {if $request.addinvite eq 'true'} 
	<form method = 'POST' action = 'invite'>
	<div class = 'profileItem' style = 'padding-left:10px;'><input id = 'profileInviteAdd' type = 'submit' value = 'Создать инвайт'></div>
	</form>
   {/if}
	

   {if $request.invites > 1}
   {foreach $request.invites as $item}
	  
	<div class = "inviteCount">{counter}</div>
	<div class = "profileInviteItem {if $item.used == 'N'}selectOn {else} inviteUsed {/if}">{$item.invite}</div>
	{if $item.used != 'N'}<div class = "profileWhoUsedLogin ">Использовал: {$item.whoUsedLogin}</div>{/if}
	<div class = "clear"></div>
   {/foreach}
   {/if}
  
	  

</div>	

<div class = 'pColorSwitcher'>	
<h2> Цветовая тема</h2> <hr>
<div class = 'pColorItems'> 
<a href = 'color/blue/'><div class = 'pColorItem pColorBlue {if $request.theme eq 'blue'}pColorActive{/if}'></div></a>
<a href = 'color/green/'><div class = 'pColorItem pColorGreen {if $request.theme eq 'green'}pColorActive{/if}'></div></a>
<a href = 'color/red/'><div class = 'pColorItem pColorRed {if $request.theme eq 'red'}pColorActive{/if}'></div></a>
<a href = 'color/grey/'><div class = 'pColorItem pColorGrey {if $request.theme eq 'grey'}pColorActive{/if}'></div></a>
<a href = 'color/orange/'><div class = 'pColorItem pColorOrange {if $request.theme eq 'orange'}pColorActive{/if}'></div></a>
<a href = 'color/cyan/'><div class = 'pColorItem pColorCyan {if $request.theme eq 'cyan'}pColorActive{/if}'></div></a>
<a href = 'color/peru/'><div class = 'pColorItem pColorPeru {if $request.theme eq 'peru'}pColorActive{/if}'></div></a>
<a href = 'color/orchid/'><div class = 'pColorItem pColorOrchid {if $request.theme eq 'orchid'}pColorActive{/if}'></div></a>
<a href = 'color/brown/'><div class = 'pColorItem pColorBrown {if $request.theme eq 'brown'}pColorActive{/if}'></div></a>
<a href = 'color/black/'><div class = 'pColorItem pColorBlack {if $request.theme eq 'black'}pColorActive{/if}'></div></a>
</div>
</div>


</div>