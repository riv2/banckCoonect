 $( document ).ready(function() {
var Site = $('#Site').val();



 $('.faqLink:first').click();






});


function faqshow (id) {

$('.hidden').hide();
$('#message'+id).fadeIn();
$('.faqLink').removeClass('active');
$('#link'+id).addClass('active');


} 
