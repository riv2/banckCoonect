var pricing = (function($){
    
    this.toggleLoadingState = function($el) {
        return $($el).html('<div class="text-center margin40"><span class="fa fa-spinner fa-spin page-loading-indicator"></span></div>');
    };
    
    return this;
})(jQuery);

// Tasks Tray
var $tasksTray ;
var $tasksCount;
// File Tray
var $fileExchangeTray;

$(function(){

    $('a[target="_blank"]').on('click',function (e) {
        e.stopPropagation();
        e.preventDefault();
        window.open($(this).attr('href'));
        return false;
    });

    $('.flex-height').each(function () {
        var fhel = this;
        var adjustHeight = function () {
            $(fhel).css('height', $(window).height() - $(fhel).position().top - 40);
        };
        $(window).resize(adjustHeight);
        adjustHeight();
    });

    $('.scroll-wrapper').each(function(){
        var $t              = $(this);
        var $upperScroll    = $(this).prev('.upper-scroll');
        $upperScroll.on('scroll', function (e) {
            $t.scrollLeft($upperScroll.scrollLeft());
        });
        $t.on('scroll', function (e) {
            $upperScroll.scrollLeft( $t.scrollLeft());
        });
        setInterval(function () {
            $upperScroll.find('.upper-scroll-dummy').css('width', $t.find('table').width());
        },100);
    });

    $(document.body).on('dragover dragenter', function() {
        $(this).addClass('file-drag-over-window');
    })
        .on('dragleave dragend drop', function() {
            $(this).removeClass('file-drag-over-window');
        });

    $.notifyDefaults({
        placement: {
            from: "bottom",
            align: "right"
        },
        template: '<div data-notify="container" class="col-xs-11 col-sm-4 col-md-3 col-lg-2 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'
    });

    // Tasks Tray
    $tasksTray = $('#tasks-tray');
    $tasksCount = $tasksTray.find('.tasks-count');
    // File Tray
    $fileExchangeTray = $('#file_exchange-tray');

    if (window.fileExchange != undefined && window.fileExchange) {
        updateFileExchangeTray(window.fileExchange);
    }
    if (window.pricingTasks != undefined && window.pricingTasks) {
        updateTasksTray(window.pricingTasks);
    }

    $('.reboot-droid').on('click', function () {

        if (confirm('Вы правда хотите физически перезагрузить этот сервер с дроидами?')) {
            var droid = $(this).attr('data-droid');

            $.ajax({
                url: 'http://' + droid + ':4000/reboot',
                type: 'get',
                dataType: 'json',
                success: function (json) {
                    $.notify(json.log, {type: 'success'});
                    console.log(json);
                }
            });
        }
    });

    $('#update-robots').on('click', function () {

        var swarms = $(this).attr('data-swarms').split(',');

        for(var s = 0; s < swarms.length ; s++ ){
            $.ajax({
                url: 'http://' + swarms[s] + ':4000/upgrade',
                type: 'get',
                dataType: 'json',
                success: function (json) {
                    $.notify(json.log, { type: 'success' });
                    console.log(json);
                }
            });
        }

    });

});


