$(function(){

    var $modal = {
        'categories'    : $('#selectCategoriesModal'),
        'brands'        : $('#selectBrandsModal'),
        'items'         : $('#excludedItems'),
    };
    var $competitorsContainer = $('#competitors-rows');
    var $competitorsFilter = $('#competitors-filter');
    var $projectForm = $('#form-update-project');
    var $itemsListTemplate = $('#itemsListTemplate');

    function renderCompetitor(data) {
        var $projectCompetitorTemplate = $('#project_competitor-template');
        var $projectCompetitor = $projectCompetitorTemplate.clone();
        $projectCompetitor.attr('id','');
        $projectCompetitor.html($projectCompetitorTemplate.html().replace(/%competitor\-id%/gim, data.id));
        $projectCompetitor.attr('data-name', data.name);
        $projectCompetitor.attr('data-competitor_id', data.id);
        $projectCompetitor.find('.project_competitor-name').html(data.name);
        $projectCompetitor.find('.project_competitor-select').val(data.id);
        var $parseLink = $projectCompetitor.find('.project_competitor-go-parse');
        $parseLink.attr('href', $parseLink.attr('href') + '&competitors='+data.id)
        $projectCompetitor.show();

        $projectCompetitor.on('click', function(event){
            var $check = $projectCompetitor.find('.project_competitor-select');
            if ($(event.target).hasClass('brandsShow') || $(event.target).hasClass('categoriesShow') || $(event.target).hasClass('itemsShow')) {
                event.preventDefault();
                var entity = 'categories';
                if ($(event.target).hasClass('brandsShow')) {
                    entity = 'brands';
                } else if ($(event.target).hasClass('itemsShow')) {
                    entity = 'items';
                }
                $check.prop('checked', true);
                var $selected = $modal[entity].find('[name="select_'+entity+'_text"]');
                var $selected2 = $modal[entity].find('[name="select_'+entity+'"]');
                $modal[entity].modal('show');
                $modal[entity].attr('data-competitor_id', $(event.target).attr('data-competitor_id'));
                $modal[entity].find('.competitorName').html($projectCompetitor.attr('data-name'));
                var vals = $projectCompetitor.find('input[name*="'+entity+'Selected"]').get(0).value;
                vals = vals?vals.split(',').map(function (el) {
                    return el.trim();
                }):[];
                $selected.val('');
                $selected2.on('select2-selecting',function (a) {
                    var snames = $selected.val() ? $selected.val().split(',').map(function (el) {
                        return el.trim();
                    }) : [];
                    snames.push(a.object.name);
                    $selected.val(snames.join(', '));
                    $selected2.select2("val", "");
                });

                $.ajax({
                    "url":"/project/id-2-name",
                    "data": {
                        "ids": vals,
                        "type": entity
                    },
                    "type":"post",
                    "dataType":"json",
                    success: function(json){
                        $itemsListTemplate.parent().children().not('#itemsListTemplate').remove();
                        if (entity == 'items') {
                            for(let i = 0; i < json.length; i++) {
                                renderCompetitorItem(json[i].id, json[i].name);
                            }
                            $selected.val(json.map(function(value) {return value.name;}).join(', '));
                        } else {
                            $selected.val(json.join(', '));
                        }
                    }
                });

                return false;
            }
            updatePCTR($projectCompetitor, false);
        });
        return $projectCompetitor;
    }

    function renderCompetitorItem(id, name) {
        var el = $itemsListTemplate.clone();
        el.find('.item-name').text(name);
        el.find('input').val(id);
        el[0].removeAttribute('style');
        el[0].removeAttribute('id');
        el.find('.item-rmv').click(function () {
            el.remove();
            let val = $modal['items'].find('[name="select_items_text"]').val();
            $modal['items'].find('[name="select_items_text"]').val(val.replace(name, '').replace(name + ',', ''));
        });
        $itemsListTemplate.parent().append(el);
    }

    $.each(window.projectCompetitors, function(){
        var $projectCompetitor = renderCompetitor(this.competitor);
        $projectCompetitor.addClass('selected');
        $projectCompetitor.find('.project_competitor-select').prop('checked', true);
        $projectCompetitor.find('.project_competitor-key').prop('checked', this.is_key_competitor);
        $projectCompetitor.find('.project_competitor-status').prop('checked', this.status_id != 0);
        $projectCompetitor.find('.project_competitor-price_variation_modifier').val(this.price_variation_modifier);
        $projectCompetitor.find('.project_competitor-price_final_modifier').val(this.price_final_modifier);
        var brandsSelected = {"ids":[],"names":[], banned: false};
        var categoriesSelected = {"ids":[],"names":[], banned: false};
        var itemsSelected = {"ids":[],"names":[]};
        $.each(this.projectCompetitorBrands, function () {
            brandsSelected.ids.push(this.brand_id);
            brandsSelected.names.push(this.name);
            brandsSelected.banned = parseInt(this.status_id,10) > 0;
        });
        $.each(this.projectCompetitorCategories, function () {
            categoriesSelected.ids.push(this.category_id);
            categoriesSelected.names.push(this.name);
            categoriesSelected.banned = parseInt(this.status_id,10) > 0;
        });
        $.each(this.projectCompetitorItems, function () {
            itemsSelected.ids.push(this.item_id);
            itemsSelected.names.push(this.name);
        });
        $projectCompetitor.find('.project_competitor-brands_selected').val(brandsSelected.ids.join(','));
        $projectCompetitor.find('.project_competitor-brands_banned').val(brandsSelected.banned?'1':'');
        $projectCompetitor.find('.project_competitor-categories_selected').val(categoriesSelected.ids.join(','));
        $projectCompetitor.find('.project_competitor-categories_banned').val(categoriesSelected.banned?'1':'');
        $projectCompetitor.find('.project_competitor-items_selected').val(itemsSelected.ids.join(','));
        if (brandsSelected.names.length > 0) {
            $projectCompetitor.find('.brandsShow').html(brandsSelected.names.join(','));
        }
        if (categoriesSelected.names.length > 0) {
            $projectCompetitor.find('.categoriesShow').html(categoriesSelected.names.join(', '));
        }
        updatePCTR($projectCompetitor, true);
        $competitorsContainer.append($projectCompetitor);
    });


    function filterCompetitors() {
        var query = $competitorsFilter.val();
        $.ajax({
            'url' : window.indexUrls['Competitor'],
            'data' : {
                'search[name]' : query,
                'fields' : 'id,name'
            },
            'dataType':'json',
            'type':'get',
            'success': function(json){
                $.each(json.items,function(){
                    if ($competitorsContainer.find('[data-competitor_id="'+this.id+'"]').length == 0) {
                        $competitorsContainer.prepend(renderCompetitor(this));
                    }
                });

                var $good = [];
                if (query) {
                    $good = $competitorsContainer.find('tr.selected, tr[data-name*="' + query + '"]');
                } else {
                    $good = $competitorsContainer.find('tr.selected');
                }
                $competitorsContainer.find('tr').not($good).hide();
                $good.show();
            }
        })
    }

    $competitorsFilter.on('keyup', function () {
        setTimeout(filterCompetitors, 10);
    });

    $projectForm.submit(function () {
        $competitorsContainer.find('tr:not(.selected)').remove();
        return true;
    });

    $.each(['categories', 'brands', 'items'],function(){
        var entity = this;
        if (entity == 'items') {
            var $selected2 = $modal[entity].find('[name="select_items"]');
            $modal[entity].find('[name="select_items_text"]').on('focusout', function (e) {
                let data = e.target.value;
                let ids = data.match(/([a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12})/g);
                $.ajax({
                    "url":"/project/id-2-name",
                    "data": {
                        ids,
                        "type": 'items'
                    },
                    "type":"post",
                    "dataType":"json",
                    success: function(json){
                        let resultVal = data;
                        $('.items-list > div:not(#itemsListTemplate)').each(function (i, el) {
                            if (!data.includes($(el).find('.item-name').text())) {
                                el.remove();
                            }
                        });
                        for (let i = 0; i < json.length; i++) {
                            let itemData = json[i];
                            resultVal = resultVal.replace(itemData.id, itemData.name);
                            renderCompetitorItem(itemData.id, itemData.name);
                        }
                        e.target.value = resultVal;
                    }
                })
            });
            $selected2.on('change', function (e) {
                renderCompetitorItem(e.val, $('#s2id_select_items .select2-chosen').text());
                // $selected2.select2("val","");
            });
            $modal[entity].find('.selectOk').on('click', function (e) {
                e.preventDefault();
                $modal[entity].modal('hide');
                var id = $modal[entity].attr('data-competitor_id');
                var $tr = $competitorsContainer.find('tr[data-competitor_id="'+id+'"]');
                var itemsCount = $itemsListTemplate.parent().children().length - 1;
                if (itemsCount > 0) {
                    $tr.find('.' + entity + 'Show')
                        .removeClass('btn-default')
                        .removeClass('btn-success')
                        .addClass('btn-danger')
                } else {
                    $tr.find('.' + entity + 'Show')
                        .removeClass('btn-danger')
                        .removeClass('btn-success')
                        .addClass('btn-default')
                }
                $tr.find('.itemsShow').find('.items-count').text(itemsCount);
                var ids = [];
                $itemsListTemplate.parent().find('input.item-id').each(function (i, el) {
                    var val = el.value;
                    if (val) {
                        ids.push(val);
                    }
                })
                console.log(ids);
                $tr.find('[name*="'+entity+'Selected"]').val(ids.join(','));
            });
        } else {
            $modal[entity].find('.selectBan,.selectOk').on('click',function(e){
                e.preventDefault();
                var $selected2 = $modal[entity].find('[name="select_'+entity+'"]');
                var $selected = $modal[entity].find('[name="select_'+entity+'_text"]');
                var snames = $selected.val()?$selected.val().split(',').map(function (el) {
                    return el.trim();
                }):[];
                $modal[entity].modal('hide');
                var id = $modal[entity].attr('data-competitor_id');
                var $tr = $competitorsContainer.find('tr[data-competitor_id="'+id+'"]');
                var $btn = $(this);
                $.ajax({
                    "url":"/project/name-2-id",
                    "data": {
                        "names": snames,
                        "type": entity
                    },
                    "type":"post",
                    "dataType":"json",
                    success: function(ids){

                        $tr.find('[name*="'+entity+'Selected"]').val(ids.join(','));

                        if ($btn.hasClass('selectBan')) {
                            $tr.find('[name*="' + entity + 'Banned"]').val(1);
                        } else {
                            $tr.find('[name*="' + entity + 'Banned"]').val('');
                        }


                        if ($tr.find('[name*="' + entity + 'Selected"]').val()) {
                            if ($tr.find('[name*="' + entity + 'Banned"]').val()) {
                                $tr.find('.' + entity + 'Show').removeClass('btn-default').removeClass('btn-success').addClass('btn-danger').html(snames.join(', '));
                            } else {
                                $tr.find('.' + entity + 'Show').removeClass('btn-default').removeClass('btn-danger').addClass('btn-success').html(snames.join(', '));
                            }
                        } else {
                            $tr.find('.' + entity + 'Show')
                                .removeClass('btn-danger')
                                .removeClass('btn-success')
                                .addClass('btn-default')
                                .html(
                                    '<span class="glyphicon glyphicon-edit"></span>  '
                                    + ((entity==='brands')
                                    ? 'Бренды'
                                    : (
                                        entity === 'categories'
                                            ? 'Категории'
                                            : ('Исключенные товары (' + ids.length + ')')
                                    ))
                                );
                        }
                    }
                });

                $selected.val('');
                $selected2.select2("val","");
                return false;
            });
        }
    });

    function updatePCTR($tr, initial) {
        $tr = $($tr);

        $.each(['categories', 'brands', 'items'],function() {
            var entity = this;
            if (entity == 'items' && $tr.find('[name*="' + entity + 'Selected"]').val()) {
                let itemsCount = $tr.find('[name*="' + entity + 'Selected"]').val().split(',').length;
                if (itemsCount > 0) {
                    $tr.find('.' + entity + 'Show')
                        .removeClass('btn-default')
                        .removeClass('btn-success')
                        .addClass('btn-danger')
                } else {
                    $tr.find('.' + entity + 'Show')
                        .removeClass('btn-danger')
                        .removeClass('btn-success')
                        .addClass('btn-default')
                }
                $tr.find('.itemsShow').find('.items-count').text(itemsCount + "");
            } else if ($tr.find('[name*="' + entity + 'Selected"]').val()) {
                if ($tr.find('[name*="' + entity + 'Banned"]').val()) {
                    $tr.find('.' + entity + 'Show').removeClass('btn-default').removeClass('btn-success').addClass('btn-danger');
                } else {
                    $tr.find('.' + entity + 'Show').removeClass('btn-default').removeClass('btn-danger').addClass('btn-success');
                }
            } else {
                $tr.find('.' + entity + 'Show').removeClass('btn-danger').removeClass('btn-success').addClass('btn-default');
            }
        });

        var $check = $tr.find('.project_competitor-select');

        if ($check.prop('checked')) {
            $tr.addClass('selected');
            $tr.show();
        } else {
            $tr.removeClass('selected');
            $tr.find('input:checked').prop('checked', false);
            $tr.find('select,input[type="text"],input[type="number"]').val('');
        }
        var $key = $tr.find('.project_competitor-key');
        if ($key.prop('checked')) {
            $tr.addClass('key');
        } else {
            $tr.removeClass('key');
        }
    }

    var $iframeReports = $('#reports-iframe');

    $(".report-links li a").on('click',function () {
        indicateFrameLoading($iframeReports.get(0));
        $(".report-links li").removeClass('active');
        $(this).parent().addClass('active');
    });

    function indicateFrameLoading(iframe) {
        pricing.toggleLoadingState($(iframe.contentWindow.document).find('body'));
    }

    function setIFrameUrl(iframe, url) {
        var iframe = $(iframe).get(0);
        indicateFrameLoading(iframe);
        iframe.contentWindow.document.location.href = url;
    }

    $('#prices-project_execution_id').on('change', function(){
        var projectExecutionId = $(this).val();
        $("a.prices-export, #prices-iframe").each(function () {
            var attr = 'href';
            var splitter = 'project_execution_id=';

            var value = $(this).attr(attr);
            if (this.id == 'prices-iframe') {
                attr  = 'src';
                value = this.contentWindow.document.location.href;
            }
            if (typeof value == 'undefined' || value == undefined) {
                return true;
            }

            if (value.indexOf('search[project_execution_id]=') > -1) {
                splitter = 'search[project_execution_id]=';
            }
            var pieces  = value.split(splitter);
            if (typeof pieces[1] == 'undefined' || pieces[1] == undefined) {
                return true;
            }
            var pieces2 = pieces[1].split('&');
            pieces2[0] = projectExecutionId;
            pieces[1] = pieces2.join('&');
            value = pieces.join(splitter);
            $(this).attr(attr, value);

            if (this.id == 'prices-iframe') {
                setIFrameUrl(this, value);
            }
        });
    });

    $('#reports-project_execution_id').on('change', function(){
        var projectExecutionId = $(this).val();
        $(".report-links a, #reports-iframe").each(function () {
            var attr = 'href';
            var splitter = 'project_execution_id=';
            var splitter2 = encodeURI('[project_execution_id]')+'=';

            var value = $(this).attr(attr);
            if (this.id == 'reports-iframe') {
                attr  = 'src';
                value = this.contentWindow.document.location.href;
            }
            if (typeof value == 'undefined' || value == undefined) {
                return true;
            }

            if (value.indexOf(splitter2) > -1) {
                splitter = splitter2;
            }
            var pieces  = value.split(splitter);
            if (typeof pieces[1] == 'undefined' || pieces[1] == undefined) {
                return true;
            }
            var pieces2 = pieces[1].split('&');

            pieces2[0] = projectExecutionId;
            pieces[1] = pieces2.join('&');
            value = pieces.join(splitter);
            $(this).attr(attr, value);

            if (this.id == 'reports-iframe') {
                setIFrameUrl(this, value);
            }
        });
    });


    var $scheduled_daily_time = $('#scheduled_daily_time');
    
    $('#scheduled_daily').on('change',function () {
       if (this.checked) {
           $scheduled_daily_time.removeAttr('disabled').next().removeClass('disabled-addon');
       } else {
           $scheduled_daily_time.attr('disabled',true).val('').addClass('disabled-addon');
       }
    });

    var $iframePrices = $('#prices-iframe');

    //save the latest tab (http://stackoverflow.com/a/18845441)
    $('.project_settings-tabs a[data-toggle="tab"]').on('click', function (e) {
        var tab =  $(e.target).attr('href').replace('#','');
        anchor.set('tab',tab);
        $projectForm.attr('action', window.location.href);
        if(tab == 'prices') {
            setIFrameUrl($iframePrices,  $iframePrices.attr('data-src'));
        }
        if(tab == 'schedule') {
            setTimeout(function () {
                $('#calendar').fullCalendar('render');
            },100);
        }
    });

    var lastTab = anchor.get('tab');

    if (lastTab) {
        $projectForm.attr('action', window.location.href);
        $('a[href="#'+lastTab+'"]').tab('show');
        if(lastTab == 'prices') {
            setIFrameUrl($iframePrices,  $iframePrices.attr('data-src'));
        }
    }
});

