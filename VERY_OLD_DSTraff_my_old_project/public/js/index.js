	
	gvChartInit();
	
	jQuery(document).ready(function(){
	
			jQuery('#graph_1').gvChart({
				chartType: 'LineChart',
				gvSettings: {
					vAxis: {title: 'Обьем трафика'},
					hAxis: {title: 'День'},
					width: 900,
					height: 400,
					}
			});
			
			
			
			
			$('#indexTable').show();
		});