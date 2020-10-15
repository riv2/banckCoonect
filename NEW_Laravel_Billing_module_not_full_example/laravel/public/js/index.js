var date_now = new Date();
$.cookie("timezone", date_now.getTimezoneOffset(), {expires: 365, path: '/'});
/*
window.onfocus = function() {
	if(alive_url) {
		window.apiCall(alive_url, null, null, 
			function(data){
				if(data.error) {
					console.log(data.data);
				}
				else {
					if(!data.data) {
						window.location.href = data.redirect;
					}
				}
			}, 
			function(status){
				console.log(status);
			}
		);
	}
};
*/
var user = User();
$.fn.datepicker.dates['ru'] = {
    days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
	months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
	monthsShort: ["Янв.", "Фев.", "Мар.", "Апр.", "Май", "Июн.", "Июл.", "Авг.", "Сен.", "Окт.", "Ноя.", "Дек."],
    today: "Today",
    clear: "Clear",
    format: "mm/dd/yyyy",
    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
    weekStart: 0
};

window.apiCall = function (url, data, ondone, onfail) {
	$.ajax({
		method: "POST",
		url: url,
		data: { data: data, timezone: date_now.getTimezoneOffset() },
		dataType: 'json'
	})
	.done(function(data) {
		if(ondone) ondone(data);
		if(data.redirect) {
			window.location.href = data.redirect;
		}
	})
	.fail(function( jqXHR, textStatus) {
		if(onfail) onfail(textStatus);
	});
}

window.toTimeValue = function(value) {
	if(value && value.length == 5) {
		var time = value.split(':');
		return parseInt(time[0]) * 3600 + time[1] * 60;
	}
	return null;
}

window.fromTimeValue = function(time, def) {
	if(time) {
		var h = Math.floor(time / 3600);
		h = h.toString();
		if(h.length == 1)
			h = '0' + h;
			
		var m = Math.ceil((time % 3600) / 60);
		m = m.toString();
		if(m.length == 1)
			m = '0' + m;

		return h + ':' + m;
	}
	return def;
}

window.datePickerToDate = function(value) {
	var value_parts = value.split('/');
	var value_date = new Date();
	value_date.setFullYear(parseInt(value_parts[2]));
	value_date.setMonth(parseInt(value_parts[1]) - 1);
	value_date.setDate(parseInt(value_parts[0]));
	value_date.setHours(0);
	value_date.setMinutes(0);
	value_date.setSeconds(0);
	return Math.floor(value_date.getTime() / 1000);
}

window.datePickerFromDate = function(timestamp) {
	var value = new Date();
	value.setTime(timestamp * 1000);
	return value.getDate() + '/' + (value.getMonth() + 1) + '/' + value.getFullYear();
}

window.nowToTimestamp = function() {
	var value_date = new Date();
	return Math.floor(value_date.getTime() / 1000);
}

window.dateToTimestamp = function(date, time) {
	var date_parts = date.split('/');
	var time_parts = time.split(':');
	
	var value_date = new Date();
	value_date.setFullYear(parseInt(date_parts[2]));
	value_date.setMonth(parseInt(date_parts[1]) - 1);
	value_date.setDate(parseInt(date_parts[0]));
	value_date.setHours(parseInt(time_parts[0]));
	value_date.setMinutes(parseInt(time_parts[1]));
	value_date.setSeconds(0);
	return Math.floor(value_date.getTime() / 1000);
}

window.timestampToDate = function(timestamp, utc) {
	var value = new Date();
	value.setTime(timestamp * 1000);
	
	var date = '';
	date += value.getDate() < 10 ? '0' + value.getDate() : value.getDate();
	date += '/';
	date += (value.getMonth() + 1) < 10 ? '0' + (value.getMonth() + 1) : (value.getMonth() + 1);
	date += '/';
	date += value.getFullYear();

	var time = '';
	time += value.getHours() < 10 ? '0' + value.getHours() : value.getHours();
	time += ':';
	time += value.getMinutes() < 10 ? '0' + value.getMinutes() : value.getMinutes();
	return [date, time];
}

$(function(){
	$('[data-toggle=tooltip]').tooltip();
	
	$('ul.dropdown-language a').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		var current = $.cookie("language");
		if(current != $(this).attr('href')) {
			$.cookie("language", $(this).attr('href'), {expires: 365, path: '/'});
			window.location.reload();
		}
		else {
			$(this).closest('li.dropdown').removeClass('open');
		}
	});
});
/*
$(document).ready(function() {
	$('a').click(function(event) {
		if($(event.currentTarget).data('confirm') != null) {
			event.preventDefault();
			event.stopPropagation();
		
			if(confirm($(event.currentTarget).data('confirm'))) {
				window.location.href=$('#del-btn').attr('href');
			}
		}
	});
	
});*/