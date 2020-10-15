$( document ).ready(function() {
var Site = $('#Site').val();

	if (location.hash != '') {
	var editRule = location.hash.replace('#', '');
		setTimeout(function () {
				$('[banner="'+editRule+'"]').click();
			}, 150);
	}
	
	$('#addBanner').click(function () {
		var number_first = $('.ItemBanner:last').attr('banner');
		var number_second = Number(number_first) + 1;
			if (number_second < 16) {
				$('.ItemBanner').removeClass('switcherActive');
				$('#advansedSwitcher').append("<div class = 'ItemBanner switcherActive' banner = '"+number_second+
											  "' onclick = 'switchBanner("+number_second+");' >&nbsp;&nbsp;&nbsp;"+number_second+
											  "&rarr;&nbsp;&nbsp;&nbsp;<div class = 'removeBanner' onclick = 'removeBanner("+number_second+");'>x</div>"+
											  "<div class = 'ruleName' id = 'ruleName_"+number_second+"'></div></div>");
				
				var ConteinerBanner = $('#ConteinerBanner_'+number_first).html();
					ConteinerBanner = ConteinerBanner.replace(new RegExp('_'+number_first, 'g'),'_'+number_second);
				$('#ConteinerBanner_'+number_first).after("<div class = 'ConteinerBanner' id = 'ConteinerBanner_"+number_second+"'>"+ConteinerBanner+"</div>");
				$('.ConteinerBanner').addClass('hide');
				$('#ConteinerBanner_'+number_second).removeClass('hide');
				$('[name = "rule[_'+number_second+'][name]"]').val('').change();
				$('[name = "rule[_'+number_second+'][trafficType]"]').val($('[name = "rule[_'+number_first+'][trafficType]"]').val()).change();
				$('[name = "rule[_'+number_second+'][trafficTypeView]"]').val($('[name = "rule[_'+number_first+'][trafficTypeView]"]').val()).change();
				$('[name = "rule[_'+number_second+'][trafficURL]"]').val($('[name = "rule[_'+number_first+'][trafficURL]"]').val()).change();
				$('[name = "rule[_'+number_second+'][vkTitle]"]').val($('[name = "rule[_'+number_first+'][vkTitle]"]').val()).change();
				$('[name = "rule[_'+number_second+'][vkMsg]"]').val($('[name = "rule[_'+number_first+'][vkMsg]"]').val()).change();
				$('[name = "rule[_'+number_second+'][videoTimer]"]').val($('[name = "rule[_'+number_first+'][videoTimer]"]').val()).change();
				$('[name = "rule[_'+number_second+'][videoGo]"]').val($('[name = "rule[_'+number_first+'][videoGo]"]').val()).change();
				$('[name = "rule[_'+number_second+'][bannerWidth]"]').val($('[name = "rule[_'+number_first+'][bannerWidth]"]').val()).change();
				$('[name = "rule[_'+number_second+'][bannerHeight]"]').val($('[name = "rule[_'+number_first+'][bannerHeight]"]').val()).change();
				$('[name = "rule[_'+number_second+'][limitView]"]').val($('[name = "rule[_'+number_first+'][limitView]"]').val()).change();
				$('[name = "rule[_'+number_second+'][limiClick]"]').val($('[name = "rule[_'+number_first+'][limiClick]"]').val()).change();
				$('[name = "rule[_'+number_second+'][banner]"]').val($('[name = "rule[_'+number_first+'][banner]"]').val()).change();
				$('[name = "rule[_'+number_second+'][video]"]').val($('[name = "rule[_'+number_first+'][video]"]').val()).change();
				$('[name = "rule[_'+number_second+'][redirectTimer]"]').val($('[name = "rule[_'+number_first+'][redirectTimer]"]').val()).change();
				
				if ($('[name = "rule[_'+number_first+'][day1]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day1]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day1]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][day2]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day2]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day2]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][day3]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day3]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day3]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][day4]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day4]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day4]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][day5]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day5]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day5]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][day6]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day6]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day6]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][day0]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][day0]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][day0]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][unreferal]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][unreferal]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][unreferal]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][unredirect]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][unredirect]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][unredirect]"]').attr('checked',false);}
				if ($('[name = "rule[_'+number_first+'][blank]"]').prop("checked") == true) {$('[name = "rule[_'+number_second+'][blank]"]').attr('checked',true);} else {$('[name = "rule[_'+number_second+'][blank]"]').attr('checked',false);}
		
				$('[name = "rule[_'+number_second+'][time]"]').val($('[name = "rule[_'+number_first+'][time]"]').val()).change();
				$('[name = "rule[_'+number_second+'][ipfiterWhite]"]').val($('[name = "rule[_'+number_first+'][ipfiterWhite]"]').val()).change();
				$('[name = "rule[_'+number_second+'][ipfiterBlack]"]').val($('[name = "rule[_'+number_first+'][ipfiterBlack]"]').val()).change();
				
				$('[name = "rule[_'+number_second+'][filter][country][]"]').val($('[name = "rule[_'+number_first+'][filter][country][]').val()).change();
				
				setTimeout(function() { $('[name = "rule[_'+number_second+'][filter][city][]"]').val($('[name = "rule[_'+number_first+'][filter][city][]').val()).change();},300);
				
				$('[name = "rule[_'+number_second+'][filter][OS][]"]').val($('[name = "rule[_'+number_first+'][filter][OS][]').val()).change();
				$('[name = "rule[_'+number_second+'][filter][browser][]"]').val($('[name = "rule[_'+number_first+'][filter][browser][]').val()).change();
				$('[name = "rule[_'+number_second+'][filter][language][]"]').val($('[name = "rule[_'+number_first+'][filter][language][]').val()).change();
				$('[name = "rule[_'+number_second+'][filter][referals][]"]').val($('[name = "rule[_'+number_first+'][filter][referals][]').val()).change();

			}
	});
	

	$('input, select').change(function() { 
	  $(this).css('border', '1px solid grey');
	  $(this).css('background-color', 'white');
	  $('#newTrackerSendDiv').slideUp();
    });
	
	$('#galleryDivClose').click(function () {
		$('#galleryDiv').fadeOut();
	});

	
	
	$('#newTrackerSend').click(function () {
	
	  $('#loading').show();
			jQuery.ajax({
                    url:     Site+"/trackers/digest/", 
                    type:     "POST", 
                    dataType: "html", 
                    data: jQuery("#newTrackerForm").serialize(), 
                    success: function(response) { 
					if (response == 'ok') {
					window.location.href = Site+"/trackers/";
					} else if (response == 'edit_ok') {
					window.location.reload();
					} else {
					$('#loading').hide();
					displayValidate(response);
					}
                },
                error: function(response) { 
				window.location.href = Site+"/trackers/";
                }
			});
			
	}); 
		
	$('#preview').click(function () {

			jQuery.ajax({
                    url:     Site+"/trackers/digest/preview/", 
                    type:     "POST", 
                    dataType: "html", 
                    data: jQuery("#newTrackerForm").serialize(), 
                    success: function(response) { 
					if (response.length == 32) {
						window.open('/preview/'+response);
					} else {
					displayValidate(response);
					}
                },
                error: function(response) { 
				window.location.href = Site+"/trackers/";
                }
			});
	}); 

$('label#tipName').tinyTips('light', '<center> Наименование потока, используется в информативном формировании названия потока в списке потоков и анализе трафика.</center>');
$('label#tipTheme').tinyTips('light', '<center> Тематика потока, используется при модерации Администрацией потока, тематика потока должна строго соответствовать трафику проходящего через поток. </center>');
$('label#tipThemeURL').tinyTips('light', '<center> Источник трафика вашего потока, поток будет работать только с домена, который указан в этом параметре, можно представлять в виде списка в виде строки через запятую, например: http://domain1.com/, http://domain2.com/</center>');
$('label#tipDomain').tinyTips('light', '<center> Домен потока, который будет использоваться в коде ротатора потока, список пополняется из раздела мои домены.</center>');
$('div#tipPreview').tinyTips('light', '<center> Предосмотр потока, вы можете посмотреть, как будет вести себя ваш поток на бою.</center>');
$('div#tipTrackerSend').tinyTips('light', '<center> Необходимые поля должны быть заполнены, ошибки будут подсвечены. </center>');
$('div#addBanner').tinyTips('light', '<center> Клонировать правило в поток, копируются все настройки с предыдущего правила в новое правило.</center>');
$('div#tipTraffickBack').tinyTips('light', '<center> Правило Трафикбека, в это правило попадает весь трафик, который не подходит по другим правилам потока. </center>');
$('span#tipWEB').tinyTips('light', '<center> WEB трафик, в этот тип трафика попадает весь трафик, который не относится к мобильным устройствам или планшетам.</center>');
$('label#tipType').tinyTips('light', '<center> Тип подачи трафика для данного потока по данному правилу потока.</center>');
$('label#tipURLTraffic').tinyTips('light', '<center> URL, по которому будет отправлен трафик для данного потока по данному правилу потока. </center>');
$('label#tipBanner').tinyTips('light', '<center> Выбор изображения для формата трафика из вашей галереи данного потока по данному правилу потока, поддерживаются изображения, флеш.</center>');
$('label#tipUnTracker').tinyTips('light', '<center> Отключить полностью поток, поток не будет функционировать при включении этого атрибута. </center>');
$('span#tipWAP').tinyTips('light', '<center> WAP трафик, в этот тип трафика попадает весь трафик, который относится к мобильным  устройствам или планшетам. </center>');
$('label#tipBannersTimer').tinyTips('light', '<center> Показ рекламного формата раз в Nное время. </center>');
$('span#tipNameRule').tinyTips('light', '<center> Информативное название правила, которое используется в анализе трафика, максимум 9 символов.</center>');
$('div#tipFlush').tinyTips('light', '<center> Сбросить все настройки данного правила.</center>');
$('label#tipTypeTrafic').tinyTips('light', '<center> WEB трафик, в этот тип трафика попадает весь трафик который не относится к мобильным устройствам или планшетам. WAP трафик, в этот тип трафика попадает весь трафик, который относится к мобильным устройствам или планшетам. </center>');
$('label#tipLimitView').tinyTips('light', '<center> Лимит показов формата, по достижению этого лимита правило не будет работать в данном потоке. </center>');
$('label#tipLimitClicks').tinyTips('light', '<center> Лимит кликов формата, по достижению этого лимита правило не будет работать в данном потоке.  </center>');
$('label#tipBlank').tinyTips('light', '<center> При включенном состоянии открывает ссылки правила потока в новой вкладке, по умолчания в той же вкладке где находится код потока. </center>');
$('label#tipVkTitle').tinyTips('light', '<center> Заголовок для формата VK</center>');
$('label#tipVkMsg').tinyTips('light', '<center> Текст сообщения для формата VK </center>');
$('label#tipBannerStyle').tinyTips('light', '<center> Стиль для формата Banner, стиль задается для изображения\флеш в формате CSS, например: width:100px;height:100px; </center>');
$('label#tipRedirectTimer').tinyTips('light', '<center> Таймер который задает через какое время будет сделан редирект.</center>');
$('label#tipRedirectLimitView').tinyTips('light', '<center> Лимит редиректов для данного правила данного потока. </center>');
$('label#tipСountry').tinyTips('light', '<center> Фильтр стран для которых будет работать данное правило данного потока, через ctrl можно выбирать несколько стран, если не выбрана ни одна страна, значит фильт будет пропускать все страны. </center>');
$('label#tipCity').tinyTips('light', '<center> Фильтр городов, для которых будет работать данное правило данного потока, через ctrl можно выбирать несколько городов, если не выбран ни один город, значит, фильтр будет пропускать все города.  </center>');
$('label#tipOS').tinyTips('light', '<center> Фильтр операционных систем, для которых будет работать данное правило данного потока, через ctrl можно выбирать несколько операционных систем, если не выбрана ни одна операционная система, значит,  фильтр будет пропускать все операционные системы.  </center>');
$('label#tipBrowser').tinyTips('light', '<center> Фильтр браузеров, для которых будет работать данное правило данного потока, через ctrl можно выбирать несколько браузеров, если не выбран ни один браузер, значит,  фильтр будет пропускать все браузеры.  </center>');
$('label#tipLanguage').tinyTips('light', '<center> Фильтр языков для которых будет работать данное правило данного потока, через ctrl можно выбирать несколько языков, если не выбран ни один язык, значит фильтр будет пропускать все языки. </center>');
$('label#tipReferals').tinyTips('light', '<center> Фильтр ссылок с которых будет работать данное правило данного потока, список разделяется переносом строки, ссылки могут оканчиваться на *, что заменяет остальную часть ссылки, например: http://domain.ru/category/* | если список пустой, правило будет пропускать все ссылки. </center>');
$('label#tipIpfiterWhite').tinyTips('light', '<center> Белый список IP диапазонов, для которых будет работать данное правило данного потока, диапазон задается в виде x.x.x.x - y.y.y.y или просто x.x.x.x | если список пустой, правило будет пропускать все ip адреса. </center>');
$('label#tipIpfiterBlack').tinyTips('light', '<center> Черный список IP диапазонов, для которых не будет работать данное правило данного потока, диапазон задается в виде x.x.x.x - y.y.y.y или просто x.x.x.x | если список пустой, правило будет пропускать все ip адреса. </center>');
$('label#tipTime').tinyTips('light', '<center> Время работы данного правила данного потока, время задается диапазоном по МСК времени в виде hh:ii-hh:ii, в данных диапазонах правило будет работать, за пределами диапазонов правило не будет  работать, | если список пустой, правило будет работать всегда. </center>');
$('label#tipDay').tinyTips('light', '<center> Дни работы данного правила  данного потока, если день не выбран, правило в этот день не будет работать. </center>');
$('label#tipUnrule').tinyTips('light', '<center> Выключить данное правило в данном потоке. </center>');
$('label#tipUnPreview').tinyTips('light', '<center> Выключить ссылку предпоказа для потока. </center>');



});
 
	function flush (id) {
		var Site = $('#Site').val();
		
		$('[name = "rule['+id+'][trafficType]"]').val('web').change();
		$('[name = "rule['+id+'][trafficTypeView]"]').val('Banner').change();
		$('[name = "rule['+id+'][trafficURL]"]').val('').change();
		$('[name = "rule['+id+'][limitView]"]').val('0').change();
		$('[name = "rule['+id+'][limiClick]"]').val('0').change();
		$('[name = "rule['+id+'][vkTitle]"]').val('').change();
		$('[name = "rule['+id+'][vkMsg]"]').val('').change();
		$('[name = "rule['+id+'][videoTimer]"]').val('0').change();
		$('[name = "rule['+id+'][videoGo]"]').val('').change();
		$('[name = "rule['+id+'][bannerWidth]"]').val('0').change();
		$('[name = "rule['+id+'][bannerHeight]"]').val('0').change();		
		$('#banner_web_img'+id).attr('src',Site+'/public/img/addBanner.jpg');
		$('[name = "rule['+id+'][banner]"]').val('').change();
		$('[name = "rule['+id+'][video]"]').val('').change();
		$('[name = "rule['+id+'][redirectTimer]"]').val('').change();
  	    $('[name = "rule['+id+'][day1]"]').attr('checked',true);
		$('[name = "rule['+id+'][day2]"]').attr('checked',true);
		$('[name = "rule['+id+'][day3]"]').attr('checked',true);
		$('[name = "rule['+id+'][day4]"]').attr('checked',true);
		$('[name = "rule['+id+'][day5]"]').attr('checked',true);
		$('[name = "rule['+id+'][day6]"]').attr('checked',true);
		$('[name = "rule['+id+'][day0]"]').attr('checked',true);
		$('[name = "rule['+id+'][unreferal]"]').attr('checked',false);
		$('[name = "rule['+id+'][unredirect]"]').attr('checked',false);
		$('[name = "rule['+id+'][blank]"]').attr('checked',true);
		$('[name = "rule['+id+'][time]"]').val('00:00-12:00\n12:00-00:00').change();
		$('[name = "rule['+id+'][ipfiterWhite]"]').val('8.8.8.8-8.8.8.8\n0.0.0.0-255.255.255.255').change();
		$('[name = "rule['+id+'][ipfiterBlack]"]').val('127.0.0.1-127.0.0.1\n192.168.0.0-192.168.255.255').change();
		$('[name = "rule['+id+'][filter][country][]"]').val('').change();
		$('[name = "rule['+id+'][filter][city][]"]').val('').change();
		$('[name = "rule['+id+'][filter][OS][]"]').val('').change();
		$('[name = "rule['+id+'][filter][browser][]"]').val('').change();
		$('[name = "rule['+id+'][filter][language][]"]').val('').change();
		$('[name = "rule['+id+'][filter][referals][]"]').val('').change();

	} 
	
	function displayValidate (errorMsg) {

	var arrayMsg = errorMsg.split(','); 
	
	if (arrayMsg[1] == 'empty') {
	$('[name = "'+arrayMsg[0]+'"]').css('border-color','red');
	$('[name = "'+arrayMsg[0]+'"]').css('background-color','rgba(255,0,0,0.2)');
	
		if (arrayMsg[0] == 'rule[_0][trafficBackBanner_1]') {
		$('#typeBanners_0_1').css('background-color','rgba(255,0,0,0.2)');
		$('#typeBanners_0_1').children('label').css('color', 'rgba(255,0,0,1)');
		}
		
		if (arrayMsg[0] == 'rule[_0][trafficBackBanner_2]') {
		$('#typeBanners_0_2').css('background-color','rgba(255,0,0,0.2)');
		$('#typeBanners_0_2').children('label').css('color', 'rgba(255,0,0,1)');
		}

		if (arrayMsg[0] == 'rule['+arrayMsg[2]+'][banner]') {
		$('#typeBanners'+arrayMsg[2]).css('background-color','rgba(255,0,0,0.2)');
		$('#typeBanners'+arrayMsg[2]).children('label').css('color', 'rgba(255,0,0,1)');
		}
	} 
	
	if (arrayMsg[1] == 'errorType') {
	$('[name = "'+arrayMsg[0]+'"]').css('border-color','red');
	$('[name = "'+arrayMsg[0]+'"]').css('background-color','rgba(255,0,0,0.2)');
	$('[name = "'+arrayMsg[0]+'"]').val('');
	$('[name = "'+arrayMsg[0]+'"]').attr('placeholder','Некорректный тип данных');
	} 
	
	if (arrayMsg[2]) {
	var number = arrayMsg[2].replace('_', '');
	switchBanner (number);
	}
		
	}
	
	
	function removeBanner (number) {
		swal({
		title: "Удалить правило?",
		text: "Правило №"+number+" ,будет безвозвратно удалено из трекера.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Да, удалить'
	},
	function(){
		delBanner(number);
	});
		
	}
	
	function delBanner (number) {
		$('[banner = '+number+']').remove();
		$('#ConteinerBanner_'+number).remove();
		$('.ItemBanner').removeClass('switcherActive');
		$('.ConteinerBanner').addClass('hide');
		
		$('#ConteinerBanner_0').removeClass('hide');
		$('[banner = 0]').addClass('switcherActive');
	}
	
	function switchBanner (number) {

	if ($('#ConteinerBanner_'+number).html()) {
		
		$('.ConteinerBanner').addClass('hide');
		$('.ItemBanner').removeClass('switcherActive');

		$('#ConteinerBanner_'+number).removeClass('hide');
		$('[banner = '+number+']').addClass('switcherActive');
	}

	}
	
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

	function trafficBackTypeSwitch(type) {
		var Site = $('#Site').val();
		if ($(type).val() == 'Redirect' || $(type).val() == 'Frame') {
			$('#trafficbackBannerDiv').hide();
			$("[banner_web = 'trafficBackBanner']").val('');
			$('#trafficBackBanner').attr('src',Site+'/public/img/addBanner.jpg');
		} else {
			$('#trafficbackBannerDiv').fadeIn();	
		}
	}
	
	function limit(item,other) {
		
		if (item.value > 0) {
			$('#'+other).val('0');			
		}
	}
	
	
	function trafficRuleType(type,banner_rule,redirect_rule,k) {

		$('#typeBanner'+k).hide();
		$('#typeVK'+k).hide();
		$('#typeFullvideo'+k).hide();
		$('#typeFullparams'+k).hide();	
		
		if ($(type).val() == 'Redirect' || $(type).val() == 'Frame') {
			$('#'+banner_rule).hide();
			$('#'+redirect_rule).show();
			$('#blank'+k).hide();
			
		} else if ($(type).val() != 'Redirect' && $(type).val() != 'Frame') {
			$('#'+banner_rule).show();
			$('#'+redirect_rule).hide();
			$('#blank'+k).show();
		} 
		
		if ($(type).val() == 'Banner') {
			$('#typeBanner'+k).show();
		}
		if ($(type).val() == 'VKMessage') {
			$('#typeVK'+k).show();
		}
		if ($(type).val() == 'Fullvideo') {
			$('#typeFullvideo'+k).show();
			$('#typeFullparams'+k).show();	
			$('#'+banner_rule).hide();
			$('#blank'+k).show();
		}
		if ($(type).val() == 'Fullscreen') {
			$('#typeFullparams'+k).show();	
		}

		if ($(type).val() == 'ClickUnder') {
			$('#typeBanners'+k).hide();
		} else {
			$('#typeBanners'+k).show();
		}
		if ($(type).val() == 'ClickUnder' && banner_rule == 'banner_rule_0_1') {
			$('#typeBanners_0_1').hide();
		} 
		if ($(type).val() != 'ClickUnder' && banner_rule == 'banner_rule_0_1') {
			$('#typeBanners_0_1').show();
		} 
		if ($(type).val() == 'ClickUnder' && banner_rule == 'banner_rule_0_2') {
			$('#typeBanners_0_2').hide();
		} 
		if ($(type).val() != 'ClickUnder' && banner_rule == 'banner_rule_0_2') {
			$('#typeBanners_0_2').show();
		}
		if ($(type).val() == 'Banner' && banner_rule == 'banner_rule_0_1') {
			$('#typeBannerStyle_1').show();
		}
		if ($(type).val() != 'Banner' && banner_rule == 'banner_rule_0_1') {
			$('#typeBannerStyle_1').hide();
		}
		if ($(type).val() == 'Banner' && banner_rule == 'banner_rule_0_2') {
			$('#typeBannerStyle_2').show();
		}
		if ($(type).val() != 'Banner' && banner_rule == 'banner_rule_0_2') {
			$('#typeBannerStyle_2').hide();
		}
		if ($(type).val() == 'Fullvideo' && banner_rule == 'banner_rule_0_1') {
			$('#typeFullvideo_0_1').show();
		} 
		if ($(type).val() != 'Fullvideo' && banner_rule == 'banner_rule_0_1') {
			$('#typeFullvideo_0_1').hide();
		} 
		if ($(type).val() == 'Fullvideo' && banner_rule == 'banner_rule_0_2') {
			$('#typeFullvideo_0_2').show();
		} 
		if ($(type).val() != 'Fullvideo' && banner_rule == 'banner_rule_0_2') {
			$('#typeFullvideo_0_2').hide();
		} 
		
	}
	
	function trafficType(el,traffictype_id) {
	var id = traffictype_id.split('_');
	var id = id[1];
		if ($(el).val() == 'web') {
	 	   $("[selecter = '"+traffictype_id+"']").val('');
			$("["+traffictype_id+" = 'WAP']").hide();
			$("["+traffictype_id+" = 'WEB']").show();
			$('[banner = '+id+']').css('border-color','blue');
			$('[banner = '+id+']').children('.removeBanner').css('border-color','blue').css('background-color','blue');
			$(el).css('color','blue');
		} 
		if ($(el).val() == 'wap') { 
		$("[selecter = '"+traffictype_id+"']").val('');
			$("["+traffictype_id+" = 'WAP']").show();
			$("["+traffictype_id+" = 'WEB']").hide();
			$('[banner = '+id+']').css('border-color','red');
			$('[banner = '+id+']').children('.removeBanner').css('border-color','red').css('background-color','red');
			$(el).css('color','red');
		}
	}
	

	
	
	function redirectNumber (el) {
  	    var number = $(el).val();
		number = number.replace(/\D/gi, '');
		if (number == '') {
			number = '0';
		}
		$(el).val(number);
	}
	

	
	function referenceCity(el,referenceCity_id) {
	var Site = $('#Site').val();
	var iso = $(el).val();
	
	if (iso == null) {
		iso = '';
	}
	
	if (iso == '') {
	$('#'+referenceCity_id).html('');
	return;
	}
	
	if (iso.length > 10) {
	return;
	}
	
		jQuery.ajax({
				url:     Site+"/trackers/referenceCity/"+iso+"/", 
				type:     "GET", 
				dataType: "json", 
				success: function(response) { 
				$('#'+referenceCity_id).html('');
				for (var key in response) { 
					var val = response[key];

				val1 = val.city; val2 = val.country+' ('+val.city+') '; val3 = '';
				
				if (val.city != null) {
				$('#'+referenceCity_id).append("<option value = '"+val1+"' "+val3+">"+val2+"</option>");	
				}
				} 
			},
			error: function(response) { 
		
			}
		});
	}
	 
	function ruleName(val,k) {
		
		$('#ruleName'+k).html('');
		$('#ruleName'+k).html(val);
	} 
	
	function fliterBig(el) {
	  var cssObj = {
		'width' : '300px',
		'height' : '400px',
		'position' : 'absolute',
		'z-index' : '9999'
      }
      $(el).css(cssObj);
	}
	
	function fliterSmall(el) {
	  var cssObj = {
		'width' : '180px',
		'height' : '82px',
		'position' : 'static',
		'z-index' : '100'
      }
      $(el).css(cssObj);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	