$(function () {


    var $modal = {
        'categories'    : $('#selectCategoriesModal'),
        'brands'        : $('#selectBrandsModal')
    };
    var $competitorsContainer = $('#competitors-rows');
    var $projectCompetitorTemplate = $('#project_competitor-template').clone();
    $('#project_competitor-template').remove()


    function renderCompetitor(projectCompetitor) {
        var $projectCompetitor = $projectCompetitorTemplate.clone();
        $projectCompetitor.attr('id','');
        $projectCompetitor.html($projectCompetitorTemplate.html().replace(/%id%/gim, projectCompetitor.id));
        $projectCompetitor.attr('data-id', projectCompetitor.id);
        $projectCompetitor.attr('data-project_id', projectCompetitor.project_id);
        $projectCompetitor.attr('data-competitor_id', projectCompetitor.competitor.id);
        $projectCompetitor.attr('data-competitorName', projectCompetitor.competitor.name);
        $projectCompetitor.attr('data-projectName', projectCompetitor.projectName);
        $projectCompetitor.find('.project_competitor-projectName').html(projectCompetitor.projectName);
        $projectCompetitor.find('.project_competitor-competitorName').html(projectCompetitor.competitor.name);
        $projectCompetitor.find('.project_competitor-select').val(projectCompetitor.id);
        $projectCompetitor.show();

        $projectCompetitor.find('input, select').on('change', function () {
            var data = {
                project_id: projectCompetitor.project_id,
                competitor_id: projectCompetitor.competitor_id,
                newCategories: [],
                newBrands: [],
            };

            $projectCompetitor.find('[data-field]').each(function (e) {
                data[$(this).attr('data-field')] = $(this).val();
            });


            data.status_id = $projectCompetitor.find('.project_competitor-status').prop('checked') ? 2 : 0;
            data.is_key_competitor = $projectCompetitor.find('.project_competitor-key').prop('checked') ? 1 : 0;

            var categories = $projectCompetitor.find('.project_competitor-categories_selected').val() ;
            var brands = $projectCompetitor.find('.project_competitor-brands_selected').val();
            categories = categories ? categories.split(',') : [];
            brands = brands ? brands.split(',') : [];
            var $cb = $projectCompetitor.find('.project_competitor-categories_banned');
            var $bb = $projectCompetitor.find('.project_competitor-brands_banned');
            $.each(categories, function (i, t) {
                data.newCategories.push({category_id: t.trim(), status_id: $cb.prop('checked') ? 0 : 2 });
            });
            $.each(brands, function (i, t) {
                data.newBrands.push({brand_id: t.trim(), status_id: $bb.prop('checked') ? 0 : 2 });
            });

            $.ajax({
                url: '/project-competitor/update/?id=' + projectCompetitor.id,
                data: data,
                dataType: 'json',
                type: 'post',
                success: function (json) {

                }
            });
            console.log(data);
        });

        $projectCompetitor.on('click', function(event){
            var $check = $projectCompetitor.find('.project_competitor-select');
            if ($(event.target).hasClass('brandsShow') || $(event.target).hasClass('categoriesShow')) {
                event.preventDefault();
                var entity = 'categories';
                if ($(event.target).hasClass('brandsShow')) {
                    entity = 'brands';
                }
                $check.prop('checked', true);
                var $selected = $modal[entity].find('[name="select_'+entity+'_text"]');
                var $selected2 = $modal[entity].find('[name="select_'+entity+'"]');
                $modal[entity].modal('show');
                $modal[entity].attr('data-id', $(event.target).attr('data-id'));
                $modal[entity].find('.competitorName').html($projectCompetitor.attr('data-competitorName'));
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
                        $selected.val(json.join(', '));
                    }
                });

                return false;
            }
            updatePCTR($projectCompetitor, false);
        });
        return $projectCompetitor;
    }


    $.each(window.projectCompetitors, function(){
        var $projectCompetitor = renderCompetitor(this);
        $projectCompetitor.addClass('selected');
        $projectCompetitor.find('.project_competitor-select').prop('checked', true);
        $projectCompetitor.find('.project_competitor-key').prop('checked', this.is_key_competitor);
        $projectCompetitor.find('.project_competitor-status').prop('checked', this.status_id !== 0);
        $projectCompetitor.find('.project_competitor-price_variation_modifier').val(this.price_variation_modifier);
        $projectCompetitor.find('.project_competitor-price_final_modifier').val(this.price_final_modifier);
        var brandsSelected = {"ids":[],"names":[], banned: false};
        var categoriesSelected = {"ids":[],"names":[], banned: false};
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
        $projectCompetitor.find('.project_competitor-brands_selected').val(brandsSelected.ids.join(', '));
        $projectCompetitor.find('.project_competitor-brands_banned').val(brandsSelected.banned?'1':'');
        $projectCompetitor.find('.project_competitor-categories_selected').val(categoriesSelected.ids.join(', '));
        $projectCompetitor.find('.project_competitor-categories_banned').val(categoriesSelected.banned?'1':'');
        if (brandsSelected.names.length > 0) {
            $projectCompetitor.find('.brandsShow').html(brandsSelected.names.join(', '));
        }
        if (categoriesSelected.names.length > 0) {
            $projectCompetitor.find('.categoriesShow').html(categoriesSelected.names.join(', '));
        }
        updatePCTR($projectCompetitor, true);
        $competitorsContainer.append($projectCompetitor);
    });

    $.each(['categories', 'brands'],function(){
        var entity = this;
        $modal[entity].find('.selectBan,.selectOk').on('click',function(e){
            e.preventDefault();
            var $selected2 = $modal[entity].find('[name="select_'+entity+'"]');
            var $selected = $modal[entity].find('[name="select_'+entity+'_text"]');
            var snames = $selected.val()?$selected.val().split(',').map(function (el) {
                return el.trim();
            }):[];
            $modal[entity].modal('hide');
            var id = $modal[entity].attr('data-id');
            var $tr = $competitorsContainer.find('tr[data-id="'+id+'"]');
            var $btn = $(this);
            console.log(id);
            console.log($tr);
            $.ajax({
                "url":"/project/name-2-id",
                "data": {
                    "names": snames,
                    "type": entity
                },
                "type":"post",
                "dataType":"json",
                success: function(ids){

                    $tr.find('[name*="'+entity+'Selected"]').val(ids.join(', '));

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
                        $tr.find('.' + entity + 'Show').removeClass('btn-danger').removeClass('btn-success').addClass('btn-default').html('<span class="glyphicon glyphicon-edit"></span>  ' + (entity==='brands') ? 'Бренды' : 'Категории');
                    }

                    $tr.find('[name*="' + entity + 'Selected"]').trigger('change');
                }
            });

            $selected.val('');
            $selected2.select2("val","");
            return false;
        });

    });

    function updatePCTR($tr, initial) {
        $tr = $($tr);

        $.each(['categories', 'brands'],function() {
            var entity = this;
            if ($tr.find('[name*="' + entity + 'Selected"]').val()) {
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


});