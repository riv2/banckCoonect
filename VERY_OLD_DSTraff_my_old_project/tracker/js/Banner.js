function t_Banner (params) {

if (params.banner_ext == 'swf') {
jQuery("[src = '" + params.tracker_url + "']").after("<div class = 'tBannerSwfConteiner'>" +
						"<div id = 'tSwf_" + params.tracker_hash + "' class = 'tBannerSwfClick' style = '"+ params.style +"'></div>" +
						"<object class = 'tBannerSwfObject' style = '"+ params.style +"' type='application/x-shockwave-flash' data='" + params.banner_url + "'>"+
						" <param name='movie' value='" + params.banner_url + "' /> </object>" +
						"</div>");
						
jQuery('#tSwf_' + params.tracker_hash).click(function(){
	var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click');  
	addStat(prefix_stat+'click');
	if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
	}
	return false;
});
	
} else {
jQuery("[src = '" + params.tracker_url + "']").after("<a id = 'tA_" + params.tracker_hash + "' href = '" + params.click_url + "'>"+
				 "<img class = 'tBannerImg' style = '" + params.style + "' src = '" + params.banner_url + "' /></a>");	

jQuery('#tA_' + params.tracker_hash).click(function(){
	var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	addStat(prefix_stat+'click');
	if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
	}
	return false;
}); 

}

}