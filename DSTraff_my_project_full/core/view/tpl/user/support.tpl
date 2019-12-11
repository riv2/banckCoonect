<script language='JavaScript' src='{$request.Site}/public/js/support.js' type='text/javascript'></script>
<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Тех. Поддержка</title>

<div class = "Conteiner">

	<h1> Мои Тикеты</h1> <hr>
	
	<div class = "sList" id = "sList"> 

	<table class = "table">
	
	<colgroup>
	<col style="width: 1%">
	{if $ADMIN eq 'true'}<col style="width: 10%">{/if}
	<col style="width: 55%">
	<col style="width: 10%">
	<col style="width: 10%">
	<col style="width: 5%">
	</colgroup>
	
    <tr class = "tableHeader">
        <th>№</th>
		{if $ADMIN eq 'true'}<th>Владелец</th>{/if}
        <th>Тикет</th>
        <th>Статус</th>
        <th>Дата создания</th>
        <th>Действия</th>
    </tr>
   
   {if $request.support eq 'none'} 
   <tr>
		<td class = "tableNone" colspan="6"><center>Нет созданных тикетов в тех. поддержку</center></td>
   </tr>
   
   {else}
   {foreach $request.support as $item}
	  
       <tr {if $item.answer != ''}class = "answerTr" {/if}>
			<td><center>{counter}</center></td>
			{if $ADMIN eq 'true'}<td>({$item.uid}) {$item.login}</td>{/if}
			<td>{$item.subject}</td>
			<td {if $item.answer != ''}class = "greenText" {/if}><center>{$item.status}</center></td>
			<td><center>{$item.timestamp}</center></td>
			<td class = "tEdit">
			<div id = "msg_{$item.id}" class = "tPreviewBotton" onclick = "showMessage({$item.id});"></div>
			{if $ADMIN eq 'true'}<div class = "tDelBotton" onclick= "DelSure({$item.id})"></div>{/if}
            </td>
       </tr>
	  
	  <tr class = "sMessageTr" id = "sMessageTr{$item.id}">
		<td class = "sMessageTd" colspan="6">
		 {foreach $item.msg as $one}
		 <div class = "sMessageMain"><h3>({$one.uTime}){if $ADMIN eq 'true'} Cообщение Пользователя: {else} Мое сообщение: {/if}</h3><hr> {$one.uMsg}</div>
		 {if {$one.aMsg} != ''} <div class = "sAnswerMain"><h3>({$one.aTime}) Ответ Администрации:</h3><hr>  {$one.aMsg}  </div> {/if}
		 {/foreach}
		  <br>
		 <div class = "sMessageMain"><h3>Мой ответ:</h3><hr> 
		<form method = "POST" action = "{$request.Site}/support/answer/{$item.id}/">
		 <div class = 'filedA'> <textarea id = 'ans_{$item.id}' name = 'Support[message]' placeholder = 'Введите ваш ответ'></textarea></div>
		 <div class = 'filedA'> <input type = "submit" value="Отправить ответ" /></div>
	    </form>
		 </div>
		</td>
      </tr>
	  
   {/foreach}
   {/if}

	</table>

	</div>

	<div class = "sNew" id = "sNew">
	<h2>Создать Тикет:</h2> 
	<hr>
    <form id = "sNewForm" method = "POST" action = "{$request.Site}/support/new/">
	{if $ADMIN eq 'true'}
	<div class = 'filed'> 
	 	<select name = 'Support[user]'>
		{foreach $request.users as $item}
		<option value = '{$item.id}'> ({$item.id}) {$item.login} </option>
		{/foreach}
		</select> 
	</div>
	{/if}
	<div class = 'filed'> <input maxlength = "150" type = "text" name = "Support[subject]" id = 'sNewSubject' placeholder = 'Название Тикета' /></div>
	<div class = 'filed'> <textarea id = 'sNewMessage' name = 'Support[message]' placeholder = 'Введите вашу проблему \ пожелание \ сообщение'></textarea></div>
	
	<div class = 'filed'> <input id = "sNewFormGo" type = "button" value="Отправить Тикет" /></div>
	
	</form>
	</div>
	

    <div class = "ButtonDiv" id = "newSupport">Создать Тикет</div>

</div>








































