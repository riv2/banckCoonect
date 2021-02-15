


//=========== local storage updater START ============
/*
$(document).ready(function() {
    $.each(localStorage, function(key, value){
		if(key != 'length') {
			var obj = $('input[name='+key+']');

			if(obj.attr('type') == "radio") {
				$('input[value="' + value + '"]').prop('checked', true);
			} else {
				$('input[name='+key+']').val(value);
				$('select[name='+key+']').val(value);
			}

		}
	});
});
*/

//localStorage.clear();

$("input").keyup(function() {
  //localStorage[$(this).attr('name')] = this.value;
});

$("select, input:radio").on('change', function(e){
	//localStorage[$(this).attr('name')] = this.value;
});
//=========== local storage updater END ============
