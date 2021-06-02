$( document ).ready(function() {
var Site = $('#Site').val();



var error = location.hash;
if (error != '') {
  if (error == '#error_uniq') {
	    setTimeout(function() { 
        $('#dErrorMsg').click();
      }, 100);
  }
  location.hash = '';
}


$('#newDomain').click(function () {
		$('#dNew').slideToggle();
	});
	
$('#dErrorMsg').click(function () {
	 swal('Ошибка', 'Такой домен уже припаркован в системе, обратитесь в тех поддержку за подробностями.');
	});	
	
	
$('#dNewFormGo').click(function () {
	
	  var dNewDomain = $('#dNewDomain').val();
    	
		    if (dNewDomain.length < 1) {
            $('#dNewDomain').css('border-color', 'red');
			$('#dNewDomain').attr('placeholder', 'Заполните имя домена');
            return false;
            }
			
			if (!dNewDomain.match(/^[^-\._][a-z\d_\.-]+\.[a-z]{2,6}$/i)) {
            $('#dNewDomain').css('border-color', 'red');
			$('#dNewDomain').val('');
			$('#dNewDomain').attr('placeholder', 'Домен не корректен');
            return false;
            }
			

			$('#loading').show();
			
			$('#dNewForm').submit();

	});

}); 





	function DelSure(id) {
		
	swal({
		title: "Отправить запрос на удаление?",
		text: "Домен будет отпаркован от системы после расмотрения запроса Администрацией.",
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
                    url:     Site+"/domains/del/", 
                    type:     "POST", 
                    dataType: "html", 
                    data:  {id: id}, 
                    success: function(response) {
					console.log (response);	
					if (response == "#success") {
					location.reload();
					}
                },
                error: function(response) { 
                console.log (response);
                }
        });
	
	}





