function initProjectItemsGrouped() {
    var $allRRPCheck = $('#rrp-all-check');
    var allSame = true;
    var prevState = null;

    function rrpCheck() {
        var $input = $(this).next('input.project_item-rrp_regulations-checkbox-hidden');
        if (!$input || $input.length == 0) {
            $input = $('<input/>');
            $input.addClass('project_item-rrp_regulations-checkbox-hidden');
            $input.attr('type', 'hidden');
            $input.attr('name', $(this).attr('data-name'));
            $(this).after($input);
        }
        $input.val(this.checked?'1':'0');
    }

    $('.project_item-rrp_regulations-checkbox').each(function () {
        var state = parseInt($(this).attr('data-state'),10);
        var checkbox = this;
        checkbox.checked = (state == 1);
        var checked = checkbox.checked;
        if (state < 0) {
            checkbox.indeterminate = true;
        }
        if (prevState === null) {
            prevState = checked;
        } else {
            allSame = allSame && (prevState == checked);
        }
    }).on('change', rrpCheck);

    $allRRPCheck.on('change',function(){
        var t=this;
        setTimeout(function(){
            $('.project_item-rrp_regulations-checkbox').prop('indeterminate', false).prop('checked', t.checked).each(rrpCheck);
        },10);
    });


    if ($allRRPCheck.length > 0) {
        if (allSame) {
            $allRRPCheck.get(0).checked = prevState;
        } else {
            $allRRPCheck.get(0).indeterminate = true;
        }
    }
}

function select2AdditionalParams(fieldName, basic) {
    if (fieldName == "select_categories") {
        basic.search = {
            'is_top' : 1,
            'status_id' : 0
        };
        return basic;
    } else {
        return basic;
    }
}