if (ws != undefined && window.wsOptions != undefined) {
    ws.init(window.wsOptions);

    console.log('WS INIT');
    ws.addHandler('fileExchangeUpdated', function (data) {
        var obj = {};
        console.log(data);
        obj[data.FileExchange.id] = data.FileExchange;
        updateFileExchangeTray(obj);
    });

    ws.addHandler('taskUpdated', function (data) {
        var title = '';
        if (typeof window.pricingTaskTypes[data.Task.task_type_id] != 'undefined') {
            title = '<i class="fa '+window.pricingTaskTypes[data.Task.task_type_id].icon+'"></i> ' + window.pricingTaskTypes[data.Task.task_type_id].name;
        }
        title += ' ' + data.Task.name;
        if (data.action === 'started') {
            $.notify("<h5>Старт</h5>"+title);
        }
        if (data.action === 'finished') {
            $.notify("<h5>Успешно</h5>"+title, { type: 'success' });
        }
        if (data.action === 'failed') {
            $.notify("<h5>Неудача</h5>"+title, { type: 'danger' });
        }
        if (data.action === 'canceled') {
            $.notify("<h5=>Отменено</h5>"+title, { type: 'danger' });
        }
        var obj = {};
        obj[data.Task.id] = data.Task;
        updateTasksTray(obj);
    });


    // ws.addHandler('robotInfo', function (data) {
    //     var info = data.message;
    //     updateRobotInfo(info);
    // });

    var $testParsing = $('#test-parsing');

    ws.addHandler('testParsingReady', function (data) {
        var parsingMessage = data.message;
        var $item = renderParsed(parsingMessage);

        if (parsingMessage.error) {
            $item.addClass('danger');
        } else {
            $item.addClass('success');
        }
        $testParsing.slideDown();
        $item.hide();
        $testParsing.prepend($item);
        $item.slideDown();
    });
    
    $('.close', $testParsing).on('click', function () {
        $('#test-parsing').hide();
    });


}

var anchor = (function(){

    function parseQuery(qstr) {
        var query = {};
        var a = qstr.substr(1).split('&');
        for (var i = 0; i < a.length; i++) {
            var b = a[i].split('=');
            if (b == "") continue;
            query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
        }
        return query;
    }

    this.get = function(name){
        var request = parseQuery(window.location.hash);
        if (typeof request[name] != 'undefined') {
            return request[name];
        } else {
            return null;
        }
    };

    this.set = function(name, value){
        var request = parseQuery(window.location.hash);
        request[name] = value;
        window.location.hash = '#' + $.param(request);
    };

    return this;
})();

var queryParams = (function(){

    function parseQuery(qstr) {
        var query = {};
        var a = qstr.substr(1).split('&');
        for (var i = 0; i < a.length; i++) {
            var b = a[i].split('=');
            if (b == "") continue;
            query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
        }
        return query;
    }

    this.get = function(name){
        var request = parseQuery(window.location.search);
        if (typeof request[name] != 'undefined') {
            return request[name];
        } else {
            return null;
        }
    };


    this.set = function(name, value){
        var request = parseQuery(window.location.search);
        request[name] = value;
        window.history.replaceState(null, null, window.location.pathname + '?' + $.param(request));
    };

    return this;
})();

