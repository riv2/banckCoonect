 $( document ).ready(function() {
var Site = $('#Site').val();


}); 

	function DelSure(id) {
		
	swal({
		title: "Вы уверены на действие удалить трекер?",
		text: "Трекер будет полностью удален из системы без возврата.",
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
                    url:     Site+"/trackers/del/", 
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

	function showCode (id) {
		
	$('#trackerCode'+id).slideDown();
		
	}


















