 $( document ).ready(function() {
var Site = $('#Site').val();



 $('.newsLink:first').click();






});


function newsshow (id) {
$('.hidden').hide();
$('#message'+id).fadeIn();
$('.newsLink').removeClass('active');
$('#link'+id).addClass('active');


} 
