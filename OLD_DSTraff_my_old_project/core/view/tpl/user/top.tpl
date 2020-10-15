<script language='JavaScript' src='{$request.Site}/public/js/top.js' type='text/javascript'></script>
<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Топ</title>

<div class = "Conteiner">

	<h1> TOП {$request.topLimit} </h1> <hr>
	
		<table class = "table" style = "width:600px!important;">
	
	<colgroup>
	<col style="width: 1%">
	{if $ADMIN eq 'true'}<col style="width: 20%">{/if}
	<col style="width: 50%">
	<col style="width: 50%">
	</colgroup>
	
	<tr class = "tableHeader">
        <th colspan = '4'><center>ТОП {$request.topLimit} по общему объему запросов за 2 месяца с {$request.toDay}.</center></th>
    </tr>
	
    <tr class = "tableHeader">
        <th>№</th>
		{if $ADMIN eq 'true'}<th>Владелец</th> {/if}
        <th>Ник</th>
		<th>Объем запросов</th>
    </tr>
   
   {if $request.statistic eq 'none'} 
   <tr>
		<td class = "tableNone textCenter" colspan="9">Потоков нет</td>
   </tr>
   
   {else}
   {foreach $request.statistic as $item}
	   <tr>
			<td class = 'textCenter'>{counter}</td>
		{if $ADMIN eq 'true'}<td>({$item.uid}) {$item.login}</td> {/if}
			<td>{$item.nick}</td>
			<td class = 'textCenter'>{$item.statView}</td>
	
       </tr>
	 
   {/foreach}
   {/if}

 
	</table>
	
	
</div>