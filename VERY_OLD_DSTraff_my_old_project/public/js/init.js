
$( document ).ready(function() {
 
var Site = $('#Site').val();
	$('body').fadeIn('fast');
	$('#loading').hide();
	
	$('#rules').click(function () {
		jQuery.ajax({
                    url:     Site+"/rules/", 
                    type:     "POST", 
                    dataType: "html", 
                    success: function(response) { 
					$('body').append(response);
                },
                error: function(response) { 
                }
        });
	});
	
	$('.load > a').click(function () {
	$('#loading').show();
	});
	
	$('.fade > a').click(function () {
	$('body').fadeOut('fast');
	});
	
	$('#popupClose').click(function () {
		
		$('#popup').fadeOut();
		
	});
	
	jQuery.ajax({
		url:     Site+"/popup/", 
		type:     "POST", 
		dataType: "html", 
		data: "", 
		success: function(response) { 
		if (response != 'null') {
			$('#popup').removeClass('hide');
			$('#popupText').html(response);			
		}
		},
		error: function(response) { 
		}
	});
	
	setInterval(function() {
	jQuery.ajax({
		url:     Site+"/authStatus/", 
		type:     "POST", 
		dataType: "html", 
		data: "", 
		success: function(response) { 
			if (response != 'Auth') {
				$(location).attr("href",Site);
			}
		},
		error: function(response) { 
		$(location).attr("href",Site);
		}
	});
	}, 100000);
	
	
	

});