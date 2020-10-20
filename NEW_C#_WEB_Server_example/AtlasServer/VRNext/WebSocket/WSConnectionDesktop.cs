#if !UNITY_WEBGL || UNITY_EDITOR
using System;
using System.Net;
using System.Security.Authentication;
using System.Threading;

namespace VRNext.WebSocket
{
    /// <summary>
    /// Простое WebSocket подключение для рабочего стола, с функией переподключения, если связь была потеряна.
    /// </summary>
    public class WSConnectionDesktop : WSConnectionAbstract
    {
        private Thread thrdUpdate;
        private WebSocketSharp.WebSocket Socket;

        public float UpdatePeriod = 1f;
        public float ConnectPeriod = 5f;
        
        private long tsUpdate = 0L;
        private long tsConnect = 0L;
        private long tsLastConnected = 0L;
        private int connectionAttempt = 0;

        public WSConnectionDesktop(string host_name, string host_port, string init_token, bool secure, bool resolve_ip = true)
            :base(host_name, host_port, init_token, secure, resolve_ip)
        {
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
            Socket.Send(buffer);
        }
        
        /// <summary>
        /// Отключает WebSocket от сервера
        /// </summary>
        override public void Disconnect()
        {
            if(Socket != null)
            {
                Socket.Close();
                Socket = null;
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
                            if (Socket != null)
                            {
                                Socket.Close();
                                Socket = null;
                            }
                        }
                        else if (Socket == null)
                        {
                            // Создаем WebSocket подключение, если оно отсутсвует
                            if (GetTimePassed(tsConnect) > ConnectPeriod)
                            {
                                string ipEndpoint = "";
                                if (IsIpResolve)
                                {
                                    IsNetworkAvailable = GetIPEndpoint(HostName, ref ipEndpoint);
                                }
                                else
                                {
                                    IsNetworkAvailable = true;
                                    ipEndpoint = HostName;
                                }

                                if (!string.IsNullOrEmpty(ipEndpoint))
                                {
                                    tsConnect = GetTimestamp();
                                    connectionAttempt++;

                                    // Создаем сокет
                                    Socket = CreateSocket(IsSecure, ipEndpoint, HostPort, InitToken);
                                    if (DebugMode)
                                    {
                                        XLogger.Log("[WSClient] Server address: " + Socket.Url);
                                    }

                                    // Назначаем слушателей событий
                                    if (IsSecure)
                                    {
                                        // Set full TLS protocols
                                        Socket.SslConfiguration.EnabledSslProtocols = SslProtocols.Default | SslProtocols.Tls | SslProtocols.Tls11 | SslProtocols.Tls12;
                                        Socket.SslConfiguration.ServerCertificateValidationCallback = (sender, certificate, chain, sslPolicyErrors) =>
                                        {
                                            return true; // If the server certificate is valid.
                                        };
                                    }

                                    Socket.OnMessage += (sender, e) =>
                                    {
                                        State = WSState.ONLINE;
                                        if (DebugMode)
                                        {
                                            XLogger.Log("[WSConnection] Message has been received");
                                        }

                                        if (e.Opcode == WebSocketSharp.Opcode.Text)
                                        {
                                            Messages.Add(new WSData() { type = WSContentType.TEXT, data = e.Data });
                                        }
                                        else if (e.Opcode == WebSocketSharp.Opcode.Binary)
                                        {
                                            Messages.Add(new WSData() { type = WSContentType.BINARY, rawData = e.RawData });
                                        }
                                    };

                                    Socket.OnOpen += (sender, e) =>
                                    {
                                        State = WSState.ONLINE;
                                        if(DebugMode)
                                        {
                                            XLogger.Log("[WSConnection] Connection has been open");
                                        }
                                    };

                                    Socket.OnError += (sender, e) =>
                                    {
                                        State = WSState.ERROR;
                                        XLogger.LogError("[WSConnection] Connection error: " + e.Message);
                                    };

                                    Socket.OnClose += (sender, e) =>
                                    {
                                        State = WSState.OFFLINE;
                                        XLogger.LogWarning("[WSConnection] Connection has been closed.");
                                    };

                                    State = WSState.CONNECTING;
                                    Socket.Connect();
                                }
                            }
                        }
                        else if(Socket != null)
                        {
                            // Закрываем существующее WebSocket подключение, тк статус сообщил что WebSocket отключен
                            Socket.Close();
                            Socket = null;
                        }
                    }
                    else
                    {
                        connectionAttempt = 0;
                        tsLastConnected = GetTimestamp();
                    }
                }
            }
        }

        /// <summary>
        /// Возвращает ip адрес хоста по имени
        /// </summary>
        /// <param name="hostName"></param>
        /// <param name="ipEndpoint"></param>
        /// <returns></returns>
        private static bool GetIPEndpoint(string hostName, ref string ipEndpoint)
        {
            try
            {
                XLogger.Log("[WSConnection] GetIPEndpoint:" + hostName);
                IPHostEntry ips = Dns.GetHostEntry(hostName);
                ipEndpoint = (ips.AddressList[0].ToString());

                if (string.IsNullOrEmpty(ipEndpoint))
                {
                    XLogger.Log("[WSConnection] UpdateIPEndpointThread Error.");
                    return false;
                }
            }
            catch (Exception ex)
            {
                XLogger.Log("[WSConnection] UpdateIPEndpointThread Error.");
                XLogger.LogException(ex);
                ipEndpoint = null;
                return false;
            }
            return true;
        }

        /// <summary>
        /// Создает WebSocket инстанс
        /// </summary>
        /// <param name="secure"></param>
        /// <param name="host"></param>
        /// <param name="port"></param>
        /// <param name="token"></param>
        /// <returns></returns>
        private static WebSocketSharp.WebSocket CreateSocket(bool secure, string host, string port, string token)
        {
            string uri = secure ? "wss://" : "ws://";
            uri += host + ":" + port + "/";
            if (!string.IsNullOrEmpty(token)) uri += "?token=" + token;
            return new WebSocketSharp.WebSocket(new Uri(uri).ToString());
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