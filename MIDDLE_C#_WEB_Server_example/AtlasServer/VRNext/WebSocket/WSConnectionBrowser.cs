#if UNITY_WEBGL && !UNITY_EDITOR
using System;
using System.Runtime.InteropServices;

namespace VRNext.WebSocket
{
    /// <summary>
    /// Простое WebSocket подключение для браузера с функией переподключения, если связь была потеряна.
    /// </summary>
    public class WSConnectionBrowser : WSConnectionAbstract
    {
        [DllImport("__Internal")]
        private static extern int SocketCreate(string url);

        [DllImport("__Internal")]
        private static extern int SocketState(int socketInstance);

        [DllImport("__Internal")]
        private static extern void SocketSendData(int socketInstance, string message);

        [DllImport("__Internal")]
        private static extern void SocketSendRawData(int socketInstance, byte[] ptr, int length);

        [DllImport("__Internal")]
        private static extern string SocketRecvData(int socketInstance, int length);

        [DllImport("__Internal")]
        private static extern void SocketRecvRawData(int socketInstance, byte[] ptr, int length);

        [DllImport("__Internal")]
        private static extern string SocketRecvType(int socketInstance);

        [DllImport("__Internal")]
        private static extern int SocketRecvLength(int socketInstance);

        [DllImport("__Internal")]
        private static extern void SocketClose(int socketInstance);

        [DllImport("__Internal")]
        private static extern int SocketError(int socketInstance, byte[] ptr, int length);

        [DllImport("__Internal")]
        public static extern string GetCookie(string name);

        public int Socket { private set; get; }
        public float UpdatePeriod = 1f;
        public float ConnectPeriod = 5f;

        private long tsUpdate = 0L;
        private long tsConnect = 0L;
        private long tsLastConnected = 0L;
        private int connectionAttempt = 0;

        public WSConnectionBrowser(string host_name, string host_port, string init_token, bool secure, bool resolve_ip = true) :
            base(host_name, host_port, init_token, secure, resolve_ip)
        {
        }

        private void Recv()
        {
            string type = SocketRecvType(Socket);
            if (type == "") return;

            int length = SocketRecvLength(Socket);
            if (length == 0) return;

            State = WSState.ONLINE;
            if (DebugMode)
            {
                XLogger.Log("[WSConnection] Message has been received");
            }

            if (type == "text")
            {
                string message = SocketRecvData(Socket, length);
                Messages.Add(new WSData() { type = WSContentType.TEXT, data = message });
            }
            else if (type == "binary")
            {
                byte[] buffer = new byte[length];
                SocketRecvRawData(Socket, buffer, length);
                Messages.Add(new WSData() { type = WSContentType.BINARY, rawData = buffer });
            }
        }

        private bool Error()
        {
            const int bufsize = 1024;
            byte[] buffer = new byte[bufsize];
            int result = SocketError(Socket, buffer, bufsize);

            if (result == 0)
                return false;

            State = WSState.ERROR;
            XLogger.LogError("[WSConnection] Connection error: " + System.Text.Encoding.UTF8.GetString(buffer));
            return true;
        }

        /// <summary>
        /// Активирует подключение WebSocket к серверу
        /// </summary>
        override public void Connect()
        {
            State = WSState.AWAIT;
            IsAlive = true;
        }

        /// <summary>
        /// Отправляет данные через сокет
        /// </summary>
        override public void Send(byte[] buffer)
        {
            SocketSendRawData(Socket, buffer, buffer.Length);
        }

        /// <summary>
        /// Отключает WebSocket от сервера
        /// </summary>
        override public void Disconnect()
        {
            if (Socket != 0)
            {
                SocketClose(Socket);
                Socket = 0;
            }
            State = WSState.OFFLINE;
            IsAlive = false;
        }

        /// <summary>
        /// Обновляет состояние подключения
        /// </summary>
        override public void Update()
        {
            if (IsAlive || IsReconnectRequired)
            {
                if (GetTimePassed(tsUpdate) > UpdatePeriod)
                {
                    tsUpdate = GetTimestamp();

                    if (State != WSState.ONLINE && State != WSState.CONNECTING)
                    {
                        if (IsReconnectRequired || State == WSState.ERROR)
                        {
                            // В случае ошибки производим отключение WebSocket от сервера
                            IsReconnectRequired = false;
                            IsAlive = true;
                            State = WSState.AWAIT;
                            if (Socket != 0)
                            {
                                SocketClose(Socket);
                                Socket = 0;
                            }
                        }
                        else if (Socket == 0)
                        {
                            // Создаем WebSocket подключение, если оно отсутсвует
                            if (GetTimePassed(tsConnect) > ConnectPeriod)
                            {
                                string ipEndpoint = HostName;
                                IsNetworkAvailable = true;

                                if (!string.IsNullOrEmpty(ipEndpoint))
                                {
                                    tsConnect = GetTimestamp();
                                    connectionAttempt++;

                                    // Создаем сокет
                                    string uri = IsSecure ? "wss://" : "ws://";
                                    uri += ipEndpoint + ":" + HostPort + "/";
                                    if (!string.IsNullOrEmpty(InitToken)) uri += "?token=" + InitToken;
                                    XLogger.Log("[WSClient] Server address: " + uri);
                                    Socket = SocketCreate(new Uri(uri).ToString());

                                    State = WSState.CONNECTING;
                                }
                            }
                        }
                        else if (Socket != 0)
                        {
                            // Закрываем существующее WebSocket подключение, тк статус сообщил что WebSocket отключен
                            SocketClose(Socket);
                            Socket = 0;
                        }
                    }
                }

                if (State == WSState.ONLINE || State == WSState.CONNECTING)
                {
                    int state = SocketState(Socket);
                    if (state != 0)
                    {
                        Error();
                        Recv();

                        connectionAttempt = 0;
                        tsLastConnected = GetTimestamp();
                    }
                }
            }
        }

        /// <summary>
        /// Возвращает текущий слепок времени
        /// </summary>
        /// <returns></returns>
        private static long GetTimestamp()
        {
            return DateTime.Now.Ticks / 10000L;
        }

        /// <summary>
        /// Возвращает время в секундах, прошедшее с момента в аргументе
        /// </summary>
        /// <param name="timestamp"></param>
        /// <returns></returns>
        private static double GetTimePassed(long timestamp)
        {
            return (GetTimestamp() - timestamp) / 1000.0;
        }
    }
}
#endif