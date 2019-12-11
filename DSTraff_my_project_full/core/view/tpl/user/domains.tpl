<script language='JavaScript' src='{$request.Site}/public/js/domains.js' type='text/javascript'></script>
<input id = "Site" type = "hidden" value = "{$request.Site}">
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
	{if $ADMIN eq 'true'}<col style="width: 10%">{/if}
	<col style="width: 55%">
	<col style="width: 10%">
	<col style="width: 10%">
	<col style="width: 5%">
	</colgroup>
	
    <tr class = "tableHeader">
        <th>№</th>
		{if $ADMIN eq 'true'}<th>Владелец</th>{/if}
        <th>Домен</th>
		<th>Был использован</th>
        <th>Статус</th>
        <th>Дата создания</th>
        <th>Действия</th>
    </tr>
   
   {if $request.domains eq 'none'} 
   <tr>
		<td class = "tableNone" colspan="6"><center>Нет припаркованых доменов</center></td>
   </tr>
   
   {else}
   {foreach $request.domains as $item}
	  
       <tr {if $item.status == 'Припаркован'}class = "domainOkTr" {/if} {if $item.status == 'Ожидание'}class = "domainWaitTr" {/if} {if $item.status == 'Удаление'}class = "domainRemoveTr" {/if}>
			<td><center>{counter}</center></td>
			{if $ADMIN eq 'true'}<td>({$item.uid}) {$item.login}</td>{/if}
			<td>{$item.domain} {if $item.for_all == '1'} <b>(Общий)</b> {/if}</td>
			<td>{$item.trackers}</td>
			<td {if $item.status == 'Припаркован'}class = "greenText" {/if} {if $item.status == 'Ожидание'}class = "grayText" {/if} {if $item.status == 'Удаление'}class = "redText" {/if}><center>{$item.status}</center></td>
			<td><center>{$item.timestamp}</center></td>
			<td class = "tEdit">
			{if $item.status != 'Удаление' && $item.status != 'Удален'}<div class = "tDelBotton" onclick= "DelSure({$item.id})">{/if}</div>
            </td>
       </tr>
	   
   {/foreach}
   {/if}

	</table>

	
	</div>

	<div class = "dNew" id = "dNew">
	<h2>Припарковать домен:</h2> 
	<hr>
	<div class = "dTipNew"> Отправить запрос Администрации сайта на паркование домена.</div>
    <form id = "dNewForm" method = "POST" action = "{$request.Site}/domains/new/">
	
	<div class = 'filed'> <input maxlength = "150" type = "text" name = "Domains[name]" id = 'dNewDomain' placeholder = 'Домен' /></div>
	
	<div class = 'filed'> <input id = "dNewFormGo" type = "button" value="Отправить запрос" /></div>
	
	</form>
	</div>
	
    <div class = "ButtonDiv" id = "newDomain">Добавить домен</div>

</div>

<div class = "dErrorMsg" id = "dErrorMsg"></div>






