function updateTasksTray(tasksData) {
    window.pricingTasks = $.extend(window.pricingTasks, tasksData);
    var activeCount = 0;
    for (i in window.pricingTasks) {
        if (window.pricingTasks[i].task_status_id < 6) {
            activeCount++;
        }
    }
    $tasksCount.html(" "+activeCount+" ");
    if (activeCount > 0) {
        $tasksTray.show();
        $tasksCount.show();
    } else {
        $tasksCount.hide();
    }

    $.each(tasksData, function (i) {
        var task    = this;
        var $a      = $('<a></a>');
        var $li     = $('<li></li>');

        var title   = (typeof task.requester_name == 'undefined')?task.name:task.requester_name;
        var percent = Math.ceil(task.progress * 100 / task.total);
        var infoStr = '';
        var dateStr = '';
        if (this.started_at) {
            dateStr += 'Старт:' + extractDate(this.started_at).toLocaleTimeString();
        }
        if (this.finished_at) {
            dateStr += '.  Готово:' + extractDate(this.finished_at).toLocaleTimeString();
        }
        if (task.task_status_icon) {
            title   = '<i class="fa '+task.task_status_icon+'"></i>' + title;
        }
        if (task.total && task.progress <= task.total) {
            infoStr += task.progress+'/'+task.total;
            if (task.task_status_id >= 6) {
                infoStr += '<i class="fa fa-flag-checkered pull-right"></i>';
            } else {
                infoStr += '<small class="pull-right">' + percent + '%</small>';
            }
        } else if (task.progress) {
            if (task.task_status_id >= 6) {
                infoStr += task.progress + '<i class="fa fa-flag-checkered pull-right"></i>';
            } else {
                infoStr += '<i class="fa fa-spin fa-spinner" style="color: #0d6aad;"></i> ' + task.progress;
            }
        }
        if (task.task_status_id <= 2) {
            infoStr = 'В очереди';
            if (task.total) {
                infoStr += ' (' + task.total + ')';
            }
        } else if (task.task_status_id >=6) {
            $li.css('opacity', 0.6);
        }

        var $h3     = $('<h3>'+title+'</h3>');
        var $info   = $('<div>'+infoStr+'</div>');
        var $btnCancel = $('<i class="fa fa-times-circle pull-right" style="color: #a94442; cursor: pointer;"></i>');

        var color = 'aqua';
        if (task.task_type_id == 8) {
            color = 'yellow';
        }
        if (task.task_type_id == 7) {
            color = 'green';
        }
        var $progressBar = $('<div class="progress xs"><div class="progress-bar progress-bar-'+color+'" style="width: '+percent+'%;"><span class="sr-only">'+task.progress+'/'+task.total+' </span></div></div>');

        $li.append($a.append($h3).append($info)).attr('data-task-id', task.id);

        if (task.task_status_id < 6) {
            //if (!task.is_external) {
            $h3.append($btnCancel);
            $btnCancel.on('click', function () {
                $.ajax({
                    'url': '/task/cancel?id=' + task.id,
                    'type': 'post',
                    'success': function () {

                    }
                });
            });
            //}
            if (task.task_status_id > 2 && percent <= 100) {
                $a.append($progressBar);
            }
        }

        if (dateStr) {
            $a.append('<div><small>'+dateStr+'</small></div>');
        }

        var $taskType = $tasksTray.find('[data-task-task_type_id="'+task.task_type_id+'"]');
        $taskType.show();

        var $existing = $tasksTray.find('[data-task-id="'+task.id+'"]');
        if ($existing && $existing.length > 0) {
            $existing.replaceWith($li);
        } else {
            $taskType.find('.tasks-list').prepend($li);
        }
        $tasksTray.show();
    });

}

