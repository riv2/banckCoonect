
import jQuery from "jquery";

export default class Mask {

    static fields() {
        return {
            'price'                     : 'Цена',
            'competitor_item_sku'       : 'Артикул',
            'competitor_item_name'      : 'Наименование',
            'competitor_shop_name'      : 'Магазин',
            'competitor_item_count'     : 'Количество',
            'competitor_item_url'       : 'УРЛ',
            'competitor_item_rubric1'   : 'Рубрика1',
            'competitor_item_rubric2'   : 'Рубрика2',
            'competitor_item_brand'     : 'Бренд',
            'competitor_item_seller'    : 'Продавец',
            'delivery'                  : 'Доставка'
        }
    }

    static types() {
        return {
            'selector'      : 'Селектор',
            'regexp'        : 'Регулярка',
            'range'         : 'От-До'
        }
    }

    static actions() {
        return {
            'clicks'      : 'Клик (аппаратный)',
            'clicksSoft'  : 'Клик (программный)',
            'hrefs'       : 'HREF ссылка',
        }
    }
    static dateParseKinds() {
        return {
            'replace'      : 'Замена',
            'match'       : 'Вхождение',
        }
    }
    static params() {
        return [
            'selector',
            'attribute',
            'from',
            'to',
            'expr',
            'replaceFrom',
            'replaceTo',
            'json'
        ]
    }
    static parsedFields() {
        return Object.assign(Mask.fields(), {
            out_of_stock: 'Налич.',
            url: 'Ссылка',
            delivery_days: 'Дни доставки',
            error_message: 'Ошибка',
        })
    }
    constructor(group, key, data) {
        this.data   = data;

        this.key    = key;

        this.group  = group;

        if (this.data.type === 'norm') {
            this.data.type = 'range';
        }
    }

    element() {
        return this.$el;
    }

    updateLabel() {
        if (this.group === 'fields') {
            this.label = Mask.fields()[this.key];
        } else if (this.group === 'actions') {
            this.label = Mask.actions()[this.key];
        } else if (this.group === 'bounds') {
            this.label = '';
        } else if (this.group === 'stock') {
            this.label = this.data.out ? 'Отсутствует при' : 'В наличии при';
        } else if (this.group === 'dateparse') {
            this.label = Mask.dateParseKinds()[this.key];
        } else if (this.group === 'scripts') {
            this.label = '';
        } else {
            this.label = this.key;
        }
        this.$el.find('.msk-field-name').html(this.label);
    }

    init(appendTo) {

        let templId = this.group;

        if (this.group === 'dateparse') {
            templId = 'replaces';
        }

        let grpSel = '#msk-'+templId+'-template .msk-field';

        if (jQuery(grpSel).length > 0) {
            this.$el  = jQuery(grpSel).clone();
        } else {
            this.$el  = jQuery('#msk-field-template .msk-field').clone();
        }

        this.$el.find('.msk-field-mod-in').on('click', (e) => {
            e.preventDefault();
            this.$el.find('.msk-out').prop('checked', false);
            this.data.out = false;
            this.updateLabel();
        });
        this.$el.find('.msk-field-mod-attr').on('click', (e) => {
            e.preventDefault();
            this.$el.find('.msk-attribute').toggle();
        });
        this.$el.find('.msk-field-mod-json').on('click', (e) => {
            e.preventDefault();
            this.$el.find('.msk-json').toggle();

        });
        this.$el.find('.msk-field-mod-cut').on('click', (e) => {
            e.preventDefault();
            this.$el.find('.msk-replaceTo').toggle();
            this.$el.find('.msk-replaceFrom').toggle();
        });
        this.$el.find('.msk-field-mod-out').on('click', (e) => {
            e.preventDefault();
            this.$el.find('.msk-out').prop('checked', true);
            this.data.out = true;
            this.updateLabel();
        });
        this.$el.find('.msk-field-mod-delete').on('click', (e) => {
            e.preventDefault();
            this.$el.remove();
        });
        if (appendTo) {
            jQuery(appendTo).append(this.$el);
        }
        this.$el.data('mask', this);
        this.$el.get(0).mask = this;
    }

    populateData(data, selector) {
        if (this.$el.find('.msk-'+selector+' input').length > 0 && this.$el.find('.msk-'+selector+' input').val()) {
            data[selector] = this.$el.find('.msk-'+selector+' input').val();
        }
        return data;
    }


