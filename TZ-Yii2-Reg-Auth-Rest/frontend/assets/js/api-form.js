(function ($) {
    $.fn.apiForm = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.apiForm');
            return false;
        }
    };

    let defaults = {
        apiUrl: null,
        requestType: null,
        fieldIdPrefix: ''
    };

    let methods = {
        init: function (options) {
            return this.each(function () {
                let settings = $.extend({}, defaults, options || {});
                let form = $(this);

                form.on('beforeSubmit', function (e) {
                    e.preventDefault();
                    let formData = new FormData(form[0]),
                        jsonData = {};

                    for (const [key, value] of formData.entries()) {
                        let clearedKey = key.replace(/.*?\[(.*?)\]/, "$1");
                        jsonData[clearedKey] = value;
                    }

                    $.ajax({
                        url: settings.apiUrl,
                        type: settings.requestType,
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify(jsonData),
                        success: function (data) {
                            form.trigger($.Event('apiForm.success'), data);
                        },
                        error: function (data) {
                            for (let attribute in data.responseJSON) {
                                form.yiiActiveForm(
                                    'updateAttribute',
                                    settings.fieldIdPrefix + data.responseJSON[attribute].field,
                                    [data.responseJSON[attribute].message]
                                );
                            }
                        }
                    });

                    return false;
                });
            });
        }
    };
})(window.jQuery);