function updateFileExchangeTray(fileExchangeData) {
    window.fileExchange = $.extend(window.fileExchange, fileExchangeData);

    $.each(fileExchangeData, function () {
        var str = '';
        var $a = $('<a>'+this.name+'</a>');
        var fileExchange = this;
        var date = '';
        if (fileExchange.created_at) {
            date = extractDate(fileExchange.created_at).toLocaleTimeString();
        }

        $a.attr('title', fileExchange.name + ' ' + date);

        $a.css('position','relative');
        var $close = $('<span class="btn btn-danger"><i class="fa fa-times"></i></span>');
        $close.css({
            position: 'absolute',
            right: 0,
            top: 0,
            padding: 1,
            lineHeight: 0
        });
        $close.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $.ajax({
                url: '/site/file-dismiss/?id=' + fileExchange.id,
                type: 'get',
                success: function() {
                    $a.remove();
                }
            });
            return false;
        });

        if (fileExchange.task_status_id <= 3) {
            $a.addClass('processing');
            var $cancelBtn = $('<button  class="btn btn-xs btn-danger" style="position: absolute; right: 1px; top: 1px; font-size: 12px; padding: 0px 2px; line-height:  14px;">x</button>');
            $cancelBtn.on('click',function(e){
                e.stopPropagation();
                e.preventDefault();
                $.get('/task/cancel-file-exchange?id='+fileExchange.id);
                return false;
            });
            $a.append($cancelBtn);
            str += date;
        }
        else if (fileExchange.task_status_id > 4 && fileExchange.task_status_id < 8) {
            $a.append($close);
            if (fileExchange.is_export) {
                $a.addClass('download');
                $a.attr('href', '/site/download/?id='+this.id);
                $a.attr('target', '_blank');
                $a.on('click', function () {
                    $a.fadeOut();
                });
            } else {
                $a.addClass('ready');
            }
            if (fileExchange.is_export) {
                if (fileExchange.file_size) {
                    str += formatBytes(fileExchange.file_size);
                }
            } else {
                if (fileExchange.rows_imported) {
                    str += parseInt(fileExchange.rows_imported||0) +'/'+(parseInt(fileExchange.rows_imported||0)+parseInt(fileExchange.rows_failed||0));
                }
            }
        }
        else if (fileExchange.task_status_id === 8) {
            $a.append($close);
            $a.addClass('error');
            $a.attr('href', '/crud-file-exchange/view?id='+this.id);
            $a.attr('target', '_blank');
            if (!fileExchange.is_export) {
                if (fileExchange.rows_failed) {
                    str += parseInt(fileExchange.rows_imported||0) +'/'+(parseInt(fileExchange.rows_imported||0)+parseInt(fileExchange.rows_failed||0));
                }
            }
        }
        if (fileExchange.is_export) {
            if (fileExchange.rows_imported) {
                str += '<span class="pull-right">'+parseInt(fileExchange.rows_imported||0) +'</span>';
            }
        }

        $a.append('<br/>');
        $a.append('<span>'+str+'</span>');
        $a.addClass('file_exchange-file');
        $a.attr('data-file_exchange-id', fileExchange.id);



        var $existing = $fileExchangeTray.find('[data-file_exchange-id="'+fileExchange.id+'"]');
        if ($existing && $existing.length > 0) {
            $existing.replaceWith($a);
            if (this.is_export && this.status_id != 0) {
                $a.fadeOut(function () {
                    $(this).remove()
                });
            }
        } else {
            $fileExchangeTray.prepend($a);
        }
    });
}

function formatBytes(bytes,decimals) {
    if(bytes == 0) return '0 Byte';
    var k = 1000; // or 1024 for binary
    var dm = decimals + 1 || 2;
    var sizes = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function extractDate(date) {
    if (typeof date.date != 'undefined') {
        date = date.date.split('.')[0];
    }
    return new Date(date);
}



function iframeLoaded() {
    $('iframe').each(function(){
        var $iframe = $(this);
        setTimeout(function () {
            var height = $iframe.contents().height();
            if (height > 200) {
                $iframe.css('height', height);
            }
        },100);

    });
}

/**
 *
 *  Base64 encode / decode
 *  <a href="http://www.webtoolkit.info/" title="http://www.webtoolkit.info/" class="liexternal">http://www.webtoolkit.info/</a>
 *
 **/

var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }
};


var  parsedFieldNames = {
    'bounds'                : 'Границы товара',
    'out_of_stock'          : 'Отсутствует',
    'price'                 : 'Цена',
    'competitor_item_sku'   : 'Артикул',
    'competitor_item_name'  : 'Наименование',
    'competitor_shop_name'  : 'Магазин',
    'competitor_item_count' : 'Количество',
    'competitor_item_rubric1'   : 'Рубрика1',
    'competitor_item_rubric2'   : 'Рубрика2',
    'competitor_item_seller'    : 'Продавец',
    'competitor_item_brand'   : 'Бренд',
    'delivery'              : 'Доставка',
    'proxy'                 : 'Прокси',
    'time_ms'               : 'Время (мс.)',
    'mask_name'             : 'Маска',
    'item_id'               : 'ID товара'
};

