using System.Linq;
using System.Collections.Generic;
using VRNext.WebSocket;

namespace VRNext.Network
{
    /// <summary>
    /// Часть загружаемых данных
    /// </summary>
    public class NetFlexDownloadChunk
    {
        private static int counter = 1;
        public NetFlexDownloadAgent AgentLock { private set; get; }
        public NetFlexDownloadData DownloadData { private set; get; }
        public int Uniq { private set; get; }
        public int Offset { private set; get; }
        public int Length { private set; get; }
        public byte[] ChunkData { private set; get; }

        internal NetFlexDownloadChunk(NetFlexDownloadData downloadData, int offset, int length)
        {
            Uniq = counter; counter++;
            DownloadData = downloadData;
            Offset = offset;
            Length = length;
        }

        internal void AcceptData(byte[] data)
        {
            ChunkData = data;
            DownloadData.UpdateProgress();
        }

        internal bool Lock(NetFlexDownloadAgent agent)
        {
            if(AgentLock == null || AgentLock == agent)
            {
                AgentLock = agent;
                return true;
            }
            return false;
        }

        internal void Unlock()
        {
            AgentLock = null;
        }

        internal void Dispose()
        {
            ChunkData = null;
        }

        internal bool IsLocked()
        {
            return AgentLock != null;
        }
    }

    /// <summary>
    /// Информация о загружаеммых данных
    /// </summary>
    public class NetFlexDownloadData : XEventDispatcher
    {
        public enum DataType { DEFAULT, CACHE, OBJECT, MATERIAL, RENDER };
        public string Uniq { private set; get; }
        public string Directory { private set; get; }
        public string RelativePath { private set; get; }
        public string DataHash { private set; get; }
        public int BytesTotal { private set; get; }
        public int BytesLoaded { internal set; get; }
        public int Attempt { private set; get; }
        public int Index { private set; get; }
        public NetFlexDownloadChunk[] DataChunks { private set; get; }
        public DataType Type = DataType.DEFAULT;

        public NetFlexDownloadData(string directory, string relative_path, string data_hash = null, int length = 0)
        {
            Uniq = NetFlex.GetRandomUniq(4);
            Directory = directory;
            RelativePath = relative_path;
            DataHash = data_hash;
            BytesTotal = length;
            BytesLoaded = 0;
            Attempt = 0;
        }

        /// <summary>
        /// Очищает загруженные данные из памяти
        /// </summary>
        public byte[] GetData()
        {
            byte[] data = new byte[BytesTotal];
            for (int i = 0; i < DataChunks.Length; i++)
            {
                if(DataChunks[i].ChunkData == null)
                {
                    throw new System.Exception("NetFileDownloadData is not downloaded yet!");
                }
                System.Array.Copy(DataChunks[i].ChunkData, 0, data, DataChunks[i].Offset, DataChunks[i].Length);
            }

            return data;
        }

        /// <summary>
        /// Очищает загруженные данные из памяти
        /// </summary>
        public void Dispose()
        {
            for (int i = 0; i < DataChunks.Length; i++)
                DataChunks[i].Dispose();

            DataChunks = null;
        }

        public bool IsDownloaded()
        {
            return BytesTotal == BytesLoaded;
        }

        internal bool ChopToChunks()
        {
            if (BytesTotal > 0 && DataChunks == null)
            {
                int chunksCount = (int)System.Math.Ceiling(BytesTotal / (double)NetFlex.DOWNLOAD_PART_BYTES);
                int chunkOffset = 0;
                int chunkLength = 0;

                DataChunks = new NetFlexDownloadChunk[chunksCount];
                for (int i = 0; i < chunksCount; i++)
                {
                    chunkLength = System.Math.Min(BytesTotal - chunkOffset, NetFlex.DOWNLOAD_PART_BYTES);
                    DataChunks[i] = new NetFlexDownloadChunk(this, chunkOffset, chunkLength);
                    chunkOffset += chunkLength;
                }
                return chunksCount > 0;
            }
            return false;
        }

        internal void SetIndex(int index)
        {
            Index = index;
        }

        internal void SetDataInfo(string relativePath, int length, string hash)
        {
            RelativePath = relativePath;
            BytesTotal = length;
            BytesLoaded = 0;
            DataHash = hash;
        }

        internal int GetBytesLeft()
        {
            return BytesTotal - BytesLoaded;
        }

        internal void UpdateProgress()
        {
            if (DataChunks != null)
            {
                BytesLoaded = 0;
                for (int i = 0; i < DataChunks.Length; i++)
                {
                    if (DataChunks[i].ChunkData != null)
                        BytesLoaded += DataChunks[i].ChunkData.Length;
                }
            }
        }

