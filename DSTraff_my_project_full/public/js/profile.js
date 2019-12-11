 $( document ).ready(function() {
 
  $('#profileSubmit').click(function() {
  
  var pass = $('#pass').val();
  var passConfirm = $('#passConfirm').val();
  var email = $('#email').val();
  
   if (pass.length > 0) {
  
   if (pass.length < 1) {
		$('#pass').css('border-color', 'red');
		$('#pass').attr('placeholder', 'Заполните Пароль');
		return false;
	}

    if (passConfirm.length < 1) {
		$('#passConfirm').css('border-color', 'red');
		$('#passConfirm').attr('placeholder', 'Заполните Пароль');
		return false;
	}

	if (pass != passConfirm) {
            $('#pass,#passConfirm').css('border-color', 'red');
			$('#pass,#passConfirm').val('');
			$('#pass,#passConfirm').attr('placeholder', 'Пароль не совпадает');
            return false;
        }			
   }
   
   if (!email.match(/^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i)) {
            $('#email').css('border-color', 'red');
			$('#email').val('');
			$('#email').attr('placeholder', 'Email не корректен');
            return false;
   }
   
   
   $('#loading').show();
   $('#profileInfoForm').submit();
   
  });
  
  
 });
 
 

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 