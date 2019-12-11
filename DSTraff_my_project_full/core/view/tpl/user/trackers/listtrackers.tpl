<script language='JavaScript' src='{$request.Site}/public/js/listtrackers.js' type='text/javascript'></script>

<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Список потоков</title>

<div class = "Conteiner">

	<h1> Мои Потоки</h1> <hr>
	
	<table class = "table">
	
	<colgroup>
	<col style="width: 1%">
	{if $ADMIN eq 'true'}<col style="width: 1%">{/if}
	{if $ADMIN eq 'true'}<col style="width: 1%">{/if}
	{if $ADMIN eq 'true'}<col style="width: 1%">{/if}
	<col style="width: 15%">
	<col style="width: 1%">
	<col style="width: 8%">
	<col style="width: 40%">
	<col style="width: 8%">
	<col style="width: 10%">
	<col style="width: 1%">
	</colgroup>
	
    <tr class = "tableHeader">
        <th>№</th>
		{if $ADMIN eq 'true'}<th>Владелец</th>{/if}
		{if $ADMIN eq 'true'}<th>Типы трафика</th>{/if}
		{if $ADMIN eq 'true'}<th>Тематика</th>{/if}
        <th>Назавание потока</th>
		<th>Запросы</th>
		<th>Модерация</th>
		<th>Коментарии Администратора</th>
        <th>Дата создания</th>
		<th>Код потока</th>
        <th>Действия</th>
    </tr>
   
   {if $request.trackers eq 'none'} 
   <tr>
		<td class = "tableNone" colspan="10"><center>Потоков нет</center></td>
   </tr>
   
   {else}
   {foreach $request.trackers as $item}
	   <tr>
			<td class = 'center'>{counter}</td>
			{if $ADMIN eq 'true'}<td>({$item.uid}){$item.login}</td>{/if}
			{if $ADMIN eq 'true'}<td>{$item.trafficTypes}</td>{/if}
			{if $ADMIN eq 'true'}<td>{$item.theme}</td>{/if}
			<td>({$item.id}) {$item.name}</td>
			<td>{$item.stat}</td>
			<td>{if $ADMIN eq 'true'}
			<form action = '{$request.Site}/trackers/moderate/' method = 'POST'>
			<input type = 'hidden' value = '{$item.id}' name = 'id'>
			<input type = 'hidden' value = '{$item.uid}' name = 'uid'>
			<select name = 'moderated' style = "margin-top:8px;">
			<option style = 'color:red;' value = 'N' {if $item.moderated eq 'N'}selected {/if}>На Модерации</option>
			<option style = 'color:orange;' value = 'R' {if $item.moderated eq 'R'}selected {/if}>Исправить</option>
			<option style = 'color:green;' value = 'Y' {if $item.moderated eq 'Y'}selected {/if}>Проверен</option>
			</select>
			{else}
			<center>{if $item.moderated eq 'N'}<span style = 'color:red;'>На Модерации</span>{/if}{if $item.moderated eq 'R'}<span style = 'color:orange;'>Исправить</span>{/if}{if $item.moderated eq 'Y'}<span style = 'color:green;'>Проверен</span>{/if} {/if}	</center>
			</td>
			<td>
			{if $ADMIN eq 'true'} 
			<input type = 'text' style = "width:350px;" name = 'comment' value = '{$item.comment}'>
			<input type = 'submit' value = 'Отправить' /> 
			</form>
			{else}
			{$item.comment}{/if}</td>
			<td class = 'center'><div class = 'w105'>{$item.timestamp}</div></td>
			<td>{if $item.moderated == 'Y' or $ADMIN eq 'true'}<div onclick= "showCode({$item.id})" class = "showCode">Получить коды</div>{/if}</td>
			<td class = "tEdit">
			<div class = 'w90'>
			<a href = 'edit/{$item.id}/'><div class = "tPreviewBotton"></div></a>
			<div class = "tDelBotton" onclick= "DelSure({$item.id})"></div>
			</div>
            </td>
       </tr>
	   {if $item.moderated eq 'Y' or $ADMIN eq 'true'}
	    <tr class = "trackerCode" id = "trackerCode{$item.id}">
			<td colspan = "10">
			<div class = "trackerCodeItem">
			<span class = 'codeHeader'>&uarr; Код потока тип 1: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>&lt;script id = &quot;{$item.trackerHash}&quot; async src = &quot;{$item.http}{$item.domain}/{$item.trackerHash}&quot;&gt;&lt;/script&gt;</span><br>
			<br><span class = 'codeHeader'>&uarr; Код потока тип 2: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style = "padding-left:181px;"><span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>
			{literal}
			&nbsp;&lt;script async type="text/javascript"&gt;<br>
			&nbsp;&nbsp;&nbsp;&nbsp;   (function(w) {<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	 var script = document.createElement('script');<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	 var i = setInterval(function() {<br>
			&nbsp;&nbsp;&nbsp;&nbsp;	   if(typeof w.document.body !== 'undefined') {<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		  {/literal} script.src = '{$item.http}{$item.domain}' + '/{$item.trackerHash}';<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	   w.document.getElementById('{$item.trackerHash}').appendChild(script);<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		   clearInterval(i);<br>
			&nbsp;&nbsp;&nbsp;&nbsp;	   }<br>
			&nbsp;&nbsp;&nbsp;	 }, 100);<br>
			&nbsp;&nbsp;})(window);<br>
			&nbsp;&lt;/script&gt;
			&nbsp;&lt;div id = '{$item.trackerHash}'&gt;&lt;/div&gt;
			
			</span></div> <br>
			<span class = 'codeHeader'>&uarr; Код потока тип 3: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style = "padding-left:181px;"><span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>
			
			&lt;label id = &quot;{$item.trackerHash}&quot; name = '{$item.trackerHash}'/&gt; &lt;script async type='text/javascript'&gt;<br>
			&nbsp;function addCodeScript() { var codeScript, elScript; <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;codeScript = document.createElement('script'); <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;elScript = document.getElementsByName('{$item.trackerHash}'); <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;codeScript.src = '{$item.http}{$item.domain}/{$item.trackerHash}'; <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;elScript[0].appendChild(codeScript); <br>
			} setTimeout(addCodeScript, 125); &lt;/script&gt;

			</span></div> <br>

			<br>
			<span class = 'codeHeader'>&uarr; Предпоказ ссылка: </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class = 'selectOn' style ='background-color:rgba(255,255,255,0.5);color:grey;'>{$item.http}{$item.domain}/preview/{$item.trackerHash}/</span>
			
			<script>
			</script>
			</div>
			</td>
       </tr>
	   {/if}
   {/foreach}
   {/if}

 
	</table>
 
    <div class = "ButtonA"><a href = "{$request.Site}/trackers/new/">Добавить поток</a></div>



</div>

<div id = 'previewDiv' class = 'previewDiv'>
	     <div class = 'previewDivClose' id = 'previewDivClose'></div> 
				<div id = "previewContent" class = "previewContent">
				</div>	
</div>	
