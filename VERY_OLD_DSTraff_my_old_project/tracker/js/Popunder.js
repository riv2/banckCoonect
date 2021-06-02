function t_Popunder(params) {
	
if (params.banner_ext == 'swf') {
	
jQuery('body').append("<div id = 'tLayer_" + params.tracker_hash + "' class = 'tPopunderLayer'>"+
                 "<div id = 'tWindow_" + params.tracker_hash + "' class = 'tPopunderWindow'>"+
				 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tPopunderClose'></div>"+
				 "<div class = 'tPopunderSwfConteiner'><div id = 'tSwf_" + params.tracker_hash + "' class = 'tPopunderSwfClick'></div>"+
				 "<object class = 'tPopunderSwfObject' type='application/x-shockwave-flash' data='" + params.banner_url + "'>"+
				 "<param name='movie' value='" + params.banner_url + "' /> </object>" +
				 "</div></div></div>");
			
jQuery('#tSwf_' + params.tracker_hash).click(function(){
	    var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
		jQuery('#tLayer_'+params.tracker_hash).hide();
		jQuery('#tWindow_'+params.tracker_hash).hide();
		jQuery('#tClose_'+params.tracker_hash).hide();
		if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}
	return false;
});

} else {
	
jQuery('body').append("<div id = 'tLayer_" + params.tracker_hash + "' class = 'tPopunderLayer'>"+
                 "<div id = 'tWindow_" + params.tracker_hash + "' class = 'tPopunderWindow'>"+
				 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tPopunderClose'></div>"+
				 "<a id = 'tA_" + params.tracker_hash + "' href = '" + params.click_url + "' alt = '" + params.click_url + "' >"+
				 "<img class = 'tPopunderImg' src = '" + params.banner_url + "' /></a></div></div>");

jQuery('#tA_' + params.tracker_hash).click(function(){
	   var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
		jQuery('#tLayer_'+params.tracker_hash).hide();
		jQuery('#tWindow_'+params.tracker_hash).hide();
		jQuery('#tClose_'+params.tracker_hash).hide();
		if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}
	return false;
});

}

jQuery('#tClose_'+params.tracker_hash).click(function(){
	jQuery('#tLayer_'+params.tracker_hash).hide();
	jQuery('#tWindow_'+params.tracker_hash).hide();
	jQuery('#tClose_'+params.tracker_hash).hide();
});

}



