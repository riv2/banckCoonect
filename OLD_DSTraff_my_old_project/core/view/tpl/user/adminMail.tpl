<script language='JavaScript' src='{$request.Site}/public/js/adminmail.js' type='text/javascript'></script>
<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Администрирование - Рассылка почты</title>

<div class = "Conteiner">

<h1> Администрирование - Рассылка почты</h1> <hr>
 
<table class = "table" >

<tr>
<th> E-mails пользователей в системе:</th>
<th> Письмо</th>
</tr>

<form id = 'sendForm'>
<tr>
<td width = '40%'>
E-mails:<br>
<textarea name = 'emails' style = 'width:400px; height:450px;' id = 'emails'>
{$request.emails}
</textarea>
</td>
<td width = '60%'>
<label>Тема</label><br>
<input type='text' name = 'subject' style = 'width: 400px;' maxlength='250'/> <br>
<label>Сообщение</label><br>
<textarea name = 'message' style = 'width:400px; height:450px;'>
</textarea>
</td>
</tr>
<tr>
<td colspan = '2'>
<center>
<input type = 'checkbox' id = 'sendOk'/> Я все заполнил правильно.
<input type = 'button' value = 'Отправить' disabled id = 'sendBtn'/>
</center>
</td>

</tr>


</form>

</table>







</div> 