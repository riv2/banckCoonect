using System.Collections;
using System.Collections.Generic;
using VRNext.WebSocket;
using VRNext.XService;

namespace VRNext.Network
{
    // TODO: Есть возможные улучшения модуля.
    // 1. При отмене отправки большого файла по разным причинам, файл удалится из очереди отправки,
    // однако принимающая сторона об этом ничего не узнает и будет пролдолжать ждать файл еще 120 секунд.
    // 2. При уведомлении клиента об отмене примема файла, клиент может получить его и не отправить оставшую часть данных,
    // но на принимающей стороне файл помечается как отмененный, но не удаляется и хранится еще 30 секунд.

    public class NetFlex : XEventDispatcher, IXService
    {
        public const string CONNECTED = "connected";
        public const string DISCONNECTED = "disconnected";
        public const string COMMAND_RECEIVED = "command_received";
        public const string NETFILE_RECEIVED = "netfile_received";

        /// <summary>
        /// Данные успешно получены (целиком)
        /// </summary>
        public const string DATA_RECEIVED = "data_received";

        /// <summary>
        /// Заголовочные данные получены
        /// </summary>
        public const string DATA_INFO_RECEIVED = "data_info_received";

        /// <summary>
        /// Загружена часть данных
        /// </summary>
        public const string DATA_PART_RECEIVED = "data_part_received";

        /// <summary>
        /// Список данных загружен целиком
        /// </summary>
        public const string DATA_LIST_RECEIVED = "data_list_received";

        public enum NetFlexMode { DEFAULT, SERVER, MODULE };
        public enum NetConectionMode { NONE, DISABLED, ENABLED, SECURE, DUPLEX };
        public enum NetConnectionStatus { OFFLINE, CONNECTING, ONLINE };
        public class NetConfig
        {
            public string IpEndpoint;
            public string Host;
            public int Port;
            public int Timeout;
            public bool Secure;

            public NetConfig() { }

            public NetConfig(NetConfig config)
            {
                IpEndpoint = config.IpEndpoint;
                Host = config.Host;
                Port = config.Port;
                Timeout = config.Timeout;
                Secure = false;
            }
        }

        private static NetFlex instance = null;
        private static System.Random random = new System.Random();

        internal const string NETFLEX_PATH = @"C:\ATLAS_SERVER\temp\netflex\";

        /// <summary>
        /// Время ожидания ответа от сервера в секундах
        /// </summary>
        internal const int AWAIT_TIMEOUT = 10;

        /// <summary>
        /// Ограничение размера одного пакета данных для отправки
        /// </summary>
        internal const int PACKET_SIZE_BYTES = 102400;

        /// <summary>
        /// Ограничение размера одного блока данных для загрузки
        /// </summary>
        internal const int DOWNLOAD_PART_BYTES = 250000;

        /// <summary>
        /// Количество агентов загрузки данных
        /// </summary>
        internal const int DOWNLOAD_AGENTS_COUNT = 16;

        /// <summary>
        /// Ограничение размера очереди отправляемых принимаемых файлов 
        /// </summary>
        internal const int SENDING_QUEUE_LIMIT = 500;

        /// <summary>
        /// Ограничение размера очереди неотмененных принимаемых файлов 
        /// </summary>
        internal const int RECEIVING_QUEUE_LIMIT = 500;

        /// <summary>
        /// Время (в секундах) ожидания обязательного файла
        /// </summary>
        internal const int AWAIT_IMPORTANT_TIME = 120;

        /// <summary>
        /// Время (в секундах) защиты от двойного приема файла
        /// </summary>
        internal const int DUPLICATE_PROTECTION_TIME = 180;

        /// <summary>
        /// Количество попыток скачивания файла, при наличии ошибки
        /// </summary>
        internal const int DOWNLOAD_DATA_ATTEMPTS_LIMIT = 1;

        /// <summary>
        /// Время (в секундах) ожидания необязательного файла
        /// </summary>
        internal const int AWAIT_UNIMPORTANT_TIME = 30;

        /// Режим отладки, выводит в лог больше информации о работе модуля
        /// </summary>
        internal bool debug_mode = false;