    result() {
        let data = null;

        if (this.group === 'dateformat' || this.group === 'scripts') {
            data = this.$el.find('input, textarea').val();
        } else {
            data = {};

            Mask.params().forEach(param => {
                data = this.populateData(data, param);
            });

            if (Object.keys(data).length > 0) {
                if (this.group === 'stock') {
                    data.out = this.$el.find('.msk-out').prop('checked');
                }
                if (this.group === 'replaces') {
                    data.from = data.from || '';
                    data.to = data.to || '';
                }

                if (this.$el.attr('data-type')) {
                    data.type = this.$el.attr('data-type');
                }
            } else {
                data = null;
            }
        }


        return {
            group: this.$el.attr('data-group'),
            key: this.$el.attr('data-key'),
            data: data
        };
    }

    render(appendTo) {
        this.init(appendTo);

        this.updateLabel();
        this.$el.attr('data-group', this.group);
        this.$el.attr('data-key', this.key);

        if (typeof this.data === 'object')
        {
            if (this.data.type) {
                this.$el.attr('data-type',this.data.type);
                this.$el.addClass('msk-field-type-' + this.data.type);
                for (let type in Mask.types()) {
                    if (this.data.type !== type) {
                        if (type === 'regexp') {
                            this.$el.find('.msk-expr').remove();
                        } else if (type === 'range') {
                            this.$el.find('.msk-from').remove();
                            this.$el.find('.msk-to').remove();
                        } else {
                            this.$el.find('.msk-' + type).remove();
                        }
                    }
                }
            }
            if (this.data.selector || this.data.selector === 0) {
                this.$el.find('.msk-selector').show().find('input').val(this.data.selector);
            }
            if (this.data.attribute || this.data.attribute === 0) {
                this.$el.find('.msk-attribute').show().find('input').val(this.data.attribute);
            }
            if (this.data.from || this.data.from === 0) {
                this.$el.find('.msk-from').show().find('input').val(this.data.from);
            }
            if (this.data.to || this.data.to === 0) {
                this.$el.find('.msk-to').show().find('input').val(this.data.to);
            }
            if (this.data.expr || this.data.expr === 0) {
                this.$el.find('.msk-expr').show().find('input').val(this.data.expr);
            }
            if (this.data.replaceFrom || this.data.replaceFrom === 0 || this.data.crop) {
                this.$el.find('.msk-replaceFrom').show().find('input').val(this.data.replaceFrom || this.data.crop);
                this.$el.find('.msk-replaceTo').show().find('input').val(this.data.replaceTo || '');
            }
            if (this.data.json || this.data.json === 0) {
                this.$el.find('.msk-json').show().find('input').val(this.data.json);
            }
            if (this.group === 'stock') {
                if (this.data.out) {
                    this.$el.find('.msk-out').prop('checked', true);
                } else {
                    this.$el.find('.msk-out').prop('checked', false);
                }
                if (this.data.type === 'range' && this.data.from === this.data.expr) {
                    this.$el.find('.msk-expr').hide().find('input').val('');
                }

            }

            if (this.group === 'actions') {
                if (this.key === 'hrefs') {
                    if (this.data.type === 'range') {
                        this.$el.find('.msk-from').show();
                        this.$el.find('.msk-to').show();
                    } else if (this.data.type === 'regexp') {
                        this.$el.find('.msk-expr').show();
                    } else {
                        this.$el.find('.msk-selector').show();
                    }
			    } else {
				    this.$el.find('.msk-selector').show();
			    }
            } else {
                if (this.data.type === 'selector') {
                    this.$el.find('.msk-selector').show();
                }

                if (this.group === 'dateparse' || this.data.type === 'range') {
                    this.$el.find('.msk-from').show();
                    this.$el.find('.msk-to').show();
                }

                if (this.data.type === 'regexp') {
                    this.$el.find('.msk-expr').show();
                }
            }
        } else {
            if (this.group === 'dateformat') {
                this.$el.find('.msk-format').show().find('input').val(this.data);
            }
            if (this.group === 'scripts') {
                this.$el.find('.msk-script').show().find('textarea').val(this.data);
            }
        }

    }


}