(function($){

    /**
     * Класс имопрта
     *
     * @param form
     * @param options массив опций
     * @constructor
     */
    var fileExchangeWidget = function (form, options) {

        this.options = $.extend({
            columnsNames: {},
            columnsExcluded: [],
            columnsValues: {},
            columnsOrder: [],
            isExport: false
        }, options);

        this.form = form;
        this.$form = $(this.form);
        //this.formID = this.$form.attr('id');
        this.$skipFirstRowCheckbox = this.$form.find('[name="FileExchangeSettings[skip_first_row]"]').filter('[type="checkbox"]');
        this.$autoMappingCheckbox = this.$form.find('[name="FileExchangeSettings[auto_mapping]"]').filter('[type="checkbox"]');
        this.$sortable = this.$form.find('.import-widget-sortable');
        
        this.init();
    };

    fileExchangeWidget.prototype.skipFirstRow = function () {
        return this.$skipFirstRowCheckbox.prop('checked');
    };
    
    fileExchangeWidget.prototype.autoMapping = function () {
        return this.$autoMappingCheckbox.prop('checked');
    };


    fileExchangeWidget.prototype.renderExcludedColumn = function (name) {
        if (!name) {
            return null;
        }
        return $('<span class="well import-widget-excluded-column"><a href="#" class="btn btn-default btn-xs import-widget-unexclude" data-column="' + name +'"  title="Использовать эту колонку"><span class="glyphicon glyphicon-download-alt"></span></a>' + this.options.columnsNames[name] + '</span>');
    };

    function removeFromArray(arr, item) {
        for(var i = arr.length; i--;) {
            if(arr[i] === item) {
                arr.splice(i, 1);
            }
        }
        return arr;
    }

    fileExchangeWidget.prototype.init = function () {
        var _ = this;

        var $excludedColumnsContainer =  this.$form.find('.import-widget-excluded-columns');

        $.each(this.options.columnsExcluded, function(){
            var $field = _.renderExcludedColumn(this.toString());
            if ($field) {
                $excludedColumnsContainer.append($field);
            }
        });

        _.$skipFirstRowCheckbox.on('change', function () {
           if (!_.skipFirstRow()) {
               _.$autoMappingCheckbox.prop('checked', false);
               _.$autoMappingCheckbox.trigger('change');
               _.$autoMappingCheckbox.attr('disabled', true);
           } else {
               _.$autoMappingCheckbox.removeAttr('disabled');
           }
        });
        _.$autoMappingCheckbox.on('change', function () {
            if (_.autoMapping()) {
                _.$sortable.hide();
                _.$skipFirstRowCheckbox.prop('checked', true);
                _.$skipFirstRowCheckbox.trigger('change');
            } else {
                _.$sortable.show();
            }
        });

        _.$form.on('click','.import-widget-unpreset', function () {
            var colName = $(this).attr('data-column');
            _.$form.find('.import-widget-column[data-column="'+colName+'"]').removeClass('hidden-disabled');
            _.$form.find('.import-widget-preset-column[data-column="'+colName+'"]').addClass('hidden-disabled').find('input,select,textarea').attr('disabled', true);
            return false;
        });

        _.$form.on('click','.import-widget-preset', function () {
            var colName = $(this).attr('data-column');

            if (_.options.isExport) {
                var $field = _.renderExcludedColumn(colName);
                if ($field) {
                    $excludedColumnsContainer.append($field);
                }
                _.options.columnsExcluded.push(colName);
                _.$form.find('.import-widget-column[data-column="'+colName+'"]').addClass('hidden-disabled');
                //_.$form.find('.import-widget-preset-column[data-column="'+colName+'"]').addClass('hidden-disabled').find('input,select,textarea').attr('disabled', true);
            } else {
                _.$form.find('.import-widget-column[data-column="'+colName+'"]').addClass('hidden-disabled');
                _.$form.find('.import-widget-preset-column[data-column="'+colName+'"]').removeClass('hidden-disabled').find('input,select,textarea').removeAttr('disabled', true);
            }

            return false;
        });

        _.$form.on('click','.import-widget-unexclude', function () {
            var colName = $(this).attr('data-column');
            _.options.columnsExcluded = removeFromArray(_.options.columnsExcluded, colName);
            if (_.options.isExport) {
                $(this).parents('.import-widget-excluded-column').remove();
                _.$form.find('.import-widget-column[data-column="'+colName+'"]').removeClass('hidden-disabled');
            } else {
                $(this).parents('.import-widget-excluded-column').remove();
                _.$form.find('.import-widget-preset-column[data-column="'+colName+'"]').removeClass('hidden-disabled').find('input,select,textarea').removeAttr('disabled', true);
            }
            return false;
        });

        _.$form.on('click','.import-widget-exclude', function () {
            var colName = $(this).attr('data-column');
            var $field = _.renderExcludedColumn(colName);
            if ($field) {
                $excludedColumnsContainer.append($field);
            }
            _.options.columnsExcluded.push(colName);
            _.$form.find('.import-widget-column[data-column="'+colName+'"]').addClass('hidden-disabled');
            _.$form.find('.import-widget-preset-column[data-column="'+colName+'"]').addClass('hidden-disabled').find('input,select,textarea').attr('disabled', true);
            return false;
        });

        var updateDataSource = function () {
            if (_.$form.find('#fileexchangesettings-data_source').find('[value="file"]').prop('checked')) {
                _.$form.find('.import-widget-file').show();
                _.$form.find('.import-widget-content').hide();
            } else {
                _.$form.find('.import-widget-file').hide();
                _.$form.find('.import-widget-content').show();
            }
        };

        _.$form.find('#fileexchangesettings-data_source input').on('change', updateDataSource);

        updateDataSource();

        _.$form.on('submit', function () {
            //e.preventDefault();

            var columns_order = [];
            var preset_columns = [];

            _.$sortable.find('.import-widget-column:not(.hidden-disabled)').each(function () {
                columns_order.push($(this).attr('data-column'));
            });
            _.$form.find('.import-widget-preset-column:not(.hidden-disabled)').each(function () {
                preset_columns.push($(this).attr('data-column'));
            });
            _.$form.find('[name*="columns_order"]').val(columns_order.join(","));
            _.$form.find('[name*="preset_columns"]').val(preset_columns.join(","));
            _.$form.find('[name*="exclude_columns"]').val(_.options.columnsExcluded.join(","));

            if (_.options.isExport) {
               // _.$form.find('.btn-export').attr('disabled', true).html('Не закрывайте страницу до окончания загруки');
            }
            return true;
        });
    };

    function fileExchangeWidgetJQuery(option, _relatedTarget){
        var result;
        this.each(function () {
            var data = $(this).data('import-widget');

            if (!data) {
                $(this).data('import-widget', (data = new fileExchangeWidget(this, option)));
            }
            if (typeof option == 'string') {
                result = data[option](_relatedTarget);
            }
        });
        if (result) {
            return result;
        } else {
            return this;
        }
    }

    $.fn.fileExchangeWidget = fileExchangeWidgetJQuery;
    $.fn.fileExchangeWidget.Constructor = fileExchangeWidget;

})(jQuery);

function select2AdditionalParams(fieldName, basic) {
    if (fieldName == "search_shop_id") {
        basic.search = {
            'source_id' : $('#search-source_id').val(),
            'competitor_id' : $('#search-competitor_id').val()
        };
        return basic;
    } else {
        return basic;
    }
}