using System.Collections;
using System.Collections.Generic;
using VRNext.WebSocket;

namespace VRNext.Network
{
    public class NetFlexDownloadAgent : XEventDispatcher
    {
        /// <summary>
        /// Текущий список загружаемых данных
        /// </summary>
        private NetFlexDownloadDataList DownloadList = null;

        /// <summary>
        /// Текущий список загружаемых заголовучных данных
        /// </summary>
        private List<NetFlexDownloadData> DataInfoList = null;

        /// <summary>
        /// Текущий список загружаемых данных
        /// </summary>
        private NetFlexDownloadChunk DownloadChunk = null;

        /// <summary>
        /// Конфигурация подключения к файловому серверу
        /// </summary>
        private NetFlex.NetConfig NetworkConfig = null;

        /// <summary>
        /// Websocket коннектор к файловому серверу
        /// </summary>
        private WSManager WSConnector = null;

        /// <summary>
        /// Уникальный идентификатор последнего запроса на файловый сервер
        /// </summary>
        private string RequestUniq = null;

        /// <summary>
        /// Время отправки последнего запроса на файловый сервер
        /// </summary>
        private long RequestSent = 0;

        /// <summary>
        /// Состояние подключения по WebSocket
        /// </summary>
        private NetFlex.NetConnectionStatus ConnectionStatus = NetFlex.NetConnectionStatus.OFFLINE;

