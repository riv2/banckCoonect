<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script language='JavaScript' src='{$request.Site}/public/js/jquery.gvChart-0.1.min.js' type='text/javascript'></script>
<script language='JavaScript' src='{$request.Site}/public/js/statistic.js' type='text/javascript'></script>
<link rel='stylesheet' type='text/css' href='{$request.Site}/public/css/calendar.css'>
<script language='JavaScript' src='{$request.Site}/public/js/calendar.js' type='text/javascript'></script>
<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Анализ трафика</title>

<div class = "Conteiner">
	<h1> Анализ трафика</h1> <hr>
<table class = 'sTable'>

	<tr>
		<th>Выбирите поток</th>
		<th>{if $request.rules_1 eq 'none'}{else}Правило{/if}</th>
		<th>Показать 10 дней до:</th> 
	</tr>
	<tr>
	<form id = 'stat_form' action = '' method = 'POST'>
	   
	   <td class = 'sTrackers'> 
		<select id = 'trackers_1' name = 'trackers_1'> 
		<option value = ''>Все</option>
		{if $request.trackers eq 'none'}{else}
		{foreach $request.trackers as $item}
		<option value = '{$item.id}' {if $request.selected_tracker_1 eq 'none'}{else} {if $request.selected_tracker_1 eq $item.id}selected{/if}{/if}> <b>({$item.id}) {$item.name}</b> {if $ADMIN eq 'true'} | (({$item.uid}) {$item.login}){/if}</option>
		{/foreach} {/if}
		</select> 
	   </td>
	   
	   <td class = 'sRules'>
	   	{if $request.rules_1 eq 'none'}{else}
			{$request.rules_1}
		{/if}
	   </td>
	   
	   <td>	   
		   <input maxlength = "10"  id="calendar-field" size="10" value = "{$request.toDay}" disabled /> <button id="calendar-trigger">...</button>
		   <input type = "hidden" value = "{$request.toDay}" name = "goDate" id = "goDate"/> 
			<script>
			Calendar.setup({
				inputField : "calendar-field",
				trigger    : "calendar-trigger",
				onSelect   : function() { this.hide() },
				min: {$request.cal_min},
				max: {$request.cal_max},
				onBlur   : function(cal) {
						$('#loading').show();
						var date = this.selection.get();
						date = Calendar.intToDate(date);
						date = Calendar.printDate(date, "%Y-%m-%d");
						$('#dateReport').html('').html(date);
						$('#goDate').val(date);
						$('#stat_form').submit();
					}
			});
			</script>
	   </td>
 
	   </form>
	</tr>

	<tr>
		<td colspan = '5'><br>
		<div class = 'diagram bg_white'>
		{if $request.diagram_1 eq 'none'}{else}
			{$request.diagram_1}
		{/if}
		</div>
		</td>
	</tr>
	
	<tr>
		<td colspan = '5'><br>
		<div class = 'diagram'>
			<div style = 'padding:10px;'>
				<table class = 'prev_table'>
				<tr>
					<th colspan = '11'><center> Количество уникальных отправленных запросов за {$request.goDate} по потокам.</center></th>
				</tr>
				<tr>
					<th>Название потока</th> <th>Редиректы</th> <th>Системе</th> <th>Показы</th>  <th>Системе</th> <th>Клики</th>  <th>Системе</th> <th>Фреймы</th>  <th>Системе</th> <th>Переходы</th> <th>Системе</th>
				</tr>
				{foreach $request.potoks as $item}
				<tr>
				<td>{$item.name}</td>
				<td class = 'bg_blue'>{$item.redirects}</td>
				<td class = 'bg_red'>{$item.redirects_dst}</td>
				<td class = 'bg_blue'>{$item.view}</td>
				<td class = 'bg_red'>{$item.view_dst}</td>
				<td class = 'bg_blue'>{$item.click}</td>
				<td class = 'bg_red'>{$item.click_dst}</td>
				<td class = 'bg_blue'>{$item.frame}</td>
				<td class = 'bg_red'>{$item.frame_dst}</td>
				<td class = 'bg_blue'>{$item.direct}</td>
				<td class = 'bg_red'>{$item.direct_dst}</td>
				</tr>
				{/foreach}
				</table>
			</div>
		</div>
		</td>
	</tr>
	
</table>
	
</div>
