        internal void Reset()
        {
            for (int i = 0; i < DataChunks.Length; i++)
            {
                DataChunks[i].Dispose();
            }
        }
    }

    /// <summary>
    /// Список загружаемых данных
    /// </summary>
    public class NetFlexDownloadDataList : XEventDispatcher
    {
        private List<NetFlexDownloadData> DataList;
        private List<NetFlexDownloadChunk> ChunkList;

        public string Name { private set; get; }
        public long BytesTotal { private set; get; } = 0;
        public long BytesLoaded { private set; get; } = 0;
        public int FilesLoaded { private set; get; } = 0;
        public int Priority { private set; get; } = 0;
        public bool IsDownloading { private set; get; } = false;
        public bool IsAborted { private set; get; } = false;
        public long DownloadStart { private set; get; } = 0;
        public float Progress { private set; get; } = 0;
        public float Speed { private set; get; } = 0;

        public NetFlexDownloadDataList(string name, int priority = 0)
        {
            Name = name;
            Priority = priority;
            DataList = new List<NetFlexDownloadData>();
        }

        /// <summary>
        /// Добавляет данные в список загрузки
        /// ВНИМАНИЕ! Добавление файлов недопускается, когда список начал загружаться!
        /// </summary>
        /// <param name="dataInfo"></param>
        public void Add(NetFlexDownloadData dataInfo)
        {
            if(IsDownloading)
            {
                throw new System.Exception("NetFlexDataList already downloading, adding is unavailable!");
            }

            if(DataList.Find(x => x.Type == dataInfo.Type && x.RelativePath == dataInfo.RelativePath) == null)
                DataList.Add(dataInfo);
        }

        /// <summary>
        /// Прерыввает загрузку списка данных
        /// </summary>
        public void Abort()
        {
            foreach (NetFlexDownloadData data in DataList)
                data.ClearTriggerListeners();

            ClearTriggerListeners();
            IsAborted = true;
        }

        internal void Refresh()
        {
            BytesTotal = 0;
            BytesLoaded = 0;
            for (int i = 0; i < DataList.Count; i++)
            {
                BytesTotal += DataList[i].BytesTotal;
                BytesLoaded += DataList[i].BytesLoaded;
            }

            FilesLoaded = 0;
            for (int i = 0; i < DataList.Count; i++)
            {
                if(DataList[i].IsDownloaded())
                {
                    FilesLoaded++;
                }
                else
                {
                    break;
                }
            }

            if(BytesTotal > 0)
                Progress = BytesLoaded / (float)BytesTotal;
            else
                Progress = 0;

            if (BytesLoaded > 0)
            {
                Speed = BytesLoaded / NetFlex.GetTimePassed(DownloadStart);
                Speed = (float)System.Math.Round((Speed / 1048576.0f) * 100) / 100.0f;
            }
            else
            {
                Speed = 0;
            }
        }

        internal NetFlexDownloadData GetDataInfo(string uniq)
        {
            return DataList.Find(x => x.Uniq == uniq);
        }

        internal NetFlexDownloadData GetDataInfo(int index)
        {
            return DataList[index];
        }

        internal NetFlexDownloadData GetCurrentDataInfo()
        {
            return FilesLoaded < DataList.Count ? DataList[FilesLoaded] : null;
        }

        internal int GetCurrentDataIndex()
        {
            return FilesLoaded;
        }

        internal int Count()
        {
            return DataList.Count;
        }

        internal List<NetFlexDownloadData> GetEmptyDataList()
        {
            return DataList.FindAll(x => x.BytesTotal == 0);
        }

        internal void StartDownloading()
        {
            DataList.RemoveAll(x => x.BytesTotal == 0);
            ChunkList = new List<NetFlexDownloadChunk>();
            for(int i = 0; i < DataList.Count; i++)
            {
                DataList[i].SetIndex(i);
                if (DataList[i].ChopToChunks())
                {
                    ChunkList.AddRange(DataList[i].DataChunks);
                }
            }

            Refresh();
            DownloadStart = NetFlex.GetAbsoluteTime();
            IsDownloading = true;
        }

        internal NetFlexDownloadChunk GetNextChunk()
        {
            if (IsDownloading && !IsDataDownloadComplete())
            {
                lock (ChunkList)
                {
                    for (int i = 0; i < ChunkList.Count; i++)
                    {
                        if (ChunkList[i].ChunkData == null && !ChunkList[i].IsLocked())
                        {
                            return ChunkList[i];
                        }
                    }
                }
            }
            return null;
        }

