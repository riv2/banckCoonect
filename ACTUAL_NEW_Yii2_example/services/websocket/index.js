// Какое-то древнее говнище
var app                     = require('express')();
var https                   = require('http');
var redis                   = require('redis');
var cookie                  = require('cookie');
var socketsBySessionsIds    = {};
var sessionsIdsByChannels   = {};
var channelsBySessionsIds   = {};
var redisClient             = null;
var redisClientBlocking     = null;
var wsConfig = {
    sessionKeyPrefix:       'prc:session:',
    activeSessionsKey:      'prc:active_sessions',
    sessionKeyForChannels:  'ws-channels',
    cookieKeyForSessionId:  'PHPSESSID',
    channelForMessages:     'ws:messages',
    pendingMessagesPrefix:  'ws:pending_messages:',
    pendingMessagesTimeout: 5,
    log:                    false
};
var fs = require('fs');

var credentials = {
    key: fs.readFileSync('/hosts/pricing.vseinstrumenti.ru.key'),
    cert: fs.readFileSync('/hosts/pricing.vseinstrumenti.ru.crt')
};
var httpServer = require('http').createServer(app);
var httpsServer = require('https').createServer(credentials, app);
var ioServer = require('socket.io');
var io = new ioServer();
io.attach(httpServer);
io.attach(httpsServer);


// Обеспечить мягкий выход
process.stdin.resume();
process.on('exit', exitHandler.bind(null,{cleanup:true}));
process.on('SIGINT', exitHandler.bind(null, {exit:true}));
process.on('uncaughtException', exitHandler.bind(null, {exit:true}));

// Запустить сокеты и редиску
startSocket();
startRedis();

/**
 * Обработчик завершения процесса
 * @param options
 * @param err
 */

function exitHandler(options, err) {
    if (options.cleanup) {

    }
    if (err) {
        console.error(err.stack);
    }
    if (options.exit) {
        process.exit();
    }
}


function startRedis() {
    redisClient         = redis.createClient();
    redisClientBlocking = redis.createClient();

    redisClientBlocking.on('message', function (redisChannel, data) {
        if (redisChannel === wsConfig.channelForMessages) {
            data = JSON.parse(data);
            if (data['channel'] && data['message']) {
                let channel = data['channel'];
                let message = JSON.stringify(data['message']);
                if (channel in sessionsIdsByChannels) {
                    sessionsIdsByChannels[channel].map(function (sessionId) {
                        socketsBySessionsIds[sessionId].map(function (socket) {
                            socket.emit(channel, message);
                        });
                        //console.log('Session ID = ' + sessionId + ': Message published.\nChannel: ' + channel + '\nMessage: ' + message);
                    });
                } else {
                    let timestamp = (new Date()).getTime();
                    let commands = [
                        ['ZREMRANGEBYSCORE', wsConfig.pendingMessagesPrefix + channel, 0, timestamp - wsConfig.pendingMessagesTimeout * 1000],
                        ['ZADD', wsConfig.pendingMessagesPrefix + channel, timestamp, message],
                        ['EXPIRE', wsConfig.pendingMessagesPrefix + channel, wsConfig.pendingMessagesTimeout]
                    ];
                    redisClient.multi(commands).exec(function (err) {
                        if (err) {
                            throw new Error(err);
                        }
                        //console.log('Pending message added.\nChannel: ' + channel + '\nMessage: ' + message);
                    });
                }
            }
        }
    });

    redisClientBlocking.send_command('subscribe', [wsConfig.channelForMessages]);
}


/**
 * Отправить сообщение во все присоедеиненные браузеры через сокет
 * @param channel
 * @param event
 * @param message
 */

function sendToBrowser(channel, event, message) {
    if (io && io.sockets && io.sockets.sockets && Object.keys(io.sockets.sockets).length > 0) {
        io.sockets.emit(channel, JSON.stringify({
            event: event,
            message: message
        }));
    }
}

