function t_Adspot(params) {
	
if (params.banner_ext == 'swf') {
	
jQuery('body').append("<div id = 'tWindow_" + params.tracker_hash + "' class = 'tAdspotWindow'>"+
				 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tAdspotClose'></div>"+
				 "<div class = 'tAdspotSwfConteiner'><div id = 'tSwf_" + params.tracker_hash + "' class = 'tAdspotSwfClick'></div>"+
				 "<object class = 'tAdspotSwfObject' type='application/x-shockwave-flash' data='" + params.banner_url + "'>"+
				 "<param name='movie' value='" + params.banner_url + "' /> </object>" +
				 "</div></div>");
			
jQuery('#tSwf_' + params.tracker_hash).click(function(){
	jQuery('#tWindow_'+params.tracker_hash).hide();
	jQuery('#tClose_'+params.tracker_hash).hide();
	var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule);  
	addStat(prefix_stat+'click');
	if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
	}
	return false;
});

} else {
	
jQuery('body').append("<div id = 'tWindow_" + params.tracker_hash + "' class = 'tAdspotWindow'>"+
				 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tAdspotClose'></div>"+
				 "<a id = 'tA_" + params.tracker_hash + "' href = '" + params.click_url + "' alt = '" + params.click_url + "' >"+
				 "<img class = 'tAdspotImg' src = '" + params.banner_url + "' /></a></div></div>");

jQuery('#tA_' + params.tracker_hash).click(function(){
	jQuery('#tWindow_'+params.tracker_hash).hide();
	jQuery('#tClose_'+params.tracker_hash).hide();
	var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule); 
	addStat(prefix_stat+'click');
	if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
	}
	return false;
});

}

setTimeout (function (){jQuery('#tWindow_'+params.tracker_hash).fadeIn('slow');},300);

jQuery('#tClose_'+params.tracker_hash).click(function(){
	jQuery('#tWindow_'+params.tracker_hash).hide();
	jQuery('#tClose_'+params.tracker_hash).hide();
});

}
