const AUTHORIZE = 'auth';
const SEND_MESSAGE = 'send_message';
const GET_NEW_MESSAGES_TOTAL_COUNT = 'get_new_messages_total_count';

let senderId;
let senderName;
let senderPhoto;
let senderEmail;
let senderType;
let totalCount;
let connection;

axios.post( '/chat/user/getId')
    .then(function (response) {
        if (response) {

            let server_path;
            if (response.data.env === "production") {
                server_path = "wss://miras.app:4433";
            } else if (response.data.env === "premaster") {
                server_path = "wss://premaster.miras.app:4433";
            } else {
                server_path = "wss://localhost:8080";
            }

            senderId = response.data.user_id.toString();
            senderName = response.data.name;
            senderPhoto = response.data.photo;
            senderEmail = response.data.email;
            senderType = response.data.type;

            connection = new WebSocket(server_path);

            connection.onopen = (event) => {
                console.log("WebSocket is open now.");
                authorize(senderId, senderName, senderPhoto, senderEmail, senderType);
                getNewMessagesTotalCount(senderId);
            };

            connection.onclose = (event) => {
                console.warn('Соединение с сервером потеряно!');
            };

            connection.onerror = (event) => {
                console.error("WebSocket error observed:", event);
            };

            connection.onmessage = (event) => {
                let data = JSON.parse(event.data);


                if (data.action === GET_NEW_MESSAGES_TOTAL_COUNT) {
                    totalCount = data.params.newMessagesTotalCount;
                    updateCountValue(totalCount);
                }

                if (data.action === SEND_MESSAGE) {
                    if(!data.params.owner) {
                        let messageRingtone = new Audio('/assets/audio/chat-ringtone.mp3');
                        messageRingtone.play();
                    }

                    totalCount++;
                    updateCountValue(totalCount);
                }

            };

        } else {
            console.log('bad request');
        }
    });

authorize = (id, name, senderPhoto, email, type) => {
    const data = {
        action: AUTHORIZE,
        params: {
            user_id: id,
            user_name: name,
            user_photo: senderPhoto,
            user_email: email,
            user_type: type
        }
    };

    connection.send(JSON.stringify(data));
};

getNewMessagesTotalCount = (id) => {
    const data = {
        action: GET_NEW_MESSAGES_TOTAL_COUNT,
        params: {
            user_id: id,
        }
    };

    connection.send(JSON.stringify(data));
};

updateCountValue = (totalCount) => {
    if (totalCount > 0) {
        const mainStudentSidebarLink = $('#student-sidebar-link');
        const studentChatCounterTag = $('#student-chat_new-messages');
        const teacherChatCounterTag = $('#teacher-chat_new-messages');

        if (mainStudentSidebarLink.length > 0 && studentChatCounterTag.length > 0) {
            $('#newMessagesTotalCount').text(totalCount);
            mainStudentSidebarLink.find('.fa-envelope').show();
            studentChatCounterTag.text(totalCount);
            studentChatCounterTag.show();
        }

        if (teacherChatCounterTag.length > 0) {
            teacherChatCounterTag.text(totalCount);
            teacherChatCounterTag.show();
        }
    }
};