$( document ).ready(function() {
var Site = $('#Site').val();

	if (location.hash != '') {
	var idMsg = location.hash.replace('#', '');
		setTimeout(function () {
				$('#msg_'+idMsg).click();
			}, 150);
	}

$('#newSupport').click(function () {
			$('#sNew').slideToggle();
	});
	
$('#sNewFormGo').click(function () {
	
	  var sNewSubject = $('#sNewSubject').val();
      var sNewMessage = $('#sNewMessage').val();
	
		    if (sNewSubject.length < 1) {
            $('#sNewSubject').css('border-color', 'red');
			$('#sNewSubject').attr('placeholder', 'Заполните название Тикета');
            return false;
            }
	
			if (sNewMessage.length < 1) {
			$('#sNewMessage').css('border-color', 'red');
			return false;
			}
			
			$('#loading').show();
			
			$('#sNewForm').submit();

	});



}); 


 function showMessage(id) {
 
    $('#sMessageTr'+id).slideToggle();
	
 }

	function DelSure(id) {
		
	swal({
		title: "Удалить Тикет?",
		text: "Тикет будет полностью удален из системы.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Да, удалить'
	},
	function(){
		Del(id);
	});
	
	}
	
	function Del(id) {
	$('#loading').show();		
		var Site = $('#Site').val();
		jQuery.ajax({
                    url:     Site+"/support/del/", 
                    type:     "POST", 
                    dataType: "html", 
                    data:  {id: id}, 
                    success: function(response) {
					if (response == "#success") {
					location.reload();
					}
                },
                error: function(response) { 
                console.log (response);
                }
        });
	
	}























