function t_Topline(params) {
	
if (params.banner_ext == 'swf') {
	
jQuery('body').append("<div class = 'tMain'><div id = 'tWindow_" + params.tracker_hash + "' class = 'tToplineWindow'>"+
				 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tToplineClose'></div>"+
				 "<div class = 'tToplineSwfConteiner'><div id = 'tSwf_" + params.tracker_hash + "' class = 'tToplineSwfClick'></div>"+
				 "<object class = 'tToplineSwfObject' type='application/x-shockwave-flash' data='" + params.banner_url + "'>"+
				 "<param name='movie' value='" + params.banner_url + "' /> </object>" +
				 "</div></div></div>");
			
jQuery('#tSwf_' + params.tracker_hash).click(function(){
		var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
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
	
jQuery('body').append("<div class = 'tMain'><div id = 'tWindow_" + params.tracker_hash + "' class = 'tToplineWindow'>"+
				 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tToplineClose'></div>"+
				 "<a id = 'tA_" + params.tracker_hash + "' href = '" + params.click_url + "' alt = '" + params.click_url + "' >"+
				 "<img class = 'tToplineImg' src = '" + params.banner_url + "' /></a></div></div></div>");

jQuery('#tA_' + params.tracker_hash).click(function(){
	   var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
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
	jQuery('#tWindow_'+params.tracker_hash).hide();
	jQuery('#tClose_'+params.tracker_hash).hide();
});

}