        internal void AcceptData(NetFlexDownloadData data)
        {
            if (IsDownloading && !IsDataDownloadComplete())
            {
                lock (ChunkList)
                {
                    foreach (NetFlexDownloadChunk chunk in data.DataChunks)
                        ChunkList.Remove(chunk);
                }
            }
        }

        internal bool IsDataDownloadComplete()
        {
            return DataList.Count == FilesLoaded;
        }
    }

    /// <summary>
    /// Загрузчик данных с файлового сервера
    /// </summary>
    public class NetFlexDownloader : XEventDispatcher
    {
        /// <summary>
        /// Очередь загрузки данных
        /// </summary>
        private List<NetFlexDownloadDataList> DownloadQueue = new List<NetFlexDownloadDataList>();

        /// <summary>
        /// Список агентов загрузки данных
        /// </summary>
        private List<NetFlexDownloadAgent> DownloadAgents = new List<NetFlexDownloadAgent>();

        /// <summary>
        /// Конфигурация подключения к файловому серверу
        /// </summary>
        private NetFlex.NetConfig NetworkConfig = null;

        /// <summary>
        /// Текущий загружаемый список данных
        /// </summary>
        private int QueueCurrrent = -1;

        /// <summary>
        /// Статус загрузки заголовочных данных
        /// </summary>
        private bool RequestInfoRequired = false;

        // Update is called once per frame
        public void UpdateDownloader()
        {
            foreach (NetFlexDownloadAgent agent in DownloadAgents)
                agent.UpdateAgent();

            if (DownloadQueue.Count > 0)
            {
                if (QueueCurrrent < 0)
                {
                    QueueCurrrent = 0;
                    for (int i = 1; i < DownloadQueue.Count; i++)
                    {
                        if (DownloadQueue[i].Priority > DownloadQueue[QueueCurrrent].Priority)
                        {
                            QueueCurrrent = i;
                        }
                    }
                }

                if (QueueCurrrent >= 0)
                {
                    if (!DownloadQueue[QueueCurrrent].IsDownloading)
                    {
                        if (!RequestInfoRequired)
                        {
                            List<NetFlexDownloadData> emptyDataList = DownloadQueue[QueueCurrrent].GetEmptyDataList();
                            if (emptyDataList.Count > 0)
                            {
                                NetFlexDownloadAgent agent = GetIdleAgent();
                                if (agent != null)
                                {
                                    RequestInfoRequired = true;
                                    agent.RequestDataInfo(emptyDataList);
                                }
                            }
                            else
                            {
                                DownloadQueue[QueueCurrrent].StartDownloading();
                            }
                        }
                    }

                    if (DownloadQueue[QueueCurrrent].IsDownloading)
                    {
                        for (int i = 0; i < DownloadAgents.Count; i++)
                        {
                            if (DownloadAgents[i].IsIdle())
                            {
                                DownloadAgents[i].DownloadDataList(DownloadQueue[QueueCurrrent]);
                            }
                        }

                        if (DownloadQueue[QueueCurrrent].IsDataDownloadComplete())
                        {
                            DispatchTrigger(new XEvent(NetFlex.DATA_LIST_RECEIVED, this, DownloadQueue[QueueCurrrent]));
                            DownloadQueue.RemoveAt(QueueCurrrent);
                            RequestInfoRequired = false;
                            QueueCurrrent = -1;
                            return;
                        }
                    }

                    if (DownloadQueue[QueueCurrrent].IsAborted)
                    {
                        AbortDownloadAgents();
                        DownloadQueue.RemoveAt(QueueCurrrent);
                        RequestInfoRequired = false;
                        QueueCurrrent = -1;
                        return;
                    }
                }
            }
        }

        /// <summary>
        /// Добавляет список загружаемых данных в очередь загрузки
        /// </summary>
        /// <param name="dataList"></param>
        public void AddDataList(NetFlexDownloadDataList dataList)
        {
            DownloadQueue.Add(dataList);
            UpdateDownloader();
        }

        /// <summary>
        /// Возвращает загружаемый список данных из очереди по имени
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public NetFlexDownloadDataList GetDataList(string name)
        {
            return DownloadQueue.Find(x => x.Name == name);
        }

        public void Abort()
        {
            for (int i = 0; i < DownloadAgents.Count; i++)
            {
                DownloadAgents[i].Abort();
            }

            for (int i = 0; i < DownloadQueue.Count; i++)
            {
                DownloadQueue[i].Abort();
            }

            DownloadQueue.Clear();
            ClearTriggerListeners();
        }