function initSockets() {

    io.use(function(socket, next){
        if (socket.request.headers.cookie) {
            let cookies = cookie.parse(socket.request.headers.cookie);
            if (cookies && cookies[wsConfig.cookieKeyForSessionId]) {
                socket.sessionId = cookies[wsConfig.cookieKeyForSessionId];
                return next();
            }
        }
        return next(new Error('Authentication error'));
    });

    io.on('connection', function (socket) {
        let sessionId = socket.sessionId;
        if (typeof socketsBySessionsIds[sessionId] === 'undefined') {
            socketsBySessionsIds[sessionId] = [];
        }
        socketsBySessionsIds[sessionId].push(socket);
        redisClient.send_command('SADD', [wsConfig.activeSessionsKey, sessionId]);
        if (!(sessionId in channelsBySessionsIds)) {
            channelsBySessionsIds[sessionId] = [];
        }
        redisClient.get(wsConfig.sessionKeyPrefix + sessionId, function(err, result) {
            console.log(result);
            if (err) {
                throw new Error(err);
            } else if (result) {
                if (!(sessionId in socketsBySessionsIds)) {
                    return; // Сокет отключился раньше данного callback'а, поэтому дальше ничего не делаем
                }
                let sessionData = JSON.parse(result);
                let oldChannels = channelsBySessionsIds[sessionId];
                channelsBySessionsIds[sessionId] = sessionData[wsConfig.sessionKeyForChannels] || [];
                oldChannels.map(function(oldChannel) {
                    if (channelsBySessionsIds[sessionId].indexOf(oldChannel) === -1) {
                        sessionsIdsByChannels[oldChannel] = sessionsIdsByChannels[oldChannel].filter(function(sessId) {
                            return sessId !== sessionId;
                        });
                        if (!sessionsIdsByChannels[oldChannel].length) {
                            delete sessionsIdsByChannels[oldChannel];
                        }
                    }
                });
                channelsBySessionsIds[sessionId].map(function(channel) {
                    if (oldChannels.indexOf(channel) === -1) {
                        if (!(channel in sessionsIdsByChannels)) {
                            sessionsIdsByChannels[channel] = [];
                        }
                        sessionsIdsByChannels[channel].push(sessionId);
                        let timestamp = (new Date()).getTime();
                        let commands = [
                            ['ZRANGEBYSCORE', wsConfig.pendingMessagesPrefix + channel, timestamp - wsConfig.pendingMessagesTimeout * 1000, timestamp],
                            ['DEL', wsConfig.pendingMessagesPrefix + channel]
                        ];
                        redisClient.multi(commands).exec(function(err, results) {
                            if (err) {
                                throw new Error(err);
                            } else if (results) {
                                results[0].map(function(message) {
                                    socket.emit(channel, message);
                                    //console.log('Session ID = ' + sessionId + ': Pending message published.\nChannel: ' + channel + '\nMessage: ' + message);
                                });
                            }
                        });
                    }
                });
                //console.log('Session ID = ' + sessionId + ': Socket subscribed to channels.\nChannels: ' + channelsBySessionsIds[sessionId].join(', '));
            }
        });


        socket.on('disconnect', function () {
            if (typeof socketsBySessionsIds[sessionId] === 'undefined') {
                socketsBySessionsIds[sessionId] = [];
            }
            socketsBySessionsIds[sessionId] = socketsBySessionsIds[sessionId].filter(function(sock) {
                return sock !== socket;
            });
            if (!socketsBySessionsIds[sessionId].length) {
                if (typeof channelsBySessionsIds[sessionId] === 'undefined') {
                    channelsBySessionsIds[sessionId] = [];
                }
                channelsBySessionsIds[sessionId].map(function(channel) {
                    sessionsIdsByChannels[channel] = sessionsIdsByChannels[channel].filter(function(sessId) {
                        return sessId !== sessionId;
                    });
                    if (!sessionsIdsByChannels[channel].length) {
                        delete sessionsIdsByChannels[channel];
                    }
                });
                delete channelsBySessionsIds[sessionId];
                delete socketsBySessionsIds[sessionId];
                redisClient.send_command('SREM', [wsConfig.activeSessionsKey, sessionId]);
            }
            //console.log('Session ID = ' + sessionId + ': Socket disconnected.');
        });
        //console.log('Session ID = ' + sessionId + ': Socket connected.');
    });
}

function startSocket() {

    httpServer.listen(8891, function() {
        httpsServer.listen(8892, function() {
            initSockets();
        }).on('error', function(err)
        {
            if (err.code === 'EADDRINUSE')
            {
                console.log('startSocket EADDRINUSE 8892');
                setTimeout(startSocket, 5000);
            }
        });
    }).on('error', function(err)
    {
        if (err.code === 'EADDRINUSE')
        {
            console.log('startSocket EADDRINUSE 8891');
            setTimeout(startSocket, 5000);
        }
    });

}


