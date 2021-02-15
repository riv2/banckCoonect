@extends('layouts.app_old')

@section('title', 'Chat')

@section('content')
    <section v-cloak id="chat-section" class="content">

        <div v-if="!videoChatActive" class="modal" id="call-alert" tabindex="-1" role="dialog">
            <div class="modal-dialog" style="top: 50%; transform: translateY(-50%)" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-center"> Входящий звонок</h4>
                    </div>
                    <div class="modal-body">
                        <img class="contact-photo" src="/images/uploads/faces/default.png">
                        от <span> @{{ callerName }} </span>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="callAnswer" >Принять</button>
                        <button class="btn btn-danger" @click="callCancel">Отклонить</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin" style="padding: 5px 10px;"> {{ __('Chat') }}</h2></div>

            <div class="chat-section mb-5 bg-white rounded">
                <div class="">

                    <div class="row">
                        <div class="col-md-4">

                            <div class="contact-search-wrapper">
                                <input class="contact-search" type="text" v-model="search" placeholder="Введите имя"/>
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </div>

                            <div class="contacts d-flex flex-column">
                                <div v-for="contactInfo in filteredContactsList"
                                     v-bind:id="contactInfo.id"
                                     v-bind:class="[
                                        recipient_id == contactInfo.id ? 'active' : '',
                                        contactInfo.newMessages ? 'new-message' : '',
                                        contactInfo.isOnline ? 'online' : '',
                                        contactInfo.missedCalls ? 'missed-call' : ''
                                     ]"
                                     v-bind:key="contactInfo.id"
                                     class="contact"
                                     @click="checkPerson(contactInfo.id)">
                                    <div class="contact-photo_wrapper">
                                        <img class="contact-photo" src="/images/uploads/faces/default.png">
                                    </div>
                                    <span>
                                        @{{ contactInfo.fio }}
                                        <span v-if="Number(contactInfo.newMessages)"
                                              class="badge badge-primary ml-3"
                                              style="margin-left: 10px; background-color: #007bff">
                                            @{{ contactInfo.newMessages }}
                                        </span>
                                    </span>

                                    <div v-if="Number(contactInfo.missedCalls)" class="missed-call-count">
                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                        @{{ contactInfo.missedCalls }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">

                            <div v-show="showChat" class="chat-wrapper">
                                <video id="localVideo" muted autoplay class="local-video-window"></video>

                                <div class="active-chat-info">
                                    <div class="member-info"></div>
                                    <div id="peer-call" @click="callToContact"
                                         style="margin-left: auto; margin-right: 10px">
                                        <span class="video">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <div id="peer-close" @click="peerClose(true, true)"
                                         style="margin-left: auto; margin-right: 10px; display: none;">
                                        <span class="video" style="background-color: red">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text_video-wrapper">
                                    <video id="remoteVideo" autoplay class="remote-video-window"></video>
                                    <div id="chat">
                                        {{-- messages from redis history --}}
                                        <div v-bind:class="['messsage-text-wrapper', {owner: message.owner}]" v-for="(message, index) in messagesList">
                                            <p>
                                                <span v-html="message.message" style="font-size: 16px" class="message-text"> </span>
                                                -
                                                <span style="font-size: 10px" class="message-date"> @{{ formatMessageDate(message.created_at) }} </span>
                                            </p>
                                        </div>
                                        {{-- new messages --}}
                                        <div v-bind:class="['messsage-text-wrapper', {owner: message.owner}]" v-for="(message, index) in messages" v-bind:key="index">
                                            <p>
                                                <span v-html="message.text" style="font-size: 16px" class="message-text"> </span>
                                                -
                                                <span style="font-size: 10px" class="message-date"> @{{ formatMessageDate(message.created_at) }} </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="chat-cta-wrapper">
                                    <textarea v-model="messageValue"
                                              @keydown.enter.exact.prevent
                                              @keyup.enter.exact="sendMessage"
                                              id="message"
                                              type="text"
                                              placeholder="Сообщение...">
                                    </textarea>
                                    <button id="chat-send" @click="sendMessage">
                                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')

    <script src="https://unpkg.com/peerjs@1.0.0/dist/peerjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>

    <script type="text/javascript">

        let server_path;
        if ("{{env('APP_SOCKET_ENV')}}" === "production") {
            server_path = "wss://miras.app:4433";
        } else if ("{{env('APP_SOCKET_ENV')}}" === "premaster") {
            server_path = "wss://premaster.miras.app:4433";
        } else {
            server_path = "ws://localhost:8080";
        }

        const peer = new Peer();
        let peerCall;
        let recipientPeerKey;
        let autoCloseCall;

        const AUTHORIZE = 'auth';
        const PING = 'ping';
        const PONG = 'pong';
        const MESSAGES_LIST = 'get_messages';
        const CONTACTS_LIST = 'get_contacts';
        const SEND_MESSAGE = 'send_message';
        const GET_NEW_MESSAGES_COUNT = 'get_new_messages_count';
        const RESET_NEW_MESSAGES_COUNT = 'reset_new_messages_count';
        const SET_PEER_ID = 'set_peer_id';
        const GET_PEER_ID = 'get_peer_id';
        const DISCONNECT_VIDEO = 'send_disconnect_video_event';
        const NOTIFY_CALL_CANCEL = 'notify_call_cancel';
        const NOTIFY_CALL_ABORT = 'notify_call_abort';
        const ALL_ONLINE_CONTACTS = 'all_online_contacts';
        const SET_ACTIVE_VIDEO_CHAT_ID = 'set_active_video_chat_id';
        const REMOVE_ACTIVE_VIDEO_CHAT_ID = 'remove_active_video_chat_id';
        const CHECK_ID_ON_ACTIVE_VIDEO_CHAT = 'check_id_on_active_video_chat';
        const GET_MISSED_VIDEO_CALLS_COUNT = 'get_missed_video_calls_count';
        const INCOMING_CALL = 'incoming_call';
        const RESET_MISSED_VIDEO_CALLS_COUNT = 'reset_missed_video-calls-count';

        let app = new Vue({
            el: '#chat-section',
            data: {
                connection: null,
                contactsList: [],
                contactsInfo: null,
                messagesList: [],
                messages: [],
                messageValue: '',
                recipient_id: '',
                senderId: '{{ Auth::user()->id }}',
                senderName: '{{ Auth::user()->name }}',
                showChat: false,
                activeChatPerson: null,
                videoChatActive: false,
                peerID: null,
                callAlert: false,
                recordedBlob: '',
                mediaRecorder: '',
                sendingModalDialog: null,
                callerId: null,
                callerName: null,
                videoChatActivePersonId: null,
                recipientVideoChatStatus: null,
                ringtone: null,
                search: '',
                currentUserData: {
                    id: '{{ Auth::user()->id }}',
                    name: '{{ Auth::user()->name }}',
                    photo: null,
                    email: '{{ Auth::user()->email }}',
                    type: null,
                },
            },

            mounted: function () {
                // this.getCurrentUserInfo();

                this.chatInitialize();
            },

            computed: {
                filteredContactsList() {
                    if(this.contactsInfo) {
                        return this.contactsInfo.filter(contact => {
                            if(contact && contact.fio != null) {
                                return contact.fio.toLowerCase().includes(this.search.toLowerCase())
                            }
                        })
                    }
                }
            },

            updated: function() {
                // run scroll down method for the chat window by new message or check a new person
                this.chatWindowScroll();
            },

            methods: {

                // some important callbacks/methods/event-handlers
                chatInitialize: function () {

                    self = this;

                    peer.on('open', function(peerID) {
                        this.peerID = peerID;
                        self.ajaxSetPeer(peerID);

                        const data = {
                            action: SET_PEER_ID,
                            params: {
                                user_id: app.senderId,
                                peer_id: peerID
                            }
                        };
                        app.connection.send(JSON.stringify(data));
                    });

                    peer.on('call', function(call) {
                        peerCall = call;
                        $('#call-alert').show();
                        app.soundPlay();

                        autoCloseCall = setTimeout(() => {
                            app.callCancel();
                        }, 60000)
                    });

                    peer.on('close', function() {
                        console.log('closed peer!');
                    });

                    peer.on('disconnected', function() {
                        console.log('disconnected peer!');

                        const data = {
                            action: DISCONNECT_VIDEO,
                            params: {
                                user_id: app.senderId,
                                recipient_id: app.recipient_id
                            }
                        };
                        app.connection.send(JSON.stringify(data));
                    });

                    peer.on('connection', function(conn) {
                        conn.on('data', function(data){

                            app.callerId = data;

                            axios.post('{{ route('chatGetCallerById') }}', {
                                user_id: data
                            })
                                .then(function (response) {
                                    if (response) {
                                        app.callerName = response.data.userInfo.name;
                                        // app.callerId = response.data.userInfo.id;
                                    } else {
                                        console.log('bad request');
                                    }
                                });
                        });
                    });

                    this.connection = new WebSocket(server_path);

                    this.connection.onopen = (event) => {
                        console.log("WebSocket is open now.");

                        // this.authorize();
                        this.getCurrentUserInfo();

                        this.ajaxGetContacts();
                    };

                    this.connection.onclose = (event) => {
                        console.warn('Соединение с сервером потеряно!');
                    };

                    this.connection.onerror = (event) => {
                        console.error("WebSocket error observed:", event);
                    };

                    this.connection.onmessage = (event) => {
                        let data = JSON.parse(event.data);

                        /* a new response from WS server by interval and change
                         contact.isOnline status if contact WS connection is open */
                        if(data.action === ALL_ONLINE_CONTACTS) {
                            if (data.params.allOnlineUsers.length > 0 && this.contactsInfo) {
                                this.contactsInfo = this.contactsInfo.map((contact) => {
                                    const isOnline = data.params.allOnlineUsers.some((user) => Object.keys(user)[0] == contact.id);
                                    return { ...contact, isOnline };
                                });

                            }
                        }

                        // get new message from server
                        if (data.action === SEND_MESSAGE) {
                            if(!data.params.owner) {
                                let messageRingtone = new Audio('/assets/audio/chat-ringtone.mp3');
                                messageRingtone.play();
                            }

                            this.messages.push({
                                'from': data.params.from,
                                'text': data.params.message,
                                'owner': data.params.owner,
                                'created_at': data.params.created_at
                            });

                            if (this.activeChatPerson) {
                                this.resetNewMessagesCount();
                                this.resetMissedVideoCallsCount();
                            }
                        }

                        // get available contacts id and run method for get contacts info
                        if (data.action === CONTACTS_LIST) {
                            this.contactsList = data.params.contacts;
                        }

                        // get new messages count from server for all contacts
                        if (data.action === GET_NEW_MESSAGES_COUNT) {
                            this.contactsInfo.forEach(contact => {
                                if (contact.id == data.params.fromId) {
                                    contact.newMessages = data.params.newMessagesCount;
                                }
                            });
                        }

                        // get messages history
                        if (data.action === MESSAGES_LIST) {
                            this.messagesList = data.params.messages;
                        }

                        // notify that video chat has been canceled
                        if (data.action === DISCONNECT_VIDEO) {
                            this.peerClose(false, true);
                        }

                        // notify that call has been canceled from recipient
                        if (data.action === NOTIFY_CALL_CANCEL) {
                            let video = document.getElementById('localVideo');
                            this.stopStreaming(video);
                            $('#peer-call').css('display', 'block');
                            $('#peer-close').css('display', 'none');
                            $('.remote-video-window').hide();
                            $('.local-video-window').hide();
                            app.peerClose(false, false);
                        }

                        // notify that call has been canceled from owner
                        if (data.action === NOTIFY_CALL_ABORT) {
                            this.callCancel(false);
                        }

                        // status of existing recipient id in the Redis videoChatActiveUsers list
                        if (data.action === CHECK_ID_ON_ACTIVE_VIDEO_CHAT) {
                            app.recipientVideoChatStatus = data.params.videoChatStatus;
                            app.callToNode();
                        }

                        // get count of missed video calls
                        if (data.action === GET_MISSED_VIDEO_CALLS_COUNT) {
                            this.contactsInfo.forEach(contact => {
                                if (contact.id == data.params.fromId) {
                                    contact.missedCalls = data.params.videoCallsCount;
                                }
                            });
                        }

                    };

                },

                // send request to the WS server for authorize and create WS client on the server
                authorize: function () {
                    const data = {
                        action: AUTHORIZE,
                        params: {
                            user_id: this.currentUserData.id,
                            user_name: this.currentUserData.name,
                            user_photo: this.currentUserData.photo,
                            user_email: this.currentUserData.email,
                            user_type: this.currentUserData.type
                        }
                    };
                    this.connection.send(JSON.stringify(data));
                },

                //get data about current user
                getCurrentUserInfo: function() {
                    let self = this;

                    axios.post( '/chat/user/getId')
                        .then(function (response) {
                            if (response) {
                                self.currentUserData.id = response.data.user_id;
                                self.currentUserData.name = response.data.name;
                                self.currentUserData.photo = response.data.photo;
                                self.currentUserData.email = response.data.email;
                                self.currentUserData.type = response.data.type;

                                app.authorize();
                            } else {
                                console.log('bad request!');
                            }
                        });
                },

                ajaxGetContacts: function() {
                    let self = this;

                    axios.post('{{ route('contactsInfo') }}', {})
                        .then(function (response) {
                            if (response) {
                                self.contactsInfo = response.data.contacts_info;
                                self.contactsInfo.forEach(contact => {
                                    self.contactsList.push(contact.id);
                                });
                                self.getNewMessagesCount();
                                self.getMissedVideoCallsCount();
                            } else {
                                console.log('bad request');
                            }
                        });
                },

                // send request to the server for get count of new messages
                getNewMessagesCount: function() {
                    const data = {
                        action: GET_NEW_MESSAGES_COUNT,
                        params: {
                            user_id: this.senderId,
                            contacts_list: self.contactsList
                        }
                    };
                    this.connection.send(JSON.stringify(data));
                },

                // send request to the server for reset the count of new messages by check person
                resetNewMessagesCount: function() {
                    const data = {
                        action: RESET_NEW_MESSAGES_COUNT,
                        params: {
                            user_id: this.senderId,
                            recipient_id: this.activeChatPerson
                        }
                    };
                    this.connection.send(JSON.stringify(data));
                },

                // send request to the server for get message's history from database(redis)
                getMessagesHistory: function (id) {
                    const data = {
                        action: MESSAGES_LIST,
                        params: {
                            user_id: this.senderId,
                            recipient_id: id
                        }
                    };
                    this.connection.send(JSON.stringify(data));
                },

                // check required person from contacts list
                checkPerson: function (contactId) {
                    if (contactId) {

                        if(contactId !== this.activeChatPerson) {
                            this.activeChatPerson = contactId;
                            this.showChat = true;
                            this.recipient_id = contactId;
                            this.messages = [];
                            this.getMessagesHistory(contactId);
                            this.resetNewMessagesCount();
                            this.resetMissedVideoCallsCount();
                            this.messageValue = "";
                        }

                    } else {
                        this.showChat = false;
                    }
                },

                // scroll chat window to the bottom
                chatWindowScroll: function() {
                    const chatWindow = document.querySelector("#chat");
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                },

                // send chat message to the WS server
                sendMessage: function () {

                    if (this.messageValue.trim()) {
                        this.messageValue = this.messageValue.replace(/(?:\r\n|\r|\n)/g, '<br>');

                        let currentUnixTimestamp = Math.round((new Date()).getTime() / 1000);

                        const data = {
                            action: SEND_MESSAGE,
                            params: {
                                user_id: this.senderId,
                                sender_name: this.senderName,
                                recipient_id: this.recipient_id,
                                message: this.messageValue,
                                created_at : currentUnixTimestamp
                            }
                        };
                        this.connection.send(JSON.stringify(data));

                        // clear message field
                        this.messageValue = "";
                    }
                },

                // formatted the data for messages in the chat window
                formatMessageDate: function (messageTimestamp) {
                    return new Date(messageTimestamp * 1000).toISOString().slice(0, 19).replace('T', ' ');
                },

                // send the current user id and videoChatActivePersonId into active video chat list of the Redis
                sendActiveVideoChatId: function() {
                    const data = {
                        action: SET_ACTIVE_VIDEO_CHAT_ID,
                        params: {
                            user_id: this.senderId,
                            recipient_id: app.videoChatActivePersonId
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },

                // answer to the call
                callAnswer: function () {
                    app.soundStop();
                    app.checkPerson(app.callerId);
                    app.videoChatActive = true;
                    app.videoChatActivePersonId = app.callerId;
                    clearTimeout(autoCloseCall);

                    app.sendActiveVideoChatId();

                    navigator.mediaDevices.getUserMedia ({ audio: true, video: true })
                        .then(function(mediaStream) {
                            let video = document.getElementById('localVideo');

                            peerCall.answer(mediaStream); // отвечаем на звонок и передаем свой медиапоток собеседнику

                            peerCall.on('close', function () {
                                app.peerClose(false, false);
                            }); //закрытие-обрыв звонка

                            video.srcObject = mediaStream; //помещаем собственный медиапоток в объект видео (чтоб видеть себя)
                            video.onloadedmetadata = function(e) {//запускаем воспроизведение, когда объект загружен
                                video.play();
                                $('.text_video-wrapper').addClass('video_chat_active');
                                $('.remote-video-window').show();
                                $('.local-video-window').show();
                                $('#call-alert').hide();
                                $('#peer-call').css('display', 'none');
                                $('#peer-close').css('display', 'block');
                            };
                            setTimeout(function() {
                                //входящий стрим помещаем в объект видео для отображения
                                document.getElementById('remoteVideo').srcObject = peerCall.remoteStream;
                                document.getElementById('remoteVideo').onloadedmetadata= function(e) {
                                    // и запускаем воспроизведение когда объект загружен
                                    document.getElementById('remoteVideo').play();
                                };
                            },1500);

                            app.recordedBlob = [];
                            try {
                                app.mediaRecorder = new MediaRecorder(mediaStream);
                            } catch (e) {
                                console.log('callAnswer - Ошибка записи!');
                                return;
                            }

                            app.mediaRecorder.ondataavailable =  function (event) {
                                if (event.data && event.data.size > 0) {
                                    app.recordedBlob.push(event.data);

                                    // app.downloadVideo();
                                }
                            };
                            app.mediaRecorder.start();

                        }).catch(function(err) {
                        console.log(err.name + ": " + err.message);
                    });
                },

                // close call
                callCancel: function (sendNotification = true) {
                    $('#call-alert').hide();
                    app.soundStop();
                    peerCall.close();
                    clearTimeout(autoCloseCall);

                    if(sendNotification) {
                        this.notifyCallCancel();
                    }
                },

                // say to the contact that call has been canceled - from remote
                notifyCallCancel: function() {

                    const data = {
                        action: NOTIFY_CALL_CANCEL,
                        params: {
                            user_id: this.senderId,
                            recipient_id: app.callerId
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },


                // say to the contact that call has been aborted - from self
                notifyCallerAbortCall: function() {
                    const data = {
                        action: NOTIFY_CALL_ABORT,
                        params: {
                            user_id: this.senderId,
                            recipient_id: app.activeChatPerson
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },

                // check that id exist in the Redis active video chat list
                checkUserActiveVideoChat: function() {
                    const data = {
                        action: CHECK_ID_ON_ACTIVE_VIDEO_CHAT,
                        params: {
                            user_id: this.senderId,
                            recipient_id: this.recipient_id
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },

                // fabric method for the callToNode
                callToContact: function() {
                    app.checkUserActiveVideoChat();
                },

                // send notification to WS on call
                sendNotifyIncomingCall: function() {
                    const data = {
                        action: INCOMING_CALL,
                        params: {
                            user_id: this.senderId,
                            recipient_id: this.recipient_id
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },

                // call to active contact
                callToNode: function () {
                    // if videoChatActiveUsers list in Redis contain the recipient id - exit and show message
                    if(app.recipientVideoChatStatus) {
                        app.sendNotifyIncomingCall();
                        alert('Абонент с кем-то разговаривает...');
                        return;
                    }

                    $('#peer-call').css('display', 'none');
                    $('#peer-close').css('display', 'block');

                    let self = this;

                    axios.post('{{ route('chatGetPeer') }}', {
                        contact_id: this.activeChatPerson
                    })
                        .then(function (response) {
                            if (response) {

                                recipientPeerKey = response.data.peer_id;

                                navigator.mediaDevices.getUserMedia ({ audio: true, video: true })
                                    .then(function(mediaStream) {
                                        window.stream = mediaStream;
                                        let video = document.getElementById('localVideo');

                                        let peerConnect = peer.connect(recipientPeerKey);
                                        peerConnect.on('open', function() {
                                            peerConnect.send(app.senderId);
                                        });

                                        peerCall = peer.call(recipientPeerKey, mediaStream); //звоним, указав peerID-партнера и передав свой mediaStream
                                        peerCall.on('stream', function (stream) { //нам ответили, получим стрим
                                            // setTimeout(function() {
                                            document.getElementById('remoteVideo').srcObject = peerCall.remoteStream;
                                            document.getElementById('remoteVideo').onloadedmetadata= function(e) {
                                                document.getElementById('remoteVideo').play();
                                                $('.text_video-wrapper').addClass('video_chat_active');
                                                $('.remote-video-window').show();
                                                $('.local-video-window').show();
                                                $('#peer-call').css('display', 'none');
                                                $('#peer-close').css('display', 'block');
                                            };
                                            // }, 1500);

                                            app.recordedBlob = [];
                                            try {
                                                app.mediaRecorder = new MediaRecorder(mediaStream);
                                            } catch (e) {
                                                console.log('ajaxGetPeer - Ошибка записи!');
                                                return;
                                            }

                                            app.mediaRecorder.ondataavailable =  function (event) {
                                                if (event.data && event.data.size > 0) {
                                                    // console.log('данные - ');
                                                    // console.log(event.data);
                                                    // console.log(mediaStream.getTracks());
                                                    app.recordedBlob.push(event.data);

                                                    // console.log(app.recordedBlob);
                                                    // console.log(app.recordedBlob.slice(-1)[0]);
                                                    // app.downloadVideo();
                                                }
                                            };

                                            // console.log(app.mediaRecorder);
                                            app.mediaRecorder.start(5000);

                                        });

                                        video.srcObject = mediaStream;
                                        video.onloadedmetadata = function(e) {
                                            video.play();
                                        };

                                    }).catch(function(err) {
                                    console.log(err.name + ": " + err.message);
                                });

                            } else {
                                console.log('bad request');
                            }
                        });

                },

                ajaxSetPeer: function(peerId){
                    let self = this;

                    axios.post('{{ route('chatSetPeer') }}', {
                        peer_id: peerId
                    })
                        .then(function (response) {
                            if (response) {
                            } else {
                                console.log('bad request');
                            }
                        });
                },

                // remove from active video chat list of Redis the current user id and videoChatActivePersonId
                removeActiveVideoChatId: function() {
                    const data = {
                        action: REMOVE_ACTIVE_VIDEO_CHAT_ID,
                        params: {
                            user_id: this.senderId,
                            // recipient_id: app.videoChatActivePersonId
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },

                peerClose: function (sendAbort = true, sendCancel = true) {
                    app.removeActiveVideoChatId();

                    let video = document.getElementById('localVideo');
                    this.stopStreaming(video);

                    if(app.mediaRecorder.state == 'recording') {
                        app.mediaRecorder.stop();
                    }

                    peerCall.close();

                    $('#peer-call').css('display', 'block');
                    $('#peer-close').css('display', 'none');
                    $('.remote-video-window').hide();
                    $('.local-video-window').hide();

                    if(sendAbort) {
                        app.notifyCallerAbortCall();
                    }

                    if(sendCancel) {
                        app.notifyCallCancel();
                    }

                    app.videoChatActive = false;

                },

                stopStreaming: function(videoElem) {
                    let stream = videoElem.srcObject;
                    let tracks = stream.getTracks();

                    if(tracks.length > 0) {
                        tracks.forEach(function(track) {
                            track.stop();
                        });
                    }

                    if(stream) {
                        stream = null;
                    }
                },

                downloadVideo: function() {
                    if(app.recordedBlob.length > 0) {
                        // if (self.sendingModalDialog == null) {
                        //     self.sendingModalDialog = bootbox.dialog({
                        //         message: '<p><i class="fa fa-spin fa-spinner"></i> Обработка...</p>',
                        //         closeButton: true,
                        //         // centerVertical: true  // not available for current bootstrap version - 3.3.7
                        //     });
                        //     self.sendingModalDialog.find('.bootbox-close-button').hide();
                        // } else {
                        //     self.sendingModalDialog.find('.bootbox-body').html('<p><i class="fa fa-spin fa-spinner"></i> Обработка...</p>');
                        // }

                        // self.sendingModalDialog.init(function() {
                        // setTimeout(function(){
                        let videoEl = app.recordedBlob.slice(-1);
                        let blob = new Blob(videoEl, {type: 'video/mp4'});
                        let fd = new FormData();
                        fd.append('video', blob);
                        fd.append('student_id', app.recipient_id);

                        axios({
                            method: 'post',
                            url: '{{route('chatWebcamPost')}}',
                            data: fd,
                        }).then(function(data) {
                            // console.log('данные ушли на сервер');
                            // self.sendingModalDialog.find('.bootbox-body').html('<p>Спасибо за ожидание. Обработка завершена, можете закрыть это окно</p>');
                            // self.sendingModalDialog.find('.bootbox-close-button').show();
                            // self.sendingModalDialog = null;
                        });


                        // }, 3000);
                        // });
                    }
                    // else {
                    //     this.notifyCallerAbortCall();
                    // }
                },

                soundPlay: function() {
                    app.ringtone = new Audio('/assets/audio/video-chat-ringtone.mp3');
                    app.ringtone.loop = true;
                    app.ringtone.play();
                },

                soundStop: function() {
                    app.ringtone.pause();
                    app.ringtone.currentTime = 0;
                },

                // send request to the server for get count of missed video calls
                getMissedVideoCallsCount: function() {
                    const data = {
                        action: GET_MISSED_VIDEO_CALLS_COUNT,
                        params: {
                            user_id: this.senderId,
                            contacts_list: self.contactsList
                        }
                    };
                    this.connection.send(JSON.stringify(data));
                },

                // send request to the server for reset the count of missed video calls count by check person
                resetMissedVideoCallsCount: function() {
                    const data = {
                        action: RESET_MISSED_VIDEO_CALLS_COUNT,
                        params: {
                            user_id: this.senderId,
                            recipient_id: this.activeChatPerson
                        }
                    };
                    this.connection.send(JSON.stringify(data));
                }

            }
        });

    </script>
@endsection

<style>

    .chat-section {

    }

    @media (max-width: 1023px) {
        .chat-section {
            margin: 0 auto 50px;
        }
    }

    .chat-wrapper {
        position: relative;
    }

    @media (max-width: 1023px) {
        .chat-wrapper {
            margin-top: 20px;
        }
    }

    .text_video-wrapper {
        display: flex;
        height: 300px;
    }

    @media (max-width: 1023px) {
        .text_video-wrapper {
            height: 230px;
        }
    }

    .text_video-wrapper.video_chat_active {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
    }

    .text_video-wrapper.video_chat_active .remote-video-window {
        display: block;
    }

    .remote-video-window {
        width: 50%;
        max-height: 100%;
        display: none;
    }

    /*@media (max-width: 1023px) {*/
    /*    .remote-video-window {*/
    /*        height: 50%;*/
    /*        width: 100%;*/
    /*    }*/
    /*}*/

    .local-video-window {
        width: 300px;
        height: 200px;
        position: absolute;
        right: 0;
        top: 0;
        transform: translateY(-100%);
        z-index: 9999;
        display: none;
    }

    .local-video-window.video_chat_active {
        display: block;
    }

    .text_video-wrapper.video_chat_active #chat {
        box-shadow: none;
        margin-left: 15px;
    }

    .active-chat-info {
        height: 60px;
        width: 100%;
        display: flex;
        align-items: center;
        padding: 15px 10px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        background: #F4F6F9;
    }

    .active-chat-info span.video {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #17A2B8;
        cursor: pointer;
    }

    .active-chat-info span.video i {
        color: #fff;
    }

    #chat {
        max-height: 300px;
        position: static;
        width: 100%;
        background: inherit;
        padding: 15px 10px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        overflow-y: auto;
        overflow-x: hidden;
    }

    @media (max-width: 1023px) {
        #chat {
            max-height: 230px;
        }
    }

    #chat div.messsage-text-wrapper {
        width: 100%;
        display: flex;
        flex-flow: row;
        margin: 0 0 5px 0;
    }

    #chat div.owner {
        flex-direction: row-reverse;
    }

    #chat div span.message-text {
        color: #5ba772;
    }

    #chat div.owner span.message-text {
        color: #1b8dc3;
    }

    #chat .messsage-text-wrapper p {
        max-width: 50%;
        padding: 5px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        border-radius: 10px;
        z-index: 1;
    }

    .chat-cta-wrapper {
        width: 100%;
        display: flex;
        z-index: 99;
        padding: 15px 10px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        background: #F4F6F9;
    }

    #message {
        width: 100%;
        min-height: 44px;
        max-height: 100px;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 10px 15px;
        border: none;
        border-radius: 15px;
        outline: none;
        resize: none;
    }

    #chat-send {
        width: auto;
        font-size: 20px;
        background: inherit;
        margin: 0 10px;
        border: none;
        outline: none;
    }

    .contact-search-wrapper {
        position: relative;
    }

    .contact-search-wrapper i {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        color: #948383;
    }

    .contact-search {
        width: 100%;
        height: 44px;
        border: none;
        border-bottom: 1px solid #f1dfdf;
        outline: none;
        padding-right: 20px;
    }

    .contacts {
        display: flex;
        flex-flow: column;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 400px;
        margin-top: 10px;
        cursor: pointer;
    }

    @media (max-width: 1023px) {
        .contacts {
            max-height: 320px;
        }
    }

    .contact {
        display: flex;
        align-items: center;
        padding: 10px 5px;
        order: 2;
        border: 1px solid #f1dfdf;
        border-radius: 10px;
        border-bottom: none;
        margin: 5px 10px 10px 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
    }

    @media (max-width: 1023px) {
        .contact {
            margin: 5px 0 10px 0;
        }
    }

    .contact.active {
        background: #17A2B8;
        color: #fff !important;
    }

    .contact.new-message, .contact.missed-call {
        order: 1;
    }

    .contact.online {
        box-shadow: 0 2px 5px rgba(21, 173, 26, 0.4);
        order: 1;
    }

    .contact.online .contact-photo{
        border: 2px solid rgb(21, 173, 26);
    }

    .contact.online .contact-photo_wrapper:after {
        content: '';
        position: absolute;
        right: 2px;
        bottom: 5px;
        display: block;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: rgb(21, 173, 26);
    }

    .contact-photo_wrapper {
        width: 60px;
        height: 60px;
        margin-right: 5px;
        position: relative;
    }

    .contact-photo_wrapper:after {
        content: '';
        position: absolute;
        right: 2px;
        bottom: 5px;
        display: block;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: rgb(214, 76, 64);
    }

    .contact-photo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid rgb(214, 76, 64);
    }

    .missed-call-count {
        width: 60px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
    }

    .missed-call-count i {
        transform: rotate(135deg);
        font-size: 20px;
        color: #007bff;
        margin-right: 10px;
    }
</style>