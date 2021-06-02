<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Администрирование - Домены</title>

<div class = "Conteiner">

<h1> Администрирование - Домены</h1> <hr>
 
<table class = "table">
<tr>
<th> № </th>
<th> ID </th>
<th> Пользователь </th>
<th> Домен </th>
<th> Статус </th>
<th> Время создания </th>

</tr>
{if $request.domains eq 'none'} 
   <tr>
		<td class = "tableNone" colspan="15"><center>Нет созданных доменов</center></td>
   </tr>
   
   {else}
 {foreach $request.domains as $item}

<tr>
<td width = '1%'> {counter} </td>
<td width = '1%'> {$item.id} </td>
<td width = '15%'> ({$item.uid}) {$item.login}</td>
<td width = '30%'> {$item.domain} </td>
<td width = '5%'> {$item.status} </td>
<td width = '5%'> {$item.timestamp} </td>

</tr>
{if $item.status != 'Припаркован'}
<tr>	
<td colspan = '10'>
<form action = '{$request.Site}/adminDomains/digest/' method = 'POST'>
<input type = 'hidden' name = 'id' value = '{$item.id}'>
<input type = 'hidden' name = 'uid' value = '{$item.uid}'>
Изменить статус: 
<select name = 'status'>
<option value = ''></option>
<option value = 'Припаркован'>Припаркован</option>
<option value = 'Удалить'>Удалить</option>
</select>
<input type = 'submit' value = 'Отправить' /> 
</form>
</td>	
</tr>	
{/if}
{/foreach}
{/if}

</table>

</div>