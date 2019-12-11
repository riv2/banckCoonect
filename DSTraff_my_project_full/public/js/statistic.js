gvChartInit();

$( document ).ready(function() {
	
var Site = $('#Site').val();

$('#trackers_1').change(function() { 
 $('#loading').show();
	var tracker = $('#trackers_1').val();
	if (tracker != '') {
		$('#rules_1').val('');
	}
	
	$('#stat_form').submit();
	
});

$('#rules_1').change(function() { 
$('#loading').show();
	$('#stat_form').submit();
});

			jQuery('#graph_1').gvChart({
				chartType: 'LineChart',
				gvSettings: {
					vAxis: {title: 'Обьем трафика'},
					hAxis: {title: 'День'},
					width: 1000,
					height: 350,
					}
			});	

});

function goDate () {
var Site = $('#Site').val();
}
	
	
	
	
	
	