        private int fileserver_port = 4747;
        private int fileserver_ssl_port = 4777;

        private int websocket_port = 4717;
        private int websocket_ssl_port = 0;

        private string host_name;
        private string network_client_id;

        private WSManager ws_connector = null;
        private WSManager wss_connector = null;
        private List<INetFlexFileHandler> ntf_handlers = new List<INetFlexFileHandler>();

        private static long startup_ts = 0;

        /// <summary>
        /// Идентификатор сервера в сети
        /// </summary>
        internal string ServerId { get; private set; } = "atlserv";

        /// <summary>
        /// Модуль отправки данных на сервер
        /// </summary>
        internal NetFlexFileSender Sender { get; private set; } = null;

        /// <summary>
        /// Модуль приема данных от сервера
        /// </summary>
        internal NetFlexFileReciever Receiver { get; private set; } = null;

        /// <summary>
        /// Модуль сжатия/распокви данных
        /// </summary>
        internal NetFlexFileCompressor Compressor { get; private set; } = null;

        /// <summary>
        /// Загрузчик данных NetFlex
        /// </summary>
        internal NetFlexDownloader Downloader { get; private set; } = null;

        /// <summary>
        /// Режим работы модуля NetFlex
        /// </summary>
        internal NetFlexMode Mode { get; private set; } = NetFlexMode.DEFAULT;

        /// <summary>
        /// Конфигурация сети для TNet коннектора
        /// </summary>
        private NetConfig WebsocketConfig = null;

        /// <summary>
        /// Конфигурация сети для FileServer коннектора
        /// </summary>
        private NetConfig FileServerConfig = null;

        /// <summary>
        /// Режим работы WebSocket коннектора для подключения к сети
        /// </summary>
        public NetConectionMode WebsocketMode { get; private set; } = NetConectionMode.NONE;

        /// <summary>
        /// Возвращает False если сеть интернет недоступна
        /// </summary>
        public bool IsNetworkAvailable { get { return (ws_connector != null) ? ws_connector.Connection.IsNetworkAvailable : false; } }

        private bool IpResolveRequired = true;

        long ts = 0;

        /// <summary>
        /// Возвращает синглтон экземпляр класса NetFlex
        /// </summary>
        /// <returns></returns>
        public static NetFlex Instance()
        {
            if (instance == null)
            {
                instance = new NetFlex();
                instance.SetNetworkClientId(GetRandomUniq(6));

                if(startup_ts == 0)
                    startup_ts = GetAbsoluteTime();
            }
            return instance;
        }

        public static void InitNetFlex(NetflexConfig config, string machine_id, bool debug_mode, params INetFlexFileHandler[] handlerList)
        {
            Instance();
            instance.SetWebsocketPorts(config.PORT_WS, config.PORT_WS_SSL);
            instance.SetFileserverPorts(config.PORT_WSFS, config.PORT_WS_SSL);

            instance.SetNetFlexMode(NetFlexMode.DEFAULT);
            instance.SetWebsocketMode(NetConectionMode.ENABLED);

            instance.SetHostname(config.SERVER_URL);
            instance.SetNetworkServerId(config.SERVER_ID);
            instance.SetNetworkClientId(/*NetflexConfig.Get().MACHINE_ID + */machine_id);
            instance.IpResolveRequired = config.IP_RESOLVE;

            foreach (var handler in handlerList)
            {
                instance.AddNetFileHandler(handler);
            }

            instance.SetDebugMode(debug_mode);
            instance.Initialize();
        }

        public void Initialize()
        {
            if (Receiver == null)
            {
                Receiver = new NetFlexFileReciever();
                XLogger.Log("[NetFlex] NetFlexFileReciever has been initialized");
            }

            if (Sender == null)
            {
                Sender = new NetFlexFileSender();
                XLogger.Log("[NetFlex] NetFlexFileSender has been initialized");
            }

            if (Compressor == null)
            {
                Compressor = new NetFlexFileCompressor();
                XLogger.Log("[NetFlex] NetFlexFileCompressor has been initialized");
            }
        }

