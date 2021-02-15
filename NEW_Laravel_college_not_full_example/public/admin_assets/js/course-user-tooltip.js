$(document).ready(function() {
    $('.tip').each(function () {
        $(this).tooltip(
            {
                html: true,
                title: $('#' + $(this).data('tip')).html()
            }).on('shown.bs.tooltip', function () {
            $('#tooltip-diploma').html($(this).attr('tooltip-diploma'));
            $('#tooltip-mobile').html($(this).attr('tooltip-mobile'));
            $('#tooltip-facebook').html($(this).attr('tooltip-facebook'));
            $('#tooltip-instagram').html($(this).attr('tooltip-insta'));
        });
    });
});