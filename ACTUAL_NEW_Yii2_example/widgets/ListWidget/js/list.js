(function($){

    /**
     * Класс имопрта
     *
     * @param element
     * @param options массив опций
     * @constructor
     */
    var listWidget = function (element, options) {

        this.options = $.extend({
            inputName: 'List',
            attribute: 'name',
            validateRegexp: '',
            sortable: false
        }, options);

        this.element = element;
        this.$element = $(this.element);
        this.ID = this.$element.attr('id');
        this.$input = this.$element.find('.list-widget-new-record input');
        this.$btnAdd = this.$element.find('.list-widget-add-btn');
        this.$addOnTemplate = this.$element.find('.list-widget-addOn-template');
        this.$tbody = this.$element.find('.list-grid-ul');
        this.init();
    };

    listWidget.prototype.addRecord = function (text, id) {

        var _ = this;

        if (!text || _.$tbody.find('[data-id="'+Base64.encode(id ? id : text)+'"]').length > 0) {

            return false;
        }

        var $tdName = $('<div class="left-list-block"></div>');

        $tdName.text(text);

        if (_.options.validateRegexp) {
            var regexp = new RegExp(_.options.validateRegexp);
            if (!regexp.test(id?id:text)) {
                return false;
            }
        }

        if (!id) {
            _.$input.val('');
        }

        var $tr = $('<li data-id="'+Base64.encode(id ? id : text)+'"></li>');

        var $td = $('<div class="right-list-block"></div>');

        var index = this.$tbody.find('li').length + 1;

        var $input = $('<input type="hidden" name="'+this.options.inputName+'['+index+']['+this.options.attribute+']" />');


        $input.val(id?id:text);


        $td.append($input);

        var template = _.$addOnTemplate.html();
        if (template) {
            template = template.replace(/__id__/ig, id ? id : text);
            template = template.replace(/__name__/ig, text);
            template = template.replace(/__index__/ig, index);
            $td.append(template);
            $td.find('[data-name]').each(function () {
                $(this).attr('name', $(this).attr('data-name'));
            });
        }

        $td.append('<button class="btn btn-danger btn-xs list-widget-delete-btn" type="button"><span class="glyphicon glyphicon-trash"></span></button>');

        $tr.append($td).append($tdName);

        this.$tbody.append($tr);
        return true;
    };

    listWidget.prototype.init = function () {
        var _ = this;
        
        _.$btnAdd.on('click', function(e){
            e.preventDefault();
            var $select2ChosenName =_.$element.find('.list-widget-new-record .select2-chosen');
            if ($select2ChosenName.length > 0) {
                _.addRecord($select2ChosenName.html(), _.$input.select2('val'));
            } else {
                _.addRecord(_.$input.val());
            }
            return false;
        });

        _.$input.on('keydown', function(e){
            if (e.which === 13) {
                _.addRecord(_.$input.select2('val'));
                return false;
            }
        });

        _.$tbody.on('click', '.list-widget-delete-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).parents('li').remove();
            return false;
        });

        if (_.options.sortable) {
            _.$tbody.sortable({
                onDrop: function ($item, container, _super, event) {
                    $item.removeClass(container.group.options.draggedClass).removeAttr("style");
                    $("body").removeClass(container.group.options.bodyClass);
                    _.$tbody.find('li').each(function (i) {
                        $(this).find('[name^="'+_.options.inputName+'"]').each(function(){
                            var r = new RegExp(_.options.inputName+'\\[.+?\\]', "g");
                            this.name = this.name.replace(r, _.options.inputName+'['+i+']');
                        });
                    });
                    return true;
                },
                handle: '.left-list-block',
            });
        }

    };

    function listWidgetJQuery(option, _relatedTarget){
        var result;
        this.each(function () {
            var data = $(this).data('list-widget');

            if (!data) {
                $(this).data('list-widget', (data = new listWidget(this, option)));
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

    $.fn.listWidget = listWidgetJQuery;
    $.fn.listWidget.Constructor = listWidget;

})(jQuery);
