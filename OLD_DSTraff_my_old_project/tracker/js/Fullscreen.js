function t_Fullscreen (params) {

if (params.banner_ext == 'swf') {
	
jQuery('body').append("<div class = 'tMain'><div id = 'tWindow_" + params.tracker_hash + "' class = 'tFullscreenWindow'>"+
				 "<div class = 'tSchetchik' id = 'tSchetchik" + params.tracker_hash + "'><div id = 'tTimerGo" + params.tracker_hash + "'>Пропустить через "+
				 "<span id = 'tTimer" + params.tracker_hash + "'></span> сек. </div><div class = 'tClose' id = 'tClose" + params.tracker_hash + "'>Закрыть</div></div>"+
				 "<div class = 'tFullscreenSwfConteiner'><div id = 'tSwf_" + params.tracker_hash + "' class = 'tFullscreenSwfClick'></div>"+
				 "<object class = 'tFullscreenSwfObject' type='application/x-shockwave-flash' data='" + params.banner_url + "'>"+
				 "<param name='movie' value='" + params.banner_url + "' /> </object>" +
				 "</div></div></div>");
			
jQuery('#tSwf_' + params.tracker_hash).click(function(){
		var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
	    tCloseAll(params);
			if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}

	return false;
});
} else {
	
jQuery('body').append("<div class = 'tMain'><div id = 'tWindow_" + params.tracker_hash + "' class = 'tFullscreenWindow'>"+
				 "<div class = 'tSchetchik' id = 'tSchetchik" + params.tracker_hash + "'><div id = 'tTimerGo" + params.tracker_hash + "'>Пропустить через "+
				 "<span id = 'tTimer" + params.tracker_hash + "'></span> сек. </div><div class = 'tClose' id = 'tClose" + params.tracker_hash + "'>Закрыть</div></div>"+
				 "<a id = 'tA_" + params.tracker_hash + "' href = '" + params.click_url + "' alt = '" + params.click_url + "' >"+
				 "<img class = 'tFullscreenImg' src = '" + params.banner_url + "' /></a></div></div></div>");

jQuery('#tA_' + params.tracker_hash).click(function(){
	   var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
		tCloseAll(params);
				if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}

	return false;
});

}


jQuery('#tClose' + params.tracker_hash).click(function() {
	if (params.videoGo != 'on') {
		tCloseAll(params);
	} else {
		var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
		tCloseAll(params);
				if (params.blank == 'on') {
		window.open(params.click_url);
		} else {
		jQuery(location).attr("href",params.click_url);
		}
	  return false;
	}
});


function tCloseAll(params) {
jQuery('#tWindow_' + params.tracker_hash).remove();
jQuery('#tSchetchik' + params.tracker_hash).remove();
}

if (params.videoTimer > 0) {
	count = params.videoTimer;
} else {
	count = 0;
}

jQuery('#tTimer' + params.tracker_hash).html(count);
jQuery('#tTimerGo' + params.tracker_hash).show();

function interval()
{
  if(count == 0) {
	clearInterval(intervalID);
	jQuery('#tTimerGo' + params.tracker_hash).hide();
	jQuery('#tClose' + params.tracker_hash).show();
  }
count = count - 1;
jQuery('#tTimer' + params.tracker_hash).html(count);
}

var intervalID=setInterval(interval,1000);


}