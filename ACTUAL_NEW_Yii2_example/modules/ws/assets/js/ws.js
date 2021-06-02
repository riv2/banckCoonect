var ws = (function(){
    var t = this;

    var _messageHandlers = {};

    var socket = io.connect('ws://' + window.location.hostname + ':8891');

    $.extend( t, {
        init: function(options){
            if (typeof options == 'undefined') {
                return null;
            }

            // socket.on('connect_error', function () {
            //     socket.close();
            //     socket = io.connect('ws://' + window.location.hostname + ':8891');
            // });

            options = $.extend({
                wsUserChannels: []
            }, options);

            if (options.wsUserChannels.length > 0) {
                $.each(options.wsUserChannels,function(){
                    socket.on(this, function (data) {
                        //console.log(data);
                        t.onMessage(JSON.parse(data));
                    });
                });
            }
        },
        onMessage: function(data){
            if (typeof data.event == 'undefined') {
                return false;
            }
            if (typeof _messageHandlers[data.event] != 'undefined'){
                $.each(_messageHandlers[data.event], function (key, value) {
                    return value(data);
                });
            }
            return true;
        },
        sendMessage: function (message) {
            socket.send(message);
        },
        addHandler: function (eventName, eventHandler) {
            if (typeof eventHandler != 'function') {
                return false;
            }
            if (typeof _messageHandlers[eventName] == 'undefined'){
                _messageHandlers[eventName] = [];
            }
            _messageHandlers[eventName].push(eventHandler);
            return true;
        }
    });

    return t;
})();

