 $( document ).ready(function() {

 $('body').fadeIn("slow");	

 var Site = $('#Site').val();
 

  
  	$('input').change(function() { 
	  $(this).css('border', '1px solid #e0e1e1');
    });
 
 
  $('#regSubmit').click(function() {

		var rule = $('#rule').val();
        var loginReg = $('#loginReg').val();
        var pass = $('#pass').val();
        var passConfirm = $('#passConfirm').val();
		var email = $('#email').val();
        var invite = $('#invite').val();
		
		if (rule != 'apply') {
		   rules();
		}
	
        if (loginReg.length < 1) {
            $('#loginReg').css('border-color', 'red');
			$('#loginReg').attr('placeholder', 'Заполните Логин');
            return false;
        }
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
        if (email.length < 1) {
            $('#email').css('border-color', 'red');
			$('#email').attr('placeholder', 'Заполните Email');
            return false;
        }
        if (!email.match(/^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i)) {
            $('#email').css('border-color', 'red');
			$('#email').val('');
			$('#email').attr('placeholder', 'Email не корректен');
            return false;
        }
        if (invite.length < 1) {
            $('#invite').css('border-color', 'red');
			$('#invite').attr('placeholder', 'Заполните Инваит');
            return false; 
		}
		
		if (rule == 'apply') {
		jQuery.ajax({
                    url:     Site+"/reg/registration/", 
                    type:     "POST", 
                    dataType: "html", 
                    data: jQuery("#regForm").serialize(), 
                    success: function(response) { 
					regResponse (response,Site);
                },
                error: function(response) { 
                }
        }); }
	
		
  });
  
 
	
	$('#regButton').click(function () {

		$('#auth').hide();
		$('#reg').fadeIn('slow');
	});
	
	$('#loginButton').click(function () {
		$('#reg').hide();
		$('#auth').fadeIn('slow');
	});

});


$(document).keypress(function(e) {
    if(e.which == 13) {
        $('#loginFormGo').submit();
    }
});


function regResponse (response,Site) {
	
	if (response == '#error_uniq') {
		
		    $('#loginReg').css('border-color', 'red');
			$('#loginReg').val('');
			$('#loginReg').attr('placeholder', 'Логин занят');
		
	}
	
	if (response == '#error_invite') {
		
		    $('#invite').css('border-color', 'red');
			$('#invite').val('');
			$('#invite').attr('placeholder', 'Не верный инвайт');
		
	}
	
	if (response == '#error_uniqEmail') {
		
		    $('#email').css('border-color', 'red');
			$('#email').val('');
			$('#email').attr('placeholder', 'Е-mail уже занят');
		
	}

	if (response == '#login') {
		
		var loginReg = $('#loginReg').val();
		var pass = $('#pass').val();
		
		$('#login').val(loginReg);
		$('#password').val(pass);
		$('#loginFormGo').submit(); 
		
	}

	
}

function rules() {
var Site = $('#Site').val();
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
}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