        /// <summary>
        /// Возвращает текущий загружаемый список данных
        /// </summary>
        /// <returns></returns>
        public NetFlexDownloadDataList GetCurrentDataList()
        {
            return (QueueCurrrent >= 0) ? DownloadQueue[QueueCurrrent] : null;
        }

        /// <summary>
        /// Возвращает текущий прогресс загружаемого список данных
        /// </summary>
        /// <returns></returns>
        public float GetCurrentDataListProgress()
        {
            NetFlexDownloadDataList dataList = GetCurrentDataList();
            return (dataList != null) ? dataList.Progress : -1;
        }
        
        /// <summary>
        /// Устанавливает конфигурацию подключения к файловому серверу
        /// </summary>
        /// <param name="netConfig"></param>
        internal void SetNetworkConfig(NetFlex.NetConfig netConfig)
        {
            NetworkConfig = netConfig;
            for (int i = 0; i < DownloadAgents.Count; i++)
            {
                DownloadAgents[i].SetNetworkConfig(NetworkConfig);
            }
        }

        internal void CreateAgents(int agentsCount)
        {
            for (int i = 0; i < agentsCount; i++)
            {
                NetFlexDownloadAgent downloadAgent = new NetFlexDownloadAgent();
                downloadAgent.SetNetworkConfig(NetworkConfig);
                downloadAgent.AddTriggerListener(NetFlex.DATA_INFO_RECEIVED, OnDataInfoReceived);
                downloadAgent.AddTriggerListener(NetFlex.DATA_PART_RECEIVED, OnDataPartReceived);

                DownloadAgents.Add(downloadAgent);
                //DontDestroyOnLoad(go_agent);
            }
        }

        private void DisconnectDownloadAgents()
        {
            for (int i = 0; i < DownloadAgents.Count; i++)
            {
                DownloadAgents[i].Disconnect();
            }
        }

        private void AbortDownloadAgents()
        {
            for (int i = 0; i < DownloadAgents.Count; i++)
            {
                DownloadAgents[i].Abort();
            }

            if (DownloadQueue.Count == 0)
            {
                DisconnectDownloadAgents();
            }
        }

        private NetFlexDownloadAgent GetIdleAgent()
        {
            for (int i = 0; i < DownloadAgents.Count; i++)
            {
                if(DownloadAgents[i].IsIdle())
                {
                    return DownloadAgents[i];
                }
            }
            return null;
        }

        private void OnDataInfoReceived(XEvent evt)
        {
            if (QueueCurrrent >= 0)
            {
                DownloadQueue[QueueCurrrent].StartDownloading();
                if (DownloadQueue[QueueCurrrent].IsDataDownloadComplete())
                {
                    DispatchTrigger(new XEvent(NetFlex.DATA_LIST_RECEIVED, this, DownloadQueue[QueueCurrrent]));
                    DownloadQueue.RemoveAt(QueueCurrrent);
                    RequestInfoRequired = false;
                    QueueCurrrent = -1;
                }
            }
        }

        private void OnDataPartReceived(XEvent evt)
        {
            if (QueueCurrrent >= 0 && QueueCurrrent < DownloadQueue.Count)
            {
                NetFlexDownloadChunk dataChunk = (NetFlexDownloadChunk)evt.GetMeta();
                if (dataChunk.DownloadData.IsDownloaded())
                {
                    DownloadQueue[QueueCurrrent].AcceptData(dataChunk.DownloadData);
                    dataChunk.DownloadData.DispatchTrigger(new XEvent(NetFlex.DATA_RECEIVED, dataChunk.DownloadData));
                    DownloadQueue[QueueCurrrent].DispatchTrigger(new XEvent(NetFlex.DATA_RECEIVED, this, dataChunk.DownloadData));
                    DispatchTrigger(new XEvent(NetFlex.DATA_RECEIVED, this, dataChunk.DownloadData));
                }

                DownloadQueue[QueueCurrrent].Refresh();
                if (DownloadQueue[QueueCurrrent].IsDataDownloadComplete())
                {
                    DownloadQueue[QueueCurrrent].DispatchTrigger(new XEvent(NetFlex.DATA_LIST_RECEIVED, DownloadQueue[QueueCurrrent]));
                    DispatchTrigger(new XEvent(NetFlex.DATA_LIST_RECEIVED, this, DownloadQueue[QueueCurrrent]));
                    DownloadQueue.RemoveAt(QueueCurrrent);
                    RequestInfoRequired = false;
                    QueueCurrrent = -1;

                    if(DownloadQueue.Count == 0)
                    {
                        AbortDownloadAgents();
                    }
                }
            }
        }
    }
}