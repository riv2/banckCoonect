<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script language='JavaScript' src='{$request.Site}/public/js/jquery.gvChart-0.1.min.js' type='text/javascript'></script>
<script language='JavaScript' src='{$request.Site}/public/js/index.js' type='text/javascript'></script>

<input id = 'Site' type = 'hidden' value = '{$request.Site}'>
<title>TDSTraff - Главная</title>

<div class = 'Conteiner'>

	<h1> Главная</h1> <hr>

	<table style = 'display:none;' id = 'indexTable'>
	<tr>
	<td>
			<div class = 'diagram'>
			{$request.statistic}
			</div>
	</td>
	<td style = 'padding:38px 0px 0px 0px; vertical-align:top;line-height: 1.7;'>
			<div class = 'diagram_all'>
		{$request.statistic_all}
			</div>
	<td>
	</tr>
	<tr>
	<td><br>
			<div class = 'diagram'>	
			{$request.admin_stat}
			</div>
	</td>
	<td style = 'padding:53px 0px 0px 0px; vertical-align:top;line-height: 1.7;'>
		{if $ADMIN eq 'true'}<div class = 'diagram_all'>
			<script>
			jQuery('#graph_2').gvChart({
				chartType: 'LineChart',
				gvSettings: {
					vAxis: { title: 'Обьем Admin трафика' },
					hAxis: { title: 'День' },
					width: 900,
					height: 400,
					}
			});	
			</script>			
		{$request.admin_stat_all}
		</div>{/if}
	<td>
	</tr>
	</table>

	
</div>