var testResponses = [];
var ANON_URL= '';
function renderParsed(parsedData) {
    var html = '';
    var allParsed = parsedData.items;
    var tableBody = '';
    var tableHead = '';
    var tableMap = [];
    var count = 0;
    var url = "<a href='" +ANON_URL + parsedData.url + "' target='_blank'>"+parsedData.url+"</a>";

    testResponses.push(parsedData.body);
    if (parsedData.error) {
        // testResponses.push(parsedData.body ? parsedData.body.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        //     return '&#'+i.charCodeAt(0)+';';
        // }) : null);
        html = '<table>' +
            '<thead><tr><th>Маска ('+allParsed.length+')</th><th class="text-danger">Ошибка</th><th>Ответ</th><th>Прокси</th></tr></thead>' +
            '<tbody><tr>' +
            '<td>'+parsedData.attributes.mask_name+'</td>' +
            '<td class="text-danger">'+parsedData.error.message + ' ' + JSON.stringify(parsedData.error.info) + '</td>' +
            '<td><a href="#" onclick="var oMyBlob = new Blob([testResponses['+(testResponses.length-1)+']], {encoding:\'UTF-8\',type : \'text/plain;charset=UTF-8\'}); window.open(window.URL.createObjectURL(oMyBlob)); return false;">Тело ответа</a></td>' +
            '<td>'+ (parsedData.stats.proxy || '' )+'</td>' +
            '</tr>' +
            '<tr><td colspan="4">'+url+'</td></tr></tbody></table>';
    } else {
        $.each(allParsed, function (i, parsed) {
            parsed.proxy = parsedData.stats.proxy;
            $.each(parsed, function (maskId, value) {
                if (tableMap.indexOf(maskId) < 0) {
                    if (maskId != 'url' && maskId != 'item_id' && maskId != 'mask_name' && value!='test') {
                        tableMap.push(maskId);
                        var field = typeof parsedFieldNames[maskId] != 'undefined' ? parsedFieldNames[maskId] : maskId;
                        var w = maskId == 'competitor_item_name' ? ' width="100%" ' : 'width="1"';
                        tableHead += '<th data-mask="' + maskId + '" '+w+'>' + field + "</th>";
                    }
                }
            });
            if (parsed.mask_name && tableMap.indexOf('mask_name') < 0) {
                tableMap.unshift('mask_name');
                var field = typeof parsedFieldNames['mask_name'] != 'undefined' ? parsedFieldNames['mask_name'] : 'Маска';
                tableHead = '<th data-mask="mask_name" width="100">' + field + "</th>" + tableHead;
            }
        });
        console.log(allParsed);

        tableHead = "<thead></tr><tr>" + tableHead + "</tr></thead>";
        $.each(allParsed, function (i, parsed) {
            var tr = '';
            $.each(tableMap, function (i, maskId) {
                if (typeof parsed[maskId] != 'undefined') {
                    var val = parsed[maskId];
                    if (val === true) {
                        val = 'Да';
                    }
                    if (val === false) {
                        val = 'Нет';
                    }
                    if (val === null) {
                        val = '';
                    }
                    tr += '<td style="white-space: pre-wrap;">' + val + '</td>';
                } else {
                    tr += '<td></td>';
                }
            });
            var c = 0;
            $.each(parsed, function () {
                c++;
            });
            if (c > 0) {
                tableBody += '<tr>' + tr + '</tr>';
                count++;
            }
        });

        if (tableBody) {
            tableBody = "<tbody>" + tableBody + "<tr><td colspan='" + tableMap.length + "'>" + url +"</td></tbody>";
            html = '<table>' + tableHead + tableBody + '</table>';
        } else {
            html = '<table><tbody><tr><td>Ничего не найдено</td>' +
                '<td><a href="#" onclick="var oMyBlob = new Blob([testResponses['+(testResponses.length-1)+']], {encoding:\'UTF-8\',type : \'text/plain;charset=UTF-8\'}); window.open(window.URL.createObjectURL(oMyBlob)); return false;">Тело ответа</a></td>' +

                '</tr><tr><td>' + url + '</td><td></td></tr></tbody></table>';
        }
    }

    return $('<div class="test-parsing-item">' + html + '</div>');
}