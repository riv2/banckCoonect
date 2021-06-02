var scheduleCalendar = {
    $calendar: function(){
        return $('#calendar');
    },
    $dragging: null,
    _events: [],
    _cached: false,
    _a: 'sfsd',
    events: function(start, end, timezone, callback) {

        //
        // if (!scheduleCalendar._cached) {
            var type            = scheduleCalendar.$calendar().attr('data-type') ? scheduleCalendar.$calendar().attr('data-type') : 'weekly';
            var requesterType   = scheduleCalendar.$calendar().attr('data-requester_entity_id') ? scheduleCalendar.$calendar().attr('data-requester_entity_id') : '';

            $.ajax({
                url: '/schedule/' + type + '/?type=' + requesterType,
                dataType: 'json',
                data: {
                    start: start.unix(),
                    end: end.unix()
                },
                success: function (json) {
                    scheduleCalendar._events = json;
                    var events = [];
                    $.each(scheduleCalendar._events, function (i,event) {
                        event.backgroundColor = $('.schedule-actor[data-requester_id="'+event.source+'"]').attr('data-color');
                        event.borderColor = '#000000';
                        event.textColor = '#FFFFFF';
                        var checkbox = $('.schedule-toggle[data-requester_id="'+event.source+'"]');
                        if ((checkbox.length > 0 && checkbox.prop('checked')) || checkbox.length == 0) {
                            events.push(event);
                        }
                    });
                    callback(events);
                }
            });
        // } else {
        //     var events = [];
        //     $.each(scheduleCalendar._events, function (i,event) {
        //         event.backgroundColor = $('.schedule-actor[data-requester_id="'+event.source+'"]').attr('data-color');
        //         event.borderColor = '#555555';
        //         var checkbox = $('.schedule-toggle[data-requester_id="'+event.source+'"]');
        //         if ((checkbox.length > 0 && checkbox.prop('checked')) || checkbox.length == 0) {
        //             events.push(event);
        //         }
        //     });
        //     callback(events);
        // }
        //
        // scheduleCalendar._cached = false;
    },
    drop: function(moment, ev) {
        console.log(moment);
        console.log(ev);
        $.ajax({
            "url"       : '/schedule/add',
            "type"      : 'post',
            "dataType"  : 'json',
            "data"      : {
                'Schedule' : {
                    'requester_entity_id': scheduleCalendar.$calendar().attr('data-requester_entity_id'),
                    'requester_id'  : $(ev.target).attr('data-requester_id'),
                    'args'          : $(ev.target).attr('data-args'),
                    'time'          : moment.format('HH:mm:ss'),
                    'day'           : moment.format('e')*1+1 ,
                    'duration'      : $(ev.target).attr('data-duration'),
                    'function'      : $(ev.target).attr('data-function'),
                    'color'         : $(ev.target).attr('data-color'),
                    'name'          : $(ev.target).attr('data-title'),
                    'description'   : $(ev.target).attr('data-description'),
                }
            },
            "success" : function () {
                scheduleCalendar.$calendar().fullCalendar('refetchEvents');
            }
        });
    },
    eventClick: function(calEvent, jsEvent, view) {
        var $et = $('#event-tooltip');
        $et.appendTo(document.body);
        $et.find('.event-tooltip-title').html(calEvent.title);
        $et.find('.event-tooltip-time').html(moment(calEvent.start).format('HH:mm') + ' ' + moment(calEvent.start).format('dddd'));
        $et.css('z-index',9999);
        
        var removeTooltip = function () {
            $et.animate({
                'opacity': 0
            },100,'linear',function () {
                $(this).hide();
            });
            return false;
        };

        $et.find('.tooltip-close').off('click').on('click', removeTooltip);
        $et.find('.event-tooltip-delete').off('click').on('click', function () {
            removeTooltip();
            $.ajax({
                "url"       : '/schedule/delete?id=' + calEvent.id,
                "type"      : 'post',
                "dataType"  : 'json',
                "success" : function () {
                    scheduleCalendar.$calendar().fullCalendar('refetchEvents');
                }
            });
        });
        $et.find('.event-tooltip-make-daily').off('click').on('click', function () {
            removeTooltip();
            $.ajax({
                "url"       : '/schedule/propagate-to-week?id=' + calEvent.id,
                "type"      : 'post',
                "dataType"  : 'json',
                "success" : function () {
                    scheduleCalendar.$calendar().fullCalendar('refetchEvents');
                }
            });
        });

        $et.show().css({
            'left' : $(this).offset().left + $(this).width()/2 - $et.width()/2,
            'top'  : $(this).offset().top - $et.height() - 2,
            'opacity': 0
        }).animate({
            'opacity': 1
        });
        return false;
    },
    eventDrop: function(event,  delta, revertFunc, jsEvent, ui, view) {
        $.ajax({
            "url"       : '/schedule/update?id='+event.id,
            "type"      : 'post',
            "dataType"  : 'json',
            "data"      : {
                'Schedule': {
                    'id'        : event.id,
                    'time'      : event.start.format('HH:mm:ss'),
                    'day'       : event.start.format('e')*1+1 ,
                }
            },
            "success" : function () {
                scheduleCalendar.$calendar().fullCalendar('refetchEvents');
            }
        });
    },
    viewRender: function( view, element ){
        var now = moment();
        var classes = {1:'mon', 2: 'tue', 3: 'web', 4:'thu',5:'fri',6:'sat',7:'sun'};
        var day = 'fc-'+classes[moment().format('E')];
        var minutes = parseInt(moment().format('m'),10);
        var hours = moment().format('H');
        minutes = '00';
        var time = hours + ':' + minutes + ':' + '00';
        $(element).find('.fc-bg .fc-day').css('background','white');
        $(element).find('.fc-bg .fc-day.'+day).css('background','#FFFFEE');
        $(element).find('.fc-slats tr').css('border-bottom','');
        $(element).find('.fc-slats tr').css('border-top','');
        $(element).find('.fc-slats [data-time="'+time+'"]').css('border-top','1px solid green');
        $(element).find('.fc-slats [data-time="'+time+'"]').css('border-bottom','1px solid green');

    }
};

