using System;
using System.Net;
using System.Threading;
using System.Collections.Generic;

namespace VRNext.WebSocket
{
    /// <summary>
    /// Обработчик обмена данными по WebSocket
    /// </summary>
    public class WSManager
    {
        /// <summary>
        /// Текущее WebSocket подключение
        /// </summary>
        public WSConnectionAbstract Connection { private set; get; }

        /// <summary>
        /// Текущий буффер входящих сообщений от сервера
        /// </summary>
        public WSMessageBuffer Messages { private set; get; }

        /// <summary>
        /// Текущий буффер входящих сообщений от сервера
        /// </summary>
        public WSState State { private set; get; }

        public string SecurityToken;
        public string NetworkId;
        public string SessionId;
        public string ClientName;
        public string ServerName;
        public string ServerHost;
        public int ServerPort;
        public Action<object> OnData;
        public Action<object> OnCommand;
        public Action<object> OnConnect;
        public Action<object> OnDisconnect;

        /// <summary>
        /// Режим отладки, выводит в лог больше информации о работе модуля
        /// </summary>
        private bool DebugMode;

        /// <summary>
        /// Время ожидания ответа от сервера в секундах
        /// </summary>
        public int AwaitTimeout = 10;

        /// <summary>
        /// Проверять наличие связи через механизм Ping-Pong
        /// </summary>
        public bool DoPing = false;

        /// <summary>
        /// Период отправки запроса Ping в секундах
        /// </summary>
        public int PingPeriod = 1;

        /// <summary>
        /// Время отправки запроса Ping для измерения задержки ответа от сервера
        /// </summary>
        private long PingTimestamp = 0;

        /// <summary>
        /// Время ожидания ответа от сервера на команду Ping, для оценки наличия связи с сервером
        /// </summary>
        private long ping_ts = 0;

        /// <summary>
        /// Задержка ответа от сервера
        /// </summary>
        public int Latency { private set; get; }

        /// <summary>
        /// Устанавливает флаг отладки модуля
        /// </summary>
        /// <param name="enabled"></param>
        public void SetDebugMode(bool enabled)
        {
            DebugMode = enabled;
        }
        
        /// <summary>
        /// Подключает WebSocket к серверу по указанным данным
        /// </summary>
        /// <param name="host_name"></param>
        /// <param name="host_port"></param>
        /// <param name="init_token"></param>
        /// <param name="secure"></param>
        /// <param name="resolve_ip"></param>
        public void Connect(string host_name, string host_port, string init_token, bool secure, bool resolve_ip = true)
        {
            Latency = 0;

            Messages = new WSMessageBuffer();
            Connection = WSConnectionPool.GetInstance().NewConnection(host_name, host_port, init_token, secure, resolve_ip);
            Connection.SetMessageBuffer(Messages);
            Connection.Connect();
        }

        /// <summary>
        /// Отколючает WebSocket от сервера
        /// </summary>
        public void Disconnect()
        {
            if(Connection != null)
            {
                Connection.Disconnect();
            }
        }

        /// <summary>
        /// Цикл обработки входящих сообщений от сервера в основном потоке
        /// </summary>
        public void Update()
        {
            if (Connection != null)
            {
                if(State == WSState.ONLINE && Connection.State != WSState.ONLINE)
                {
                    OnDisconnect?.Invoke(null);
                    State = WSState.OFFLINE;
                }

                if (State != WSState.ONLINE && Connection.State == WSState.ONLINE)
                {
                    //OnConnect?.Invoke(null);
                    State = WSState.ONLINE;
                }

                List<WSData> buffer = Messages.GetBuffer();
                for (int i = 0; i < buffer.Count; i++)
                {
                    if (DebugMode)
                    {
                        if(buffer[i].data == null)
                            XLogger.Log("[WSManager] Message with server command has been received");
                        else
                            XLogger.Log("[WSManager] Message with data of " + buffer[i].data.Length + " bytes has been received");
                    }
                    ProcessMessage(buffer[i]);
                    if (DebugMode) XLogger.Log("[WSManager] Message has been successfully processed");
                }
                /*
                if (Connection.State == WSState.ONLINE)
                {
                    if (DoPing) Ping();
                }
                */
            }
        }

        /// <summary>
        /// Отправляет данные по targetId через сервер
        /// </summary>
        /// <param name="target_id"></param>
        /// <param name="data"></param>
        public bool Send(string target_id, byte[] data)
        {
            if (!string.IsNullOrEmpty(SessionId) && Connection.State == WSState.ONLINE)
            {
                Connection.Send(WSPacket.Serialize(new WSPacket(SessionId, target_id, data)));
                if(DebugMode) XLogger.Log("[WSManager] Data packet of " + data.Length + " bytes has been sent");
                return true;
            }
            return false;
        }

        /// <summary>
        /// Отправляет данные на сервер
        /// </summary>
        /// <param name="target_id"></param>
        /// <param name="data"></param>
        public bool Send(byte[] data)
        {
            if (string.IsNullOrEmpty(ServerName))
                throw new Exception("Target id is not defined!");

            if (!string.IsNullOrEmpty(SessionId) && Connection.State == WSState.ONLINE)
            {
                Connection.Send(WSPacket.Serialize(new WSPacket(SessionId, ServerName, data)));
                if (DebugMode) XLogger.Log("[WSManager] Data packet of " + data.Length + " bytes has been sent");
                return true;
            }
            return false;
        }

