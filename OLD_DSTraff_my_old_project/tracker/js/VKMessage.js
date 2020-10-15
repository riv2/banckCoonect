function t_VKMessage (params) {
	
if (params.banner_ext == 'swf') {
	
jQuery('body').append("<div id = 'tWindow_" + params.tracker_hash + "' class = 'tVKWindow' >"+
                 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tVKClose'></div>"+
				 "<div class = 'tTitle'>Новое сообщение</div>"+
				 "<div class = 'tvkText'><span class = 'tvkTitle'>"+ params.vkTitle + "</span> <span class = 'tvkMsg'>"+ params.vkMsg + "</span></div>" +
				 "<div class = 'tVKSwfConteiner'><div id = 'tSwf_" + params.tracker_hash + "' class = 'tVKSwfClick'></div>"+
				 "<object class = 'tVKSwfObject' type='application/x-shockwave-flash' data='" + params.banner_url + "'>"+
				 "<param name='movie' value='" + params.banner_url + "' /> </object>" +
				 "</div></div>");
			
jQuery('#tSwf_' + params.tracker_hash).click(function(){
	   var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    jQuery('#tWindow_' + params.tracker_hash).hide();
		jQuery('#tClose_'  + params.tracker_hash).hide();
	    addStat(prefix_stat+'click');
				if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}
	return false;
});

} else {
jQuery('body').append("<div id = 'tWindow_" + params.tracker_hash + "' class = 'tVKWindow' >"+
                 "<div id = 'tClose_" + params.tracker_hash + "' class = 'tVKClose'></div>"+
				 "<div class = 'tTitle'>Новое сообщение</div>"+
				 "<div class = 'tvkText'><span class = 'tvkTitle'>"+ params.vkTitle + "</span> <span class = 'tvkMsg'>"+ params.vkMsg + "</span></div>" +
				 "<a id = 'tA_" + params.tracker_hash + "' href = '" + params.click_url + "' alt = '" + params.click_url + "' >"+
				 "<img class = 'tVKImg' src = '" + params.banner_url + "' /></a></div></div>");
				 
jQuery('#tWindow_' + params.tracker_hash).click(function(){
		var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    jQuery('#tWindow_' + params.tracker_hash).hide();
		jQuery('#tClose_'  + params.tracker_hash).hide();
	    addStat(prefix_stat+'click');
				if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}
	return false;
});

}
setTimeout(function() { 
jQuery('#tWindow_' + params.tracker_hash).animate({ bottom: "0px" }, 1000);
 }, 500);


jQuery('#tClose_' + params.tracker_hash).click(function(){
	jQuery('#tWindow_' + params.tracker_hash).hide();
	jQuery('#tClose_'  + params.tracker_hash).hide();
});

}

