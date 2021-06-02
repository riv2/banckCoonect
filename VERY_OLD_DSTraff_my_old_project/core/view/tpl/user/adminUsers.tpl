 <input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Администрирование - Пользователи</title>

<div class = "Conteiner">

<h1> Администрирование - Пользователи</h1> <hr>
 
<table class = "table">
<tr>
<th> № </th>
<th> ID </th>
<th> Логин </th>
<th> Трафик </th>
<th> Активный </th>
<th> Ник в ТОПе </th>
<th> E-mail </th>
<th> Тема </th>
<th> ip </th>
<th> Посл. вход </th>
<th> Создан </th>
<th> Действия </th>
</tr>

 {foreach $request.users as $item}

<tr>
<td width = '1%'> {counter} </td>
<td width = '1%'> {$item.id} </td>
<td width = '5%'><span class = 'selectOn'> {$item.login} </span> </td>
<td width = '1%'> {$item.stat} </td>
<td width = '1%'> {$item.active} </td>
<td width = '5%'> <span class = 'selectOn'>{$item.nick} </span></td>
<td width = '5%'> <span class = 'selectOn'>{$item.email} </span></td>
<td width = '5%'> {$item.theme} </td>
<td width = '5%'> <span class = 'selectOn'> {$item.ip} </span></td>
<td width = '5%'> {$item.timestamp} </td>
<td width = '5%'> {$item.created} </td>
<td width = '5%'>{if $item.id != 1}<a href = '{$request.Site}/adminUsers/ban/{$item.id}/'> Ban</a> <a href = '{$request.Site}/adminUsers/unban/{$item.id}/'>UnBan</a> {/if} </td>
</tr>

{/foreach}


</table>

</div>