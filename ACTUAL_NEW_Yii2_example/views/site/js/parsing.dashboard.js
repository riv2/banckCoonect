$(function () {

    var swarms = JSON.parse($('#swarms').val());
    var filterEl = $('#parsingNameFilter');

    console.log(swarms);

    $.each(swarms, function (i,swarm) {
        function refresh() {
            $.ajax({
                url: 'http://'+swarm.id+':4000',
                type: 'get',
                dataType: 'json',
                success: function (json) {
                    renderDroids(swarm, json);
                    console.log(json);
                    setTimeout(refresh, 6000);
                },
                error: function () {
                    setTimeout(refresh, 6000);
                },
                timeout: 4000
            });
        }
        if (swarm) {
            console.log(swarm);
            refresh();
        }
    });

    function fsu(bytes){
        if      (bytes>=1073741824) {bytes=(bytes/1073741824).toFixed(2)+' GB';}
        else if (bytes>=1048576)    {bytes=(bytes/1048576).toFixed(2)+' MB';}
        else if (bytes>=1024)       {bytes=(bytes/1024).toFixed(2)+' KB';}
        else if (bytes>1)           {bytes=bytes+' bytes';}
        else if (bytes==1)          {bytes=bytes+' byte';}
        else                        {bytes='0 byte';}
        return bytes;
    }

    var $droids = $('#droids');
    var $droidTemplate = $('#droid-template');
    var $droidTemplate2 = $('#droid-template2');



    function renderDroids(swarm, droids) {
        var toRemove = {};
        $droids.find('[data-s="'+swarm.id+'"][data-droid-id]').each(function (i, droid) {
            toRemove[$(this).attr('data-droid-id')] = $(this);
        });

        droids.sort(function(a,b) {
            if (a.parsingName < b.parsingName)
                return -1;
            if (a.parsingName > b.parsingName)
                return 1;
            return 0;
        });

        var filterName = filterEl.val();
        $.each(droids, function (i, droid) {
            if (droid) {
                if (filterName && droid.info && !droid.info.parsingName.includes(filterName)) {
                    return;
                }
                renderDroid(swarm, droid);
                if (toRemove[droid.id]) {
                    delete toRemove[droid.id];
                }
            }
        });
        $.each(toRemove, function (droidId, $droid) {
            $droid.remove();
        });
    }

    var locKeys = {
        'price': 'Цена',
        'out_of_stock': 'Отсутствует',
        'competitor_item_sku': 'Артикул',
        'competitor_item_name': 'Название',
    };

    function localizeKey(k) {
        if (locKeys.hasOwnProperty(k)) {
            return locKeys[k];
        }
        return k;
    }

    function renderDroid(swarm, droid) {

        var $droid  = $droids.find('[data-droid-id="'+droid.id+'"][data-s="'+swarm.id+'"]');
        if ($droid.length === 0) {
            $droid = $droidTemplate.clone();
            $droid.attr('id','');
            $droid.addClass('d-droid');
            $droid.attr('data-droid-id',droid.id);
            $droid.attr('data-s',swarm.id);
            $droid.find('td').css('border: 2px solid '+swarm.color);
            $.ajax({
                url: '/api/parsing/info',
                data: {
                    id: droid.parsingId,
                    columns: ['global_count', 'created_at'],
                },
                type: 'get',
                dataType: 'json',
                success: function (parsingInfo) {
                    if (parsingInfo) {
                        $droid.find('td.global_count').text(parsingInfo.global_count);
                        $droid.find('td.created_at').text(parsingInfo.created_at);
                    }
                },
                timeout: 4000
            });
            $droids.append($droid.show());
            $droid.find('.d-cancel').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $parent = $(this).parents('[data-droid-id]');
                if ($parent.hasClass('stopping')) {
                    return false;
                }
                $.ajax({
                    url: '/api/droid/parsing-chain?id=' + droid.parsingId,
                    type: 'get',
                    dataType: 'json',
                    success: function (names) {
                        if (confirm("Вы уверены что хотите отменить текущий парсинг и все его вариации в очереди?\n\n" + Object.values(names).join("\n"))) {
                            $.ajax({
                                url: '/site/cancel-parsing?id=' + droid.parsingId,
                                type: 'get',
                                dataType: 'json',
                                success: function (json) {
                                    console.log(json);
                                    for (let id in names) {
                                        let droidContainer = $('.d-parsingId:contains("' + id +'")')
                                            .parents('[data-droid-id]');
                                        if (droidContainer.length > 0) {
                                            console.log(droidContainer);
                                            droidContainer.addClass('stopping').css('opacity', 0.5);
                                            let swarmName = droidContainer.find('.d-ip').text().trim();
                                            $.ajax({
                                                url: 'http://' + swarms.find(function (a){return a.name === swarmName}).id
                                                    + ':4000/kill/' + droidContainer.attr('data-droid-id'),
                                                type: 'get',
                                                dataType: 'json',
                                                success: function (json) {
                                                    console.log(json);
                                                }
                                            });
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
                return false;
            });

            $droid.find('.d-stop').on('click', function (e) {
                e.preventDefault();
                var $parent = $(this).parents('[data-droid-id]');
                if ($parent.hasClass('stopping')) {
                    return false;
                }
                $parent.addClass('stopping').css('opacity', 0.5);
                $.ajax({
                    url: 'http://' + swarm.id +':4000/kill/' + droid.id,
                    type: 'get',
                    dataType: 'json',
                    success: function (json) {
                        console.log(json);
                        $.ajax({
                            url: '/api/droid/free?id=' + droid.parsingId,
                            type: 'get',
                            dataType: 'json',
                            success: function (json) {
                                console.log(json);
                            }
                        });
                    }
                });
                return false;
            });
        }


        $droid.find('td').css('border: 2px solid '+swarm.color);
        var $tbody = $droid.find('tbody');


        var $lp = $droid.find('.d-lastParsedItem').html('');

        if (droid.info) {
            $droid.find('a').each(function () {
                if ($(this).attr('data-href')) {
                    $(this).attr('href', $(this).attr('data-href').replace('{ppi}', droid.info.parsingProjectId).replace('{pi}', droid.parsingId));
                }

            });
            $.each(droid.info, function (key,val) {
                if (key !== 'lastParsedItem') {
                    $droid.find('.d-'+key).html(val);

                    if (key === 'parsingName') {
                        $droid.find('.d-'+key).html('<a href="/parsing-project/update?id=' + droid.info.parsingProjectId + '" target="_blank">'+val+'</a>');
                    }
                }
            });
            if (droid.info.lastParsedItem) {
                $.each(droid.info.lastParsedItem, function (k, v) {

                    if (k === 'item_id' || k === 'competitor_id') {
                        return true;
                    }
                    if (k === 'url' || k === 'competitor_item_url') {
                        $lp.append('<a href="' + v + '" target="_blank">Ссылка</a>');
                        return true;
                    }

                    var $e = $('<span style="white-space: nowrap; "><span>' + localizeKey(k) + ':</span> <strong style="white-space: normal; word-break: break-all; ">' + v + '</strong></span>');
                    $lp.append($e);
                    $lp.append(' ');
                    if (k === 'item_id' || k === 'competitor_id') {
                        $e.css('font-size', '70%');
                    }
                });
            }
            var progressPercent = droid.info.tasksProcessed * 100 / droid.info.tasksInQueue;
            $droid.find('.progress .progress-bar-primary')
                .css({'width': progressPercent + '%', 'overflow':'hidden'})
                .html(Math.ceil(progressPercent) + '%');
            var $status = $droid.find('.d-status');
            if (droid.info.status !== 'Дроид работает') {
                $status.css('color','orange');
                $status.css('font-weight','bold');
            } else {
                $status.css('color','');
                $status.css('font-weight','');
            }
            $status.html(droid.info.status);
            if (droid.info.vpnName) {
                $droid.find('.d-proxiesCount').hide();
                $droid.find('.d-vpnName').text(droid.info.vpnName).show();
            }
            if (droid.info.proxyName) {
                $droid.find('.d-proxyName').text(' - ' + droid.info.proxyName).show();
            }
        }
        var $ip = $droid.find('.d-ip');
        $ip.html('<span style="color: '+swarm.color+';"><i class="fa fa-android"></i> '+swarm.name + '</span>');
        var $parsingId = $droid.find('.d-parsingId');
        $parsingId.html(droid.parsingId);

        if (droid.noVncPort) {
            $droid.addClass('v-droid');
            $droid.css('background','#ddd!');
            $droid.find('.d-usage').html('<a href="http://' + swarm.id + ':' + droid.noVncPort + '/?password=' + droid.parsingId + '" target="_blank"><i class="fa fa-eye"></i> Смотреть</a>');
        }

        if (typeof droid.usage !== 'undefined' && droid.usage ) {
            var $usage = $droid.find('.d-usage');
            var cpu = Math.ceil(droid.usage.cpu * 10000) / 10000;
            var usageStr = 'CPU: ' + cpu + '% | Память: '+fsu(droid.usage.memory.usage)+' | Сеть: <i class="fa fa-download"></i> '+fsu(droid.usage.network.in_bytes)+' / <i class="fa fa-upload"></i> '+fsu(droid.usage.network.out_bytes);
            usageStr = 'Память: '+fsu(droid.usage.memory.usage);
            $usage.html(usageStr);
        }

        return $droid;
    }

    function refreshParsingTab() {
        $.ajax({
            success: function (html) {
                let content = $(html).find('#parsing-tab-body');
                if (content) {
                    $('#parsing-tab-body').html(content[0].innerHTML);
                }
                setTimeout(refreshParsingTab, 6000);
            },
            error: function () {
                setTimeout(refreshParsingTab, 6000);
            },
            timeout: 10000
        });
    }

    refreshParsingTab();

    // $('#parsingNameFilter').on('focusout', function (e) {
    //     renderDroids();
    // });
});