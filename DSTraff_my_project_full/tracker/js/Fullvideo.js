function t_Fullvideo (params) {

jQuery('body').append("<div class = 'tPlayer' id = 'tPlayer" + params.tracker_hash + "'><iframe width='100%' height='100%'"+
                      "src='https://www.youtube.com/embed/" + params.banner_url + "?enablejsapi=1&rel=0&loop=1&controls=0&autoplay=1&showinfo=0' frameborder='0' allowfullscreen></iframe></div>"+
                      "<div class = 'tSchetchik' id = 'tSchetchik" + params.tracker_hash + "'><div id = 'tTimerGo" + params.tracker_hash + "'>Пропустить через "+
					  "<span id = 'tTimer" + params.tracker_hash + "'></span> сек. </div><div class = 'tClose' id = 'tClose" + params.tracker_hash + "'>Закрыть</div></div>" + 
					  "<div class = 'tLinkGo' id = 'tLinkGo" + params.tracker_hash + "'></div>");

setTimeout(function() {				  
callPlayer("tPlayer" + params.tracker_hash, "playVideo");
}, 1000);	
setTimeout(function() {				  
callPlayer("tPlayer" + params.tracker_hash, "playVideo");
}, 2000);
setTimeout(function() {				  
callPlayer("tPlayer" + params.tracker_hash, "playVideo");
}, 3000);			  
					  
jQuery('#tLinkGo' + params.tracker_hash).click(function(){
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
jQuery('#tPlayer' + params.tracker_hash).remove();
jQuery('#tSchetchik' + params.tracker_hash).remove();
jQuery('#tLinkGo' + params.tracker_hash).remove();
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



function callPlayer(frame_id, func, args) {
    if (window.jQuery && frame_id instanceof jQuery) frame_id = frame_id.get(0).id;
    var iframe = document.getElementById(frame_id);
    if (iframe && iframe.tagName.toUpperCase() != 'IFRAME') {
        iframe = iframe.getElementsByTagName('iframe')[0];
    }

    if (iframe) {
        iframe.contentWindow.postMessage(JSON.stringify({
            "event": "command",
            "func": func,
            "args": args || [],
            "id": frame_id
        }), "*");
    }
}
	  


}