        // Update is called once per frame
        public void UpdateAgent()
        {
            if (DownloadList != null || DataInfoList != null)
            {
                if (CheckWebsocketConnector())
                {
                    if (IsConnectorOnline())
                    {
                        if (DataInfoList != null)
                        {
                            if(string.IsNullOrEmpty(RequestUniq))
                            {
                                if (!RequestDataInfo())
                                {
                                    RequestUniq = null;
                                }
                            }
                        }
                        else
                        {
                            if (string.IsNullOrEmpty(RequestUniq) && DownloadChunk == null)
                            {
                                DownloadChunk = DownloadList.GetNextChunk();
                                if (DownloadChunk != null)
                                {
                                    if (DownloadChunk.Lock(this))
                                    {
                                        if(!RequestChunkData())
                                        {
                                            DownloadChunk.Unlock();
                                            DownloadChunk = null;
                                        }
                                    }
                                    else
                                    {
                                        DownloadChunk = null;
                                        XLogger.LogError("[NetFlex] " + ": DataChunk locked by another Agent!");
                                    }
                                }
                                else
                                {
                                    DownloadList = null;
                                }
                            }
                            else
                            {
                                if (NetFlex.GetTimePassed(RequestSent) > NetFlex.AWAIT_UNIMPORTANT_TIME)
                                {
                                    if (DownloadChunk != null)
                                    {
                                        DownloadChunk.Unlock();
                                        DownloadChunk = null;
                                    }

                                    if (RequestUniq != null)
                                    {
                                        RequestUniq = null;
                                        XLogger.LogError("[NetFlex] " + ": Waiting time of downloading exceed. Request has been canceled.");
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        if (!string.IsNullOrEmpty(RequestUniq))
                        {
                            DownloadChunk.Unlock();
                            DownloadChunk = null;
                            XLogger.LogError("[NetFlex] " + ": Connection lost while file downloading. Request has been canceled.");
                        }
                    }
                }
            }
            /*
            else
            {
                if (WSConnector != null)
                {
                    if (WSConnector.Connection.State != WSState.OFFLINE)
                    {
                        WSConnector.Disconnect();
                        ConnectionStatus = NetFlex.NetConnectionStatus.OFFLINE;
                    }
                }
            }
            */
        }

        /// <summary>
        /// Устанавливает конфигурацию подключения к файловому серверу
        /// </summary>
        /// <param name="netConfig"></param>
        internal void SetNetworkConfig(NetFlex.NetConfig netConfig)
        {
            NetworkConfig = netConfig;
            if (WSConnector != null)
            {
                if (WSConnector.Connection.State != WSState.OFFLINE)
                {
                    WSConnector.Disconnect();
                }
            }
        }

        /// <summary>
        /// Назначет список загрузки данных
        /// </summary>
        /// <param name="dataList"></param>
        internal void DownloadDataList(NetFlexDownloadDataList dataList)
        {
            DownloadList = dataList;
        }

        /// <summary>
        /// Запрашивает заголовочную информацию списка данных
        /// </summary>
        /// <param name="dataList"></param>
        internal void RequestDataInfo(List<NetFlexDownloadData> dataList)
        {
            DataInfoList = dataList;
        }

        /// <summary>
        /// Возвращает статус простоя агента
        /// </summary>
        /// <returns></returns>
        internal bool IsIdle()
        {
            return DataInfoList == null || DownloadList == null;
        }

        /// <summary>
        /// Прерывает текущую загрузку агента
        /// </summary>
        internal void Abort()
        {
            if(DownloadChunk != null)
            {
                DownloadChunk.Unlock();
                DownloadChunk = null;
            }

            if (DataInfoList != null)
            {
                DataInfoList = null;
            }

            if (DownloadList != null)
            {
                DownloadList = null;
            }

            if (RequestUniq != null)
            {
                RequestUniq = null;
                XLogger.Log("[NetFlex] " +": DownloadAgent successfully aborted!");
            }
        }

        /// <summary>
        /// Прерывает текущую загрузку агента
        /// </summary>
        internal void Disconnect()
        {
            WSConnector.Disconnect();
            ConnectionStatus = NetFlex.NetConnectionStatus.OFFLINE;
            XLogger.Log("[NetFlex] " +  ": DownloadAgent successfully disconnected!");
        }

        /// <summary>
        /// Запрашивает заголовочную информацию списка данных
        /// </summary>
        private bool RequestDataInfo()
        {
            RequestUniq = NetFlex.GetRandomUniq(8);
            List<WSArgument> arguments = new List<WSArgument>();
            arguments.Add(new WSArgument(WSArgumentType.STRING, RequestUniq));

            for (int i = 0; i < DataInfoList.Count; i++)
            {
                arguments.Add(new WSArgument(WSArgumentType.STRING, DataInfoList[i].Uniq));
                arguments.Add(new WSArgument(WSArgumentType.STRING, DataInfoList[i].Directory));
                arguments.Add(new WSArgument(WSArgumentType.STRING, DataInfoList[i].RelativePath));
            }

            WSCommand command = new WSCommand(WSOperation.REQUEST_DATA_INFO, arguments);
            if (WSConnector.Send("", WSCommand.Serialize(command)))
            {
                RequestSent = NetFlex.GetAbsoluteTime();
                return true;
            }
            return false;
        }

        /// <summary>
        /// Отправляет запрос на получение данных куска файла. 
        /// </summary>
        private bool RequestChunkData()
        {
            RequestUniq = NetFlex.GetRandomUniq(8);

            List<WSArgument> arguments = new List<WSArgument>();
            arguments.Add(new WSArgument(WSArgumentType.STRING, RequestUniq));
            arguments.Add(new WSArgument(WSArgumentType.INTGER, NetFlex.DOWNLOAD_PART_BYTES));
            arguments.Add(new WSArgument(WSArgumentType.STRING, DownloadChunk.DownloadData.Uniq));
            arguments.Add(new WSArgument(WSArgumentType.STRING, DownloadChunk.DownloadData.Directory));
            arguments.Add(new WSArgument(WSArgumentType.STRING, DownloadChunk.DownloadData.RelativePath));
            arguments.Add(new WSArgument(WSArgumentType.INTGER, DownloadChunk.Offset));
            arguments.Add(new WSArgument(WSArgumentType.INTGER, DownloadChunk.Length));

            WSCommand command = new WSCommand(WSOperation.REQUEST_DATA_PART, arguments);
            if (WSConnector.Send("", WSCommand.Serialize(command)))
            {
                RequestSent = NetFlex.GetAbsoluteTime();
                return true;
            }
            return false;
        }

        /// <summary>
        /// Обработчик загрузки заголовочной информации данных
        /// </summary>
        /// <param name="arguments"></param>
        private void OnDataInfoReceived(List<WSArgument> arguments)
        {
            string requestUniq = arguments[0].GetString();
            if (RequestUniq == requestUniq)
            {
                int argIndex = 1;
                while (argIndex + 2 < arguments.Count)
                {
                    string dataUniq = arguments[argIndex].GetString();
                    string dataHash = arguments[argIndex + 1].GetString();
                    int dataSize = arguments[argIndex + 2].GetInt();
                    string relativePath = arguments[argIndex + 3].GetString();

                    NetFlexDownloadData dataInfo = DataInfoList.Find(x => x.Uniq == dataUniq);
                    if (dataInfo != null)
                    {
                        dataInfo.SetDataInfo(relativePath, dataSize, dataHash);
                    }
                    argIndex += 4;
                }

                DispatchTrigger(new XEvent(NetFlex.DATA_INFO_RECEIVED, this, DataInfoList));

                RequestUniq = null;
                DataInfoList = null;
            }
        }

        /// <summary>
        /// Обработчик загрузки блока данных
        /// </summary>
        /// <param name="arguments"></param>
        private void OnDataReceived(List<WSArgument> arguments)
        {
            string request_uniq = arguments[0].GetString();
            if (RequestUniq == request_uniq)
            {
                string dataUniq = arguments[1].GetString();
                string dataPartHash = arguments[2].GetString();
                byte[] dataPartData = arguments[3].GetBytes();

                DownloadChunk.AcceptData(dataPartData);
                DownloadChunk.Unlock();

                DispatchTrigger(new XEvent(NetFlex.DATA_PART_RECEIVED, this, DownloadChunk));
                DownloadChunk = null;
                RequestUniq = null;
            }
        }

        /// <summary>
        /// Проверяет наличие Websocket коннектора, и создает его если он отсутствует
        /// </summary>
        /// <returns></returns>
        private bool CheckWebsocketConnector()
        {
            if (WSConnector == null)
            {
                WSConnector = new WSManager();
                WSConnector.NetworkId = NetFlex.GetRandomUniq(6);
                WSConnector.SecurityToken = "";
                WSConnector.DoPing = false;

                WSConnector.OnConnect = OnWSConnectCallback;
                WSConnector.OnDisconnect = OnWSDisconnectCallback;
                WSConnector.OnCommand = OnWSCommandCallback;
                WSConnector.OnData = OnWSDataCallback;
                return false;
            }
            return true;
        }

        /// <summary>
        /// Проверяет статус Websocket соединения и создает его, если оно отсутсвует
        /// </summary>
        /// <returns></returns>
        private bool IsConnectorOnline()
        {
            if (WSConnector != null)
            {
                if (WSConnector.Connection != null)
                {
                    if (ConnectionStatus != NetFlex.NetConnectionStatus.ONLINE)
                    {
                        if (WSConnector.Connection.State == WSState.ONLINE)
                        {
                            ConnectionStatus = NetFlex.NetConnectionStatus.ONLINE;
                            return true;
                        }
                        else if (WSConnector.Connection.State == WSState.OFFLINE && NetworkConfig != null)
                        {
                            //WSConnector.Connect(NetworkConfig);
                            //WSConnector.Connect(NetworkConfig.Host, NetworkConfig.Port.ToString(), "", false, true);
                            WSConnector.Connection.IsReconnectRequired = true;
                        }
                        return false;
                    }
                    else
                    {
                        return WSConnector.Connection.State == WSState.ONLINE;
                    }
                }
                else 
                {
                    if (NetworkConfig != null)
                    {
#if UNITY_WEBGL
                        WSConnector.Connect(NetworkConfig.Host, NetworkConfig.Port.ToString(), "", NetworkConfig.Secure, false);
#else
                        WSConnector.Connect(NetworkConfig.Host, NetworkConfig.Port.ToString(), "", NetworkConfig.Secure, true);
#endif
                    }
                    return false;
                }
            }
            return false;
        }

        /// <summary>
        /// Колбэк на установку связи с севрером по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSConnectCallback(object data)
        {
            ConnectionStatus = NetFlex.NetConnectionStatus.ONLINE;
            if (NetFlex.Instance().debug_mode)
            {
                XLogger.Log("[NetFlex] " +  ": Downloader WebSocket is Online");
            }
        }

        /// <summary>
        /// Колбэк на потерю связи с севрером по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSDisconnectCallback(object data)
        {
            WSConnector.Disconnect();
            ConnectionStatus = NetFlex.NetConnectionStatus.OFFLINE;
            if (NetFlex.Instance().debug_mode)
            {
                XLogger.Log("[NetFlex] " + ": Downloader WebSocket is Offline");
            }
        }

        /// <summary>
        /// Колбэк на получение данных от сервера по WebSocket подключению
        /// </summary>
        /// <param name="data"></param>
        private void OnWSDataCallback(object data)
        {
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
                if (command.operation == WSOperation.RESPONSE_DATA_INFO)
                {
                    OnDataInfoReceived(command.arguments);
                }
                else if (command.operation == WSOperation.RESPONSE_DATA_PART)
                {
                    OnDataReceived(command.arguments);
                }
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
            }
        }
    }
}