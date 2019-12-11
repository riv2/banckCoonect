$( document ).ready(function() { 	
	$('#galleryDivClose').click(function () {
		$('#galleryDiv').fadeOut();
	});
});
	
	
	function showGallery(id_input) {
		var Site = $('#Site').val();
		$('#iframeGallery').attr('src',Site+'/gallery/'+id_input+'/');
		
		setTimeout(function () {
			$('#galleryDiv').fadeIn();
		}, 150);
	}
	
	
	function addImg(img_src,input_id) {
		
		var filesExt = ['swf']; 
        var parts = img_src.split('.');
		if(filesExt.join().search(parts[parts.length - 1]) != -1){
		$('[svg = '+input_id+']').show();
		$('#'+input_id).hide();
		$('[svg = '+input_id+']').attr('data',img_src);
		} else {
		$('[svg = '+input_id+']').hide();
		$('#'+input_id).show();
		$('#'+input_id).attr('src',img_src);
		}

		$('[banner_web = '+input_id+']').val(img_src);
		$('#galleryDiv').fadeOut();
		$('#trafficbackBannerDiv').css('border', '1px solid rgba(0,0,0,0)');
		
	}
	
	function inputBig(el) {
	  var cssObj = {
		'position' : 'absolute',
		'z-index' : '9999',
		'width' : '500px'
      }
      $(el).css(cssObj);
	}
	

	function inputSmall(el) {
	  var cssObj_1 = {
		'width' : '100px'
      }
	  var cssObj_2 = {
		'position' : 'static',
		'z-index' : '100'
      }  
      $(el).css(cssObj_1);
	  setTimeout(function() {$(el).css(cssObj_2)},500);
	}
	
	function showAll(el) {
		
		$('#tr_'+el).toggleClass('hide');
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	