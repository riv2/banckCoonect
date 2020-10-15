$( document ).ready(function() {
	
	if (location.hash != '') {
		
		alert ("Невозможно удалить изображение пока оно используется в трекере " + location.hash);
	}
	
	
	$('.imgLayer').click(function() {
		
		var input_id = $('#input_id').val();
		parent.addImg($(this).attr('src'),input_id);
		
    });	
	
	$('.imgLayerAdd').click(function() {
		
		var input_id = $('#input_id').val();
		parent.addImg($(this).attr('src'),input_id);
		
    });	
	
	$('input, select').change(function() { 
	  $(this).css('border', '1px solid grey');
	  $('.input').css('border', '1px solid rgba(0,0,0,0)');
	  $('#newTrackerSendDiv').slideUp();
    });
	
  });
  
 
  function formGo() {
		
	    var valid = validate();
		if (valid) {
			$('#loading').show(); 
			$('#newGalleryElement').submit();
		}

  }
  
	
	function validate() {
		
		  var banner_file = $('#banner_file').val();

		  var filesExt = ['jpg', 'gif', 'png', 'jpeg', 'swf', 'JPG', 'GIF', 'PNG', 'JPEG', 'SWF']; 
		  
		  	if (banner_file.length > 1) {
				
				var parts = banner_file.split('.');
				 if(filesExt.join().search(parts[parts.length - 1]) != -1){
					
				} else {
						swal("Не корректный тип фаила", "Допустимы только jpg / jpeg / gif / png / swf");
				        return false;
				}
			}
			
			if (banner_file.length < 1) {
				 $('.input').css('border-color', 'red');
				 return false;
			}

		return true;
		
	}
	 
	
  function file_exist () {
    alert("Банер с таким именем уже существует в галлерее, используйте существующий баннер или переиминуте загружаемый баннер.");
  }
	 

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	