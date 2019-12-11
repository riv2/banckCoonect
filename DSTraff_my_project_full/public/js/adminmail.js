$( document ).ready(function() { 
var Site = $('#Site').val();

	$('#sendOk').click( function () {
		
		if ($("#sendOk").prop("checked")) {
			$("#sendBtn").prop("disabled",false);
		} else {
			$("#sendBtn").prop("disabled",true);
		}
		
	});

	$('#sendBtn').click(function () {
	if ($("#sendOk").prop("checked")) {
	 
	 $('#loading').show();
			jQuery.ajax({
                    url:     Site+"/adminMail/digest/", 
                    type:     "POST", 
                    dataType: "html", 
                    data: jQuery("#sendForm").serialize(), 
                    success: function(response) { 
					$('#loading').hide();

					var respn = response.split('|');

					if (respn[0] == 'ok') {
					$("#sendOk").removeAttr("checked");
					$('#emails').val('');
					
						swal({
							title: "Отправлено",
							text: respn[1],
							type: "success",
							showCancelButton: false,
							confirmButtonColor: '#DD6B55',
							confirmButtonText: 'Ок'
							},
							function(){
						});

					} else {	
					$("#sendOk").removeAttr("checked");	
						swal({
							title: "Ошибка",
							text: response,
							type: "error",
							showCancelButton: false,
							confirmButtonColor: '#DD6B55',
							confirmButtonText: 'Ок'
							},
							function(){
						});					
					}
                },
                error: function(response) { 
				window.location.href = Site+"/trackers/";
                }
			});
			
		}
	}); 
	
});