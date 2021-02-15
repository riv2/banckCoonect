
/*
// show ENT fields if user have certificate
$('#ENTStatus input:radio[name="haveEnt"]').change(function(){

    if (this.checked && this.value == '1') {
        $('#ENTData').slideDown();
    } else {
    	$('#ENTData').slideUp();
    }
});*/


$( "#ENTData input" ).keyup(function() {
  entDataChanged();
  
});

var entDataChanged = function(){
	//console.log('typed');

	var ikt = $( "#ikt" ).val();
	var iin = $("#iin").val();

	if(ikt.length == 9 && iin.length == 12) {

		var totalBal = 0;

	    $.ajax({
	      url:"//"+ window.location.host +"/"+langURL+"ENTjson/" + ikt + "/" + iin,
	      type:'get',
	      success:function(response){
	      	response = $.parseJSON(response);

	      	var resultHTML = "<pre>";
	      	resultHTML += "<p>Язык теста: " + response.langNameRu + "</p>";

	      	resultHTML += "<p>Вариант: " + response.variant;

	      	resultHTML += "<p>" + response.stageNameRu + ": ";
	      	
	      	$.each(response.userBallList, function( index, value ) {
			  resultHTML += "<li>" + value.subjectNameRu + ": " + value.ball + "</li>";
			  resultHTML += "<input type='hidden' name=subject_" + index + " value=" + value.ball + ">";
			  totalBal += value.ball;
			});

			resultHTML += "<p><b>Общий балл: " + totalBal + "</b></p>";

	      	resultHTML += "</p></pre>";
	        
	        $('#ENTResult').html(resultHTML);

	      }
	    });

	}

}


$('.regions select').on('change', function(e){

	var cities = $('.cities select');

	cities.find('option').hide();
	cities.find('option[region="' + this.value + '"]').show();

	cities.selectpicker('refresh');
});

/*
//==== school information fields =====
*/

//=========== local storage updater START ============
$(document).ready(function() {
    /*$.each(localStorage, function(key, value){
		if(key != 'length') {
			var obj = $('input[name='+key+']');

			if(obj.attr('type') == "radio") {
				//$('input[value="' + value + '"]').prop('checked', true);
			} else {
				$('input[type=text][name='+key+']').val(value);
				$('input[type=number][name='+key+']').val(value);
				$('input[type=date][name='+key+']').val(value);
				$('select[name='+key+']').val(value);
			}

		}
	});*/
});

//localStorage.clear();
/*
$("input").keyup(function() {
  localStorage[$(this).attr('name')] = this.value;
});*/

/*$("select, input:radio").on('change', function(e){
	localStorage[$(this).attr('name')] = this.value;
});*/
/*$('input[type=date]').change(function() {
	localStorage[$(this).attr('name')] = this.value;
});*/
//=========== local storage updater END ============
