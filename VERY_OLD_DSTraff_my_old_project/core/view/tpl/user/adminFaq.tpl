<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Администрирование - FAQ</title>

<div class = "Conteiner">

<h1> Администрирование - FAQ</h1> <hr>
 
<table class = "table">

<form action = '{$request.Site}/adminFaq/digest/' method = 'POST'>
<tr>
<td colspan = '10' >
Тема
 <input type = 'text' name = 'subject'>
 FAQ
 <textarea name = 'message'></textarea> 
 <input type = 'submit' value = 'Добавить' name = 'action'>
</td>
</tr>
</form>

<tr>
<th> № </th>
<th> ID </th>
<th> Тема </th>
<th> FAQ </th>
<th> Время создания </th>
<th> Действия </th>
</tr>
{if $request.faq eq 'none'} 
   <tr>
		<td class = "tableNone" colspan="15"><center>Нет созданных FAQ</center></td>
   </tr>
   
   {else}
 {foreach $request.faq as $item}
 
<form action = '{$request.Site}/adminFaq/digest/' method = 'POST'>
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