        public void SetSessionClientName(string session_name)
        {
            WSCommand command = new WSCommand(WSOperation.RENAME_SESSION_ID, new List<WSArgument>() { new WSArgument(WSArgumentType.STRING, session_name) });
            Send("", WSCommand.Serialize(command));
        }
        /*
        #if UNITY_WEBGL && !UNITY_EDITOR
                private IEnumerator ConnectSocket()
                {
                    yield return w_socket.Connect(AwaitTimeout);
                    if (w_socket.error != null)
                    {
                        XLogger.Log("[WSManager] Error: " + w_socket.error);
                        State = WSState.ERROR;
                        w_socket.Close();
                    }
                    yield return 0;
                }
        #else
                private void ConnectSocket()
                {
                    w_socket.Connect(AwaitTimeout);
                    if (w_socket.error != null)
                    {
                        XLogger.Log("[WSManager] Error: " + w_socket.error);
                        State = WSState.ERROR;
                        w_socket.Close();
                    }
                }
        #endif
        */

        /// <summary>
        /// Обработчик входящего сообщения
        /// </summary>
        /// <param name="message"></param>
        private void ProcessMessage(WSData message)
        {
            if (message != null)
            {
                if (message.type == WSContentType.TEXT)
                {
                    if (DebugMode) XLogger.Log("[WSManager] Server reply: " + message.data);
                }

                if (message.type == WSContentType.BINARY)
                {
                    WSPacket packet = WSPacket.Deserialize(message.rawData);
                    if (packet.sender_id == "")
                    {
                        WSCommand command = WSCommand.Deserialize(packet.data);
                        if (command != null)
                        {
                            switch (command.operation)
                            {
                                case WSOperation.PONG:
                                    OnPong(command);
                                    break;
                                case WSOperation.JOIN_SESSION_ID:
                                    OnSessionJoin(command);
                                    break;
                                case WSOperation.RENAME_SESSION_ID:
                                    OnSessionRename(command);
                                    break;
                            }

                            try
                            {
                                OnCommand?.Invoke(packet.data);
                            }
                            catch (System.Exception ex)
                            {
                                XLogger.LogException(ex);
                            }
                        }
                    }
                    else if (packet.recipient_id == SessionId)
                    {
                        try
                        {
                            OnData?.Invoke(packet.data);
                        }
                        catch (Exception ex)
                        {
                            XLogger.LogException(ex);
                        }
                    }
                }
            }
            else if (Connection.State == WSState.ERROR)
            {
                try
                {
                    OnDisconnect?.Invoke(null);
                }
                catch (Exception ex)
                {
                    XLogger.LogException(ex);
                }
            }
        }

        /// <summary>
        /// Ответ на команду Ping
        /// </summary>
        /// <param name="command"></param>
        private void OnPong(WSCommand command)
        {
            Latency = (int)((DateTime.Now.Ticks / 10000L) - PingTimestamp);
            PingTimestamp = 0L;
            ping_ts = Network.NetFlex.GetAbsoluteTime();
            XLogger.Log("[WSManager] Latency: " + Latency);
        }

        /// <summary>
        /// Ответ сервера на подключение
        /// </summary>
        /// <param name="command"></param>
        private void OnSessionJoin(WSCommand command)
        {
            SessionId = command.arguments[0].GetString();
            ClientName = command.arguments[0].GetString();

            if (command.arguments.Count > 1 && command.arguments[1].type == WSArgumentType.STRING)
            {
                ServerName = command.arguments[1].GetString();
                if (DebugMode) XLogger.Log("[WSManager] Obtained the host server name: " + ServerName);
            }

            if (DebugMode) XLogger.Log("[WSManager] Joined to server, session client name is " + SessionId);
            if (ClientName != NetworkId)
            {
                SetSessionClientName(NetworkId);
            }
        }

        /// <summary>
        /// Ответ сервера на присвоение нового имени сессии
        /// </summary>
        /// <param name="command"></param>
        private void OnSessionRename(WSCommand command)
        {
            ClientName = command.arguments[1].GetString();
            if(DebugMode) XLogger.Log("[WSManager] Session client name was changed to " + ClientName);
            if (ClientName != NetworkId)
            {
                SetSessionClientName(NetworkId);
            }
        }

        /// <summary>
        /// Обработчик цикла Ping (не используется)
        /// </summary>
        private void Ping()
        {
            if (Connection.State == WSState.ONLINE)
            {
                if (PingTimestamp > 0)
                {
                    // Ожидается ответ Pong от сервера
                    if (Network.NetFlex.GetTimePassed(ping_ts) > PingPeriod)
                    {
                        // Время ожидания истекло, прерываем подключение и переводим сокет в ошибку.
                        ping_ts = 0;
                        PingTimestamp = 0;
                        Connection.IsReconnectRequired = true;
                        XLogger.LogError("[WSManager] Connection timeout!");
                    }
                }
                else
                {
                    // Ожидается отправка следующего Ping
                    if (Network.NetFlex.GetTimePassed(ping_ts) > PingPeriod)
                    {
                        // Отправляем Ping
                        WSCommand command = new WSCommand(WSOperation.PING, new List<WSArgument>() { new WSArgument(WSArgumentType.INTGER, Latency) });
                        Send("", WSCommand.Serialize(command));
                        PingTimestamp = DateTime.Now.Ticks / 10000L;
                        ping_ts = AwaitTimeout;
                    }
                }
            }
            else
            {
                ping_ts = Network.NetFlex.GetAbsoluteTime();
                PingTimestamp = 0;
            }
        }
    }
}