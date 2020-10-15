<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Администрирование - Новости</title>

<div class = "Conteiner">

<h1> Администрирование - Новости</h1> <hr>
 
<table class = "table">

<form action = '{$request.Site}/adminNews/digest/' method = 'POST'>
<tr>
<td colspan = '10' >
Тема
 <input type = 'text' name = 'subject'>
 Новость
 <textarea name = 'message'></textarea> 
 <input type = 'submit' value = 'Добавить' name = 'action'>
</td>
</tr>
</form>

<tr>
<th> № </th>
<th> ID </th>
<th> Тема </th>
<th> Новость </th>
<th> Время создания </th>
<th> Действия </th>
</tr>
{if $request.news eq 'none'} 
   <tr>
		<td class = "tableNone" colspan="15"><center>Нет созданных новостей</center></td>
   </tr>
   
   {else}
 {foreach $request.news as $item}
 
<form action = '{$request.Site}/adminNews/digest/' method = 'POST'>
<input type = 'hidden' value = '{$item.id}' name = 'id'>
<tr>
<td width = '1%'> {counter} </td>
<td width = '1%'> {$item.id} </td>
<td width = '15%'> <input style = 'width:100%;' type = 'text' name = 'subject' value = '{$item.subject}' />  </td>
<td width = '30%'>  <textarea style = 'width:400px; height:150px;'name = 'message'> {$item.message}</textarea> </td>
<td width = '5%'>{$item.timestamp} </td>
<td width = '5%'> <input type = 'submit' value = 'Редактировать' name = 'action'> <br><br><input type = 'submit' value = 'Удалить' name = 'action'> </td>
</tr>
</form>	

{/foreach}
{/if}
</table>

</div>