        /// <summary>
        /// Устанавливает флаг отладки модуля
        /// </summary>
        /// <param name="enabled"></param>
        public void SetDebugMode(bool enabled)
        {
            debug_mode = enabled;
            WSConnectionPool.GetInstance().SetDebugMode(enabled);
            if (ws_connector != null) ws_connector.SetDebugMode(enabled);
        }

        IEnumerator UpdateCoroutine()
        {
            while (true)
            {
                WSConnectionPool.GetInstance().Update();

                if (Downloader != null)
                {
                    Downloader.UpdateDownloader();
                }
                yield return null;
            }
        }

        public void Update()
        {
            if(Sender != null)
                Sender.Update();

            if(Receiver != null)
                Receiver.Update();

            if (wss_connector != null)
                wss_connector.Update();

            if (ws_connector != null)
                ws_connector.Update();

            if (Compressor != null)
                Compressor.Update();

#if ATLAS_NODE
            WSConnectionPool.GetInstance().IsSuspended = false;
#else
            WSConnectionPool.GetInstance().IsSuspended = (WebsocketMode == NetConectionMode.DISABLED);
#endif
            if (GetTimePassed(ts) > 0.03f)
            {
                ts = GetAbsoluteTime();
            }
            else
            {
                return;
            }

#if !UNITY_WEBGL
            if (WebsocketMode != NetConectionMode.DISABLED)
            {
                if (WebsocketMode == NetConectionMode.ENABLED)
                {
                    if (ws_connector == null)
                    {
                        WebsocketConfig = new NetConfig() { Host = host_name, IpEndpoint = host_name, Port = websocket_port, Timeout = 10, Secure = false };
                        ws_connector = Create_NetFlexConnector_WS(WebsocketConfig);
                    }
                }
                else if (WebsocketMode == NetConectionMode.SECURE)
                {
                    if (wss_connector == null)
                    {
                        WebsocketConfig = new NetConfig() { Host = host_name, IpEndpoint = host_name, Port = websocket_ssl_port, Timeout = 10, Secure = true };
                        wss_connector = Create_NetFlexConnector_WS(WebsocketConfig);
                    }
                }
                else if (WebsocketMode == NetConectionMode.DUPLEX)
                {
                    if (ws_connector == null)
                    {
                        WebsocketConfig = new NetConfig() { Host = host_name, IpEndpoint = host_name, Port = websocket_port, Timeout = 10, Secure = false };
                        ws_connector = Create_NetFlexConnector_WS(WebsocketConfig);
                    }

                    if (wss_connector == null)
                    {
                        NetConfig WebsocketSslConfig = new NetConfig(WebsocketConfig);
                        WebsocketSslConfig.Secure = true;
                        WebsocketSslConfig.Port = websocket_ssl_port;
                        wss_connector = Create_NetFlexConnector_WS(WebsocketSslConfig);
                    }
                }
            }

            if (Downloader != null)
            {
                if (FileServerConfig == null)
                {
                    if (WebsocketConfig != null)
                    {
                        FileServerConfig = new NetConfig(WebsocketConfig);
                    }
                    else
                    {
                        FileServerConfig = new NetConfig() { Host = host_name, IpEndpoint = host_name, Timeout = 30 };
                    }

                    FileServerConfig.Secure = (WebsocketMode == NetConectionMode.SECURE);
                    FileServerConfig.Port = (FileServerConfig.Secure) ? fileserver_ssl_port : fileserver_port;
                    Downloader.SetNetworkConfig(FileServerConfig);
                }
                else
                {
                    Downloader.UpdateDownloader();
                }
            }
#else
            if (WebsocketMode == NetConectionMode.SECURE && wss_connector == null)
            {
                WebsocketConfig = new NetConfig() { Host = host_name, IpEndpoint = host_name, Port = websocket_port, Timeout = 30, Secure = true };
                wss_connector = Create_NetFlexConnector_WS(WebsocketConfig);
                StartCoroutine("UpdateCoroutine");
            }
#endif
        }

        /// <summary>
        /// Устанавливает режим работы NetFlex
        /// </summary>
        /// <param name="host_name"></param>
        public void SetNetFlexMode(NetFlexMode mode)
        {
            Mode = mode;
        }

        /// <summary>
        /// Устанавливает адрес сервера
        /// </summary>
        /// <param name="host_name"></param>
        /// <param name="resolve_ip"></param>
        public void SetHostname(string host_name, bool resolve_ip = true)
        {
            if (this.host_name != host_name)
            {
                this.host_name = host_name;
                IpResolveRequired = resolve_ip;
            }
        }

        /// <summary>
        /// Устанавливает порты WebSocket сервера
        /// </summary>
        /// <param name="port"></param>
        /// <param name="ssl_port"></param>
        public void SetWebsocketPorts(int port, int ssl_port)
        {
            websocket_port = port;
            websocket_ssl_port = ssl_port;
        }

        /// <summary>
        /// Устанавливает порты файлового сервера
        /// </summary>
        /// <param name="port"></param>
        /// <param name="ssl_port"></param>
        public void SetFileserverPorts(int port, int ssl_port)
        {
            fileserver_port = port;
            fileserver_ssl_port = ssl_port;
        }

        /// <summary>
        /// Устанавливает режим работы по WebSocket подключению
        /// </summary>
        /// <param name="mode"></param>
        public void SetWebsocketMode(NetConectionMode mode)
        {
            WebsocketMode = mode;
        }

        /// <summary>
        /// Устанавливает идентификатор сервера в сети
        /// </summary>
        /// <returns></returns>
        public void SetNetworkServerId(string serverId)
        {
            ServerId = serverId;
        }

        /// <summary>
        /// Устанавливает публичный идентификатор клиента в сети
        /// </summary>
        /// <returns></returns>
        public void SetNetworkClientId(string networkId)
        {
            if (network_client_id != networkId)
            {
                network_client_id = networkId;

                if (ws_connector != null)
                {
                    ws_connector.NetworkId = networkId;
                    ws_connector.SetSessionClientName(networkId);
                }

                if (wss_connector != null)
                {
                    wss_connector.NetworkId = networkId;
                    wss_connector.SetSessionClientName(networkId);
                }
            }
        }

        /// <summary>
        /// Возвращает публичный идентификатор клиента в сети
        /// </summary>
        /// <returns></returns>
        public string GetNetworkClientId()
        {
            return network_client_id;
        }

        /// <summary>
        /// Возвращает состояние подключения
        /// </summary>
        /// <returns></returns>
        public NetConnectionStatus GetConnectionStatus()
        {
            WSState ws_state = (ws_connector != null) ? ws_connector.State : WSState.OFFLINE;
            WSState wss_state = (wss_connector != null) ? wss_connector.State : WSState.OFFLINE;
            //WSState tnet_state = (tn_connector != null) ? wss_connector.State : WSState.OFFLINE;
            if (ws_state == WSState.ONLINE || wss_state == WSState.ONLINE)
            {
                return NetConnectionStatus.ONLINE;
            }
            else if (ws_state == WSState.CONNECTING || wss_state == WSState.CONNECTING)
            {
                return NetConnectionStatus.CONNECTING;
            }
            else if (ws_state == WSState.AWAIT || wss_state == WSState.AWAIT)
            {
                return NetConnectionStatus.CONNECTING;
            }
            return NetConnectionStatus.OFFLINE;
        }

        /// <summary>
        /// Возвращает экземпляр класса NetFlexDownloader
        /// </summary>
        /// <returns></returns>
        public NetFlexDownloader GetDownloader()
        {
            if (Downloader == null)
            {
                Downloader = new NetFlexDownloader();

                if (WebsocketConfig != null)
                {
                    FileServerConfig = new NetConfig(WebsocketConfig);
                    FileServerConfig.Secure = WebsocketConfig.Secure;
                    FileServerConfig.Port = FileServerConfig.Secure ? fileserver_ssl_port : fileserver_port;
                    Downloader.SetNetworkConfig(FileServerConfig);
                }

                Downloader.CreateAgents(DOWNLOAD_AGENTS_COUNT);
            }
            return Downloader;
        }

        /// <summary>
        /// Добавляет обработчик NetFile
        /// </summary>
        /// <param name="handler"></param>
        /// <returns>Возвращает True если обработчик был успешно добавлен</returns>
        public bool AddNetFileHandler(INetFlexFileHandler handler)
        {
            if (handler == null && ntf_handlers.Contains(handler))
                return false;

            ntf_handlers.Add(handler);
            return true;
        }

        /// <summary>
        /// Отправляет команду на WebSocket сервер
        /// </summary>
        /// <param name="command">Команда на сервер</param>
        public bool SendWSCommand(WSCommand command)
        {
            bool sent_success = false;
            if (ws_connector != null && ws_connector.Connection.State == WSState.ONLINE)
            {
                sent_success = sent_success || ws_connector.Send("", WSCommand.Serialize(command));
            }

            if (wss_connector != null && wss_connector.Connection.State == WSState.ONLINE)
            {
                sent_success = sent_success || wss_connector.Send("", WSCommand.Serialize(command));
            }
            return sent_success;
        }

        /// <summary>
        /// Отправляет данные другому клиенту на сервере через указанный источник
        /// </summary>
        /// <param name="receiver_id"></param>
        /// <param name="data"></param>
        /// <param name="source"></param>
        /// <returns>Возвращает True если данные были отправлены</returns>
        public bool SendData(string receiver_id, byte[] data, string source)
        {
            bool sent_success = false;
            if (source == NetFileSource.WEB_SOCKET)
            {
                if (ws_connector != null && ws_connector.State == WSState.ONLINE)
                {
                    if (debug_mode) XLogger.Log("[NetFlex] Send Data packet to <" + receiver_id + "> through WS");
                    sent_success = sent_success || ws_connector.Send(receiver_id, data);
                }
            }

            if (source == NetFileSource.WEB_SOCKET_SSL)
            {
                if (wss_connector != null && wss_connector.State == WSState.ONLINE)
                {
                    if (debug_mode) XLogger.Log("[NetFlex] Send Data packet to <" + receiver_id + "> through WSS");
                    sent_success = sent_success || wss_connector.Send(receiver_id, data);
                }
            }
            return sent_success;
        }

        /// <summary>
        /// Отправляет запрос указанному клиенту на сервере
        /// </summary>
        public static void Send(string command, byte[] data, bool compress, bool important = true)
        {
            if (Instance().Sender != null)
            {
                Instance().Sender.SendCommand(command, "", data, compress, important);
            }
        }

        /// <summary>
        /// Отправляет запрос указанному клиенту на сервере
        /// </summary>
        public static void Send(string command, string message, byte[] data, bool compress, bool important = true)
        {
            if (Instance().Sender != null)
            {
                Instance().Sender.SendCommand(command, message, data, compress, important);
            }
        }

        /// <summary>
        /// Вовзвращает прогресс загрузки файла с указанной командой
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <returns></returns>
        public float GetNetFileDownloadProgress(string command, string message = null)
        {
            if (Receiver != null)
            {
                return Receiver.GetNetFileDownloadProgress(command, message);
            }
            return -1;
        }

        /// <summary>
        /// Вовзвращает прогресс загрузки Downloader
        /// </summary>
        /// <returns></returns>
        public float GetDownloadProgress()
        {
            if (Downloader != null)
            {
                return Downloader.GetCurrentDataListProgress();
            }
            return -1;
        }

        /// <summary>
        /// Вовзвращает прогресс отправки файла с указанной командой
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <returns></returns>
        public float GetNetFileUploadProgress(string command, string message = null)
        {
            if (Sender != null)
            {
                return Sender.GetNetFileUploadProgress(command, message);
            }
            return -1;
        }

        /// <summary>
        /// Проверяет наличие отправляемой команды на сервер
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <returns>Возвращает True если команда в очереди на отправку</returns>
        public bool IsCommandInQueue(string command, string message = null)
        {
            if (Sender != null)
            {
                return Sender.IsCommandExists(command, message);
            }
            return false;
        }

        /// <summary>
        /// Отменяет отправку команды на сервер
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        public void CancelCommand(string command, string message = null)
        {
            if (Sender != null)
            {
                Sender.CancelCommand(command, message);
            }
        }

        /// Отменяет отправку команды на сервер
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        public void CancelNetFile(string command, string message = null)
        {
            if (Receiver != null)
            {
                Receiver.CancelNetFile(command, message);
            }
        }

        /// <summary>
        /// Создает коннектор клиента для сети WebSocket
        /// </summary>
        private WSManager Create_NetFlexConnector_WS(NetConfig Config)
        {
            WSManager connector = null;
            try
            {
                connector = new WSManager();

                connector.NetworkId = network_client_id;
                connector.SecurityToken = "";

                connector.OnConnect = OnWSConnectCallback;
                connector.OnDisconnect = OnWSDisconnectCallback;
                connector.OnCommand = OnWSCommandCallback;
                connector.OnData = OnWSDataCallback;
                connector.Connect(Config.Host, Config.Port.ToString(), "", Config.Secure, IpResolveRequired);
                connector.SetDebugMode(debug_mode);
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
                connector = null;
            }
            return connector;
        }

        /// <summary>
        /// Колбэк на установку связи с севрером по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSConnectCallback(object data)
        {
            DispatchTrigger(new XEvent(NetFlex.CONNECTED, this));
            XLogger.Log("[NetFlex] WebSocket is Online");
        }

        /// <summary>
        /// Колбэк на потерю связи с севрером по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSDisconnectCallback(object data)
        {
            DispatchTrigger(new XEvent(NetFlex.DISCONNECTED, this));
            XLogger.Log("[NetFlex] WebSocket is Offline");
        }

        /// <summary>
        /// Колбэк на получение данных от сервера по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSDataCallback(object data)
        {
            if (Receiver != null)
            {
                Receiver.OnNetDataRecieved((byte[])data);
            }
            //DispatchTrigger(new Trigger(NetFlexTrigrer.DATA_RECEIVED, ws_connector, data));
        }

        /// <summary>
        /// Колбэк на получение команды от сервера по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSCommandCallback(object data)
        {
            try
            {
                WSCommand command = WSCommand.Deserialize((byte[])data);
                DispatchTrigger(new XEvent(NetFlex.COMMAND_RECEIVED, this, command));
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
            }
        }

        /// <summary>
        /// Вызывает обработчик принятого NetFile
        /// </summary>
        /// <param name="netFile"></param>
        internal void HandleNetFlexFile(NetFlexFile netFile)
        {
            foreach (INetFlexFileHandler handler in ntf_handlers)
            {
                try
                {
                    handler?.HandleFile(netFile);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                }
            }
            DispatchTrigger(new XEvent(NetFlex.NETFILE_RECEIVED, this, netFile));
        }

        internal static float GetTime()
        {
            return GetTimePassed(startup_ts);
        }

        /// <summary>
        /// Возвращает текущее время в милисекундах
        /// </summary>
        /// <returns></returns>
        internal static long GetAbsoluteTime()
        {
            return (System.DateTime.Now.Ticks / 10000L);
        }

        /// <summary>
        /// Вовзвращет время, прошедшее с указанного момента, в секундах
        /// </summary>
        /// <param name="timestamp"></param>
        /// <returns></returns>
        internal static float GetTimePassed(long timestamp)
        {
            return (float)((GetAbsoluteTime() - timestamp) / 1000.0);
        }

        /// <summary>
        /// Генерирует случайный строковый идентификатор указанной длины
        /// </summary>
        /// <param name="length"></param>
        /// <returns></returns>
        internal static string GetRandomUniq(int length)
        {
            string glyphs = "abcdefghijklmnopqrstuvwxyz";
            string id = "";
            int charAmount = length;

            for (int i = 0; i < charAmount; i++)
            {
                id += glyphs[random.Next(glyphs.Length)];
            }

            id += "-";

            for (int i = 0; i < charAmount; i++)
            {
                id += glyphs[random.Next(glyphs.Length)];
            }

            return id;
        }
    }
}