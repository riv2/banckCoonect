function t_ClickUnder(params) {

jQuery('body').append("<div id = 'tLayer_" + params.tracker_hash + "' class = 'tClickUnderLayer'>");
	
jQuery('#tLayer_' + params.tracker_hash ).click(function(){
jQuery('#tLayer_' + params.tracker_hash ).remove();

	   var prefix_stat = uniqClick(params.tracker_hash,params.tracker_rule,'click'); 
	    addStat(prefix_stat+'click');
window.open(params.click_url, '', 'scrollbars=1,height='+screen.availHeight+',width='+screen.availWidth);
return false;
	
});

}