$(function () {
    $(document)

        .on('change','input.schedule-toggle-all', function () {
            $('input.schedule-toggle').prop('checked', $(this).prop('checked'));
            setTimeout(function () {
                scheduleCalendar._cached = true;
                scheduleCalendar.$calendar().fullCalendar('refetchEvents');
            },10);
        })
        .on('click','.schedule-actors .schedule-actor-clear', function (e) {
            var $actor      = $(this).parents('li').children('.schedule-actor');
            var title       =  $actor.attr('data-title');
            var source  = $actor.attr('data-requester_id');
            if (confirm('Вы правда хотите удалить из расписания все экземпляры "'+title+'" ?')) {
                $.ajax({
                    "url": '/schedule/delete?requester_id=' + source,
                    "type": 'post',
                    "dataType": 'json',
                    "success": function () {
                        scheduleCalendar.$calendar().fullCalendar('refetchEvents');
                    }
                });
            }
        })
        .on('click','.schedule-actors .schedule-actor-expand-collapse', function (e) {
            e.stopPropagation();
            e.preventDefault();
            if ($(this).hasClass('fa-circle')) {
                return false;
            }
            if ($(this).hasClass('fa-plus-square')) {
                $(this).removeClass('fa-plus-square');
                $(this).addClass('fa-minus-square');
                $(this).parents('li').children('ul').slideDown();
            } else {
                $(this).removeClass('fa-minus-square');
                $(this).addClass('fa-plus-square');
                $(this).parents('li').children('ul').slideUp();
            }
            return false;
        })
        .on('change', 'input.schedule-toggle', function () {
            setTimeout(function () {
                scheduleCalendar._cached = true;
                scheduleCalendar.$calendar().fullCalendar('refetchEvents');
            },10);
        })
        .on('mousedown.schedule', '.schedule-actor', function (e) {
            scheduleCalendar.$dragging = $('<div></div>');
            scheduleCalendar.$dragging.html($(this).attr('data-title'));
            scheduleCalendar.$dragging.addClass('schedule-actor-drag');
            scheduleCalendar.$dragging.addClass('ui-draggable');
            scheduleCalendar.$dragging.attr('data-title', $(this).attr('data-title'));
            scheduleCalendar.$dragging.attr('data-requester_id', $(this).attr('data-requester_id'));
            scheduleCalendar.$dragging.attr('data-args', $(this).attr('data-args'));
            scheduleCalendar.$dragging.attr('data-function', $(this).attr('data-function'));
            scheduleCalendar.$dragging.attr('data-duration', $(this).attr('data-duration'));
            scheduleCalendar.$dragging.attr('data-description', $(this).attr('data-description'));
            scheduleCalendar.$dragging.attr('data-color', $(this).attr('data-color'));

            $(document.body).append(scheduleCalendar.$dragging);

            scheduleCalendar.$dragging.css({
                "position"  : "absolute",
                "left"      : e.pageX - scheduleCalendar.$dragging.width() / 2,
                "top"       : e.pageY - scheduleCalendar.$dragging.height() / 2,
                "cursor"    : "move",
                "z-index"   : 9999,
                'padding'   : 5,
                'background-color' : $(this).attr('data-color')
            });

            e.type = 'dragstart';

            scheduleCalendar.$dragging.trigger(e);
        })
        .on('mousemove.schedule', function (e) {
            if (scheduleCalendar.$dragging) {
                scheduleCalendar.$dragging.css({
                    "left"      : e.pageX - scheduleCalendar.$dragging.width() / 2,
                    "top"       : e.pageY - scheduleCalendar.$dragging.height() / 2,
                });
            }
        })
        .on('mouseleave.schedule', function (e) {
            if (scheduleCalendar.$dragging) {
                e.type = 'dragstop';
                scheduleCalendar.$dragging.trigger(e);
                scheduleCalendar.$dragging.remove();
                scheduleCalendar.$dragging = null;
            }
        })
        .on('mouseup.schedule', function (e) {
            if (scheduleCalendar.$dragging) {
                e.type = 'dragstop';
                scheduleCalendar.$dragging.trigger(e);
                scheduleCalendar.$dragging.remove();
                scheduleCalendar.$dragging = null;
            }

        });
});