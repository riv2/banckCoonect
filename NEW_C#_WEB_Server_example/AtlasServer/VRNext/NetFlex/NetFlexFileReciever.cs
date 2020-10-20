using System.Collections.Generic;
using System.Text;
using System.Linq;

namespace VRNext.Network
{
    public class NetFlexFileReciever
    {
        private class NetFileDone
        {
            public string uniq;
            public long timestamp;
        }

        private List<NetFlexFile> netFileQue = new List<NetFlexFile>();
        private List<NetFileDone> netFileDone = new List<NetFileDone>();
        private long websocketNetFileCounter = 0;
        private long tnetNetFileCounter = 0;
        private long que_check_ts = 0;

        public void Update()
        {
            CheckFileQue();

            foreach (NetFlexFile netFileX in netFileQue)
            {
                if (netFileX.wait_part_request)
                {
                    if (NetFlex.GetTimePassed(netFileX.ts_part_request) > 15f)
                    {
                        RequestNextPart(netFileX);
                    }
                }
            }
        }

        private void CheckFileQue()
        {
            netFileQue.RemoveAll(x => (x == null));
            if (NetFlex.GetTimePassed(que_check_ts) > 30f)
            {
                que_check_ts = NetFlex.GetAbsoluteTime();
                netFileQue.RemoveAll(x => (x.important == false || x.fileTransferCanceled) && NetFlex.GetTimePassed(x.ts_network) > NetFlex.AWAIT_UNIMPORTANT_TIME);
                netFileQue.RemoveAll(x => x.important && NetFlex.GetTimePassed(x.ts_network) > NetFlex.AWAIT_IMPORTANT_TIME);
                netFileDone.RemoveAll(x => NetFlex.GetTimePassed(x.timestamp) > NetFlex.DUPLICATE_PROTECTION_TIME);

#if ATLAS_NETWORK_SERVER
                try
                {
                    string file_path = Application.dataPath + @"\Logs\stats.txt";
                    string stats_data = "NetFiles received throught TNet: " + tnetNetFileCounter + " | " + "NetFiles received throught WebSocket: " + websocketNetFileCounter;
                    System.IO.File.WriteAllBytes(file_path, Encoding.UTF8.GetBytes(stats_data));
                }
                catch(System.Exception ex)
                {
                    XLogger.LogException(ex);
                }
#endif
            }
        }

        /// <summary>
        /// Вовзвращает прогресс загрузки файла с указанной командой
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <returns></returns>
        internal float GetNetFileDownloadProgress(string command, string message = null)
        {
            List<NetFlexFile> netFilesList = new List<NetFlexFile>();
            if (string.IsNullOrEmpty(command))
            {
                netFilesList.AddRange(netFileQue);
            }
            else if (string.IsNullOrEmpty(message))
            {
                netFilesList.AddRange(netFileQue.FindAll(x => x.commandId == command));
            }
            else
            {
                netFilesList.AddRange(netFileQue.FindAll(x => x.commandId == command && x.message == message));
            }

            float progress = -1f;
            //int bytesTotal = 0;
            //int bytesLoaded = 0;
            for (int i = 0; i < netFilesList.Count; i++)
            {
                //bytesTotal += netFilesList[i].fSize;
                //bytesLoaded += netFilesList[i].fDataLastID * NetFlex.PACKET_SIZE_BYTES;
                progress = System.Math.Max(progress, netFilesList[i].fDataLastID / (float)netFilesList[i].fPartsTotal);
            }
            return progress;
        }

        /// <summary>
        /// Обработчик получения данных
        /// </summary>
        /// <param name="data"></param>
        internal void OnNetDataRecieved(byte[] data)
        {
            NetFlexData blocks;
            try
            {
                blocks = new NetFlexData(data);
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
                return;
            }

            try
            {
                byte COMMAND_ID = blocks.FindBlockByte(NetFileHandle.MSG_COMMAND_ID);
                switch (COMMAND_ID)
                {
                    case NetFileCommand.NETFILE_INIT_RECIEVE:
                        OnInitNetFileRecive(blocks);
                        break;
                    case NetFileCommand.NETFILE_PART_REQUEST:
                        NetFlex.Instance().Sender.OnRequestFileDataPart(blocks);
                        break;
                    case NetFileCommand.NETFILE_PART_RECIEVE:
                        OnFileDataPartRecieved(blocks);
                        break;
                    case NetFileCommand.NETFILE_FILE_COMPLETE:
                        NetFlex.Instance().Sender.OnNetFileTransferCompleted(blocks);
                        break;
                    case NetFileCommand.NETFILE_FILE_CANCELED:
                        OnNetFileTransferCanceled(blocks);
                        break;
#if !UNITY_WEBGL
                    case NetFileCommand.NETFILE_ECHO_REQUEST:
                        OnFileEchoRequestReceived(blocks);
                        break;
                    case NetFileCommand.NETFILE_ECHO_RESPONSE:
                        OnFileEchoResponseReceived(blocks);
                        break;
#endif
                }
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
            }
        }

#if !UNITY_WEBGL
        /// <summary>
        /// Обрабатывает поступивший эхо-запрос 
        /// </summary>
        /// <param name="blocks"></param>
        private void OnFileEchoRequestReceived(NetFlexData blocks)
        {
            if (NetFlex.Instance().GetNetworkClientId() == NetFlex.Instance().ServerId)
            {
                string source_id = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
                string requester_id = blocks.FindBlockString(NetFileHandle.FILE_SENDER_ID);
                if (source_id == NetFileSource.TNET && requester_id != NetFlex.Instance().ServerId)
                {
#if TNET_ENABLED
                    NetworkManagerTN.instance.OnEchoResponse();
#endif
                    NetFlex.Instance().Sender.SendEchoResponse(requester_id);
                }
            }
        }

        /// <summary>
        /// Обрабатывает поступивший эхо-ответ
        /// </summary>
        /// <param name="blocks"></param>
        private void OnFileEchoResponseReceived(NetFlexData blocks)
        {
            if (NetFlex.Instance().GetNetworkClientId() != NetFlex.Instance().ServerId)
            {
                string source_id = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
                string requester_id = blocks.FindBlockString(NetFileHandle.FILE_SENDER_ID);
                if (source_id == NetFileSource.TNET && requester_id == NetFlex.Instance().ServerId)
                {
#if TNET_ENABLED
                    NetworkManagerTN.instance.OnEchoResponse();
#endif
                }
            }
        }
#endif

        /// <summary>
        /// Инициализирует передачу файла, если она еще не начата.
        /// </summary>
        /// <param name="blocks"></param>
        private void OnInitNetFileRecive(NetFlexData blocks)
        {
            if (netFileQue != null)
            {
                string source = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
                string uniq = blocks.FindBlockString(NetFileHandle.FILE_UNIQ);
                int queueCount = netFileQue.FindAll(file => !file.fileTransferCanceled).Count;

                if (queueCount < NetFlex.RECEIVING_QUEUE_LIMIT)
                {
                    if (netFileDone.Find(x => x.uniq == uniq) == null)
                    {
                        NetFlexFile netFile = netFileQue.Find(x => x != null && x.sourceId == source && x.uniq == uniq);
                        if (netFile != null)
                        {
                            if (NetFlex.Instance().debug_mode)
                            {
                                XLogger.LogWarning("[NetFlexFileReciever] Incoming NetFlexFile <" + uniq + "> already exists in que and await next part");
                            }
                            return;
                        }
                        else
                        {
                            netFile = new NetFlexFile(
                                uniq,
                                blocks.FindBlockString(NetFileHandle.FILE_COMMAND),
                                blocks.FindBlockString(NetFileHandle.FILE_MESSAGE),
                                blocks.FindBlockString(NetFileHandle.FILE_SENDER_ID),
                                blocks.FindBlockString(NetFileHandle.FILE_TARGET_ID)
                            );

                            netFile.PrepareForReceive(
                                blocks.FindBlockInt(NetFileHandle.FILE_SIZE),
                                blocks.FindBlockInt(NetFileHandle.FILE_PARTS_COUNT),
                                blocks.FindBlockInt(NetFileHandle.FILE_COMPRESS),
                                blocks.FindBlockString(NetFileHandle.FILE_HASH),
                                blocks.FindBlockString(NetFileHandle.MSG_SECURITY_KEY)
                            );

                            netFile.ts_network = NetFlex.GetAbsoluteTime();
                            netFile.sourceId = source;
                            netFileQue.Add(netFile);

                            if (source == NetFileSource.TNET)
                                tnetNetFileCounter += 1;
                            else if (source == NetFileSource.WEB_SOCKET)
                                websocketNetFileCounter += 1;

                            RequestNextPart(netFile);
                            return;
                        }
                    }
                    else
                    {
                        if (NetFlex.Instance().debug_mode)
                        {
                            XLogger.LogWarning("[NetFlexFileReciever] Incoming NetFlexFile <" + uniq + "> already processed from another source");
                        }
                    }
                }
                else
                {
                    XLogger.LogError("[NetFlexFileReciever] Incoming NetFlexFile queue is overflow: " + queueCount);
                }
            }
            else
            {
                return;
            }
        }

        /// <summary>
        /// Обработчик приема блока данных
        /// </summary>
        /// <param name="blocks"></param>
        private void OnFileDataPartRecieved(NetFlexData blocks)
        {
            if (netFileQue != null)
            {
                string source = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
                string uniq = blocks.FindBlockString(NetFileHandle.FILE_UNIQ);

                NetFlexFile netFile = netFileQue.Find(x => x != null && x.sourceId == source && x.uniq == uniq);
                if (netFile == null)
                {
                    // Клиент передал незапланированный файл, возможно сервер был перезапущен или клиент не получил уведомление.
                    // Говорим клиенту что этот файл больше не нужно пытаться отправлять.
                    if (NetFlex.Instance().debug_mode)
                    {
                        XLogger.Log("[NetFlexFileReciever] Incoming NetFlexFile <" + uniq + "> not found and doesn't expected");
                    }
                    PreventNetFileLoading(uniq, source);
                }
                else if (netFile.fileTransferCanceled)
                {
                    // Передача файла была запланировано отменена, клиент уже уведомлен об этом.
                    // Но часть файла могла быть отправлена до получения уведомления клиентом.
                    netFileQue.Remove(netFile);
                }
                else
                {
                    netFile.wait_part_request = false;
                    int fPart = blocks.FindBlockInt(NetFileHandle.FILE_PART_ID);
                    byte[] fData = blocks.FindBlockData(NetFileHandle.FILE_PART_DATA);

                    if (netFile.fDataLastID == fPart - 1)
                    {
                        // Received next part of data
                        netFile.data_parts.Add(fData);
                        netFile.fDataLastID = fPart;
                        netFile.ts_network = NetFlex.GetAbsoluteTime();

                        if (netFile.fDataLastID == netFile.fPartsTotal - 1)
                        {
                            // Received last part of data
                            OnFileDataComplete(netFile);
                        }
                        else
                        {
                            // Received not last part of data
                            RequestNextPart(netFile);
                        }
                    }
                    else
                    {
                        if (NetFlex.Instance().debug_mode)
                        {
                            XLogger.LogError("[NetFlexFileReciever] Incoming part of NetFlexFile <" + netFile.uniq + "> has incorrect number: " + (fPart - 1) + ", but expected: " + netFile.fDataLastID);
                        }
                    }
                }
            }
        }

        /// <summary>
        /// Обработчик отмены передачи файла
        /// </summary>
        /// <param name="netFile"></param>
        private void OnNetFileTransferCanceled(NetFlexData blocks)
        {
            string source = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
            string uniq = blocks.FindBlockString(NetFileHandle.FILE_UNIQ);

            NetFlexFile netFile = netFileQue.Find(x => x != null && x.sourceId == source && x.uniq == uniq);
            if (netFile != null)
            {
                //XLogger.Log("[NetFlex] NetFlexFile transfer canceled: " + netFile.commandId + " from: " + netFile.senderId);
                netFileQue.Remove(netFile);
            }
        }
        
        /// <summary>
        /// Обработчик приема одного их пакетов принимаемого файла
        /// </summary>
        /// <param name="netFile"></param>
        private void RequestNextPart(NetFlexFile netFile)
        {
            netFile.wait_part_request = true;
            netFile.ts_part_request = NetFlex.GetAbsoluteTime();
            List<byte> bt = new List<byte>();
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_PART_REQUEST));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_TARGET_ID, Encoding.UTF8.GetBytes(netFile.targetId)));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(netFile.sourceId)));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(netFile.uniq)));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_PART_ID, System.BitConverter.GetBytes(netFile.fDataLastID + 1)));

            NetFlex.Instance().SendData(netFile.senderId, bt.ToArray(), netFile.sourceId);
            if (NetFlex.Instance().debug_mode)
            {
                XLogger.Log("[NetFlexFileReciever] Requesting next part of incoming NetFlexFile <" + netFile.uniq + ">");
            }
        }

        /// <summary>
        /// Обработчик заверешения приема файла 
        /// </summary>
        /// <param name="netFile"></param>
        private void OnFileDataComplete(NetFlexFile netFile)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_FILE_COMPLETE));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_TARGET_ID, Encoding.UTF8.GetBytes(netFile.targetId)));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(netFile.sourceId)));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(netFile.uniq)));
            NetFlex.Instance().SendData(netFile.senderId, bt.ToArray(), netFile.sourceId);

            netFile.ts_network = NetFlex.GetAbsoluteTime();
            netFile.data_solid = NetFlexFile.PartsToData(netFile.data_parts);
            netFile.data_parts = null;
            netFileQue.RemoveAll(x => x.uniq == netFile.uniq);
            //XLogger.Log("[NetFlexFileReciever] NetFlexFile succeessfully received " + netFile.commandId);

#if !UNITY_WEBGL
            // Запоминаем что файл уже обработан сервером
            netFileDone.Add(new NetFileDone() { uniq = netFile.uniq, timestamp = NetFlex.GetAbsoluteTime() });

            // Отменяем загрузку файла по другим каналам связи
            PreventNetFileLoading(netFile.uniq);
#endif

            if (netFile.compress)
            {
                NetFlex.Instance().Compressor.Extract(netFile);
            }
            else
            {
                XLogger.Log("[NetFlex] Income NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " from <" + netFile.senderId + "> uncompressed size: " + netFile.data_solid.Length + " bytes");
                OnNetFileReady(netFile);
            }
        }

        internal void OnNetFileReady(NetFlexFile netFile)
        {
            NetFlex.Instance().HandleNetFlexFile(netFile);
        }

        /// <summary>
        /// Отменяет загрузку файлов с казанным unit по всем каналам связи,
        /// </summary>
        /// <param name="uniq"></param>
        /// <param name="source"></param>
        private void PreventNetFileLoading(string uniq, string source = null)
        {
            // Находим все файлы с указанным uniq
            List<NetFlexFile> netFilesList = new List<NetFlexFile>();
            if (string.IsNullOrEmpty(source))
            {
                netFilesList.AddRange(netFileQue.FindAll(x => x != null && x.uniq == uniq));
            }
            else
            {
                netFilesList.AddRange(netFileQue.FindAll(x => x != null && x.uniq == uniq && x.sourceId == source));
            }

            for (int i = 0; i < netFilesList.Count; i++)
            {
                NetFlexFile netFile = netFilesList[i];

                // Отправляем команду отмены передачи для каждого файла
                List<byte> bt = new List<byte>();
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_FILE_CANCELED));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(netFile.sourceId)));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(netFile.uniq)));

                NetFlex.Instance().SendData(netFile.senderId, bt.ToArray(), netFile.sourceId);

                // Помечаем файл как отмененный для передачи
                netFile.fileTransferCanceled = true;
            }
        }

        /// <summary>
        /// Отменяет отправку команды на сервер
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        internal void CancelNetFile(string command, string message)
        {
            List<NetFlexFile> netFileList = new List<NetFlexFile>();
            if (string.IsNullOrEmpty(message))
            {
                netFileList.AddRange(netFileQue.FindAll(file => file.commandId == command));
            }
            else
            {
                netFileList.AddRange(netFileQue.FindAll(file => file.commandId == command && file.message == message));
            }

            foreach (NetFlexFile netFile in netFileList)
            {
                List<byte> bt = new List<byte>();
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_FILE_COMPLETE));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_TARGET_ID, Encoding.UTF8.GetBytes(netFile.targetId)));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(netFile.sourceId)));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(netFile.uniq)));
                NetFlex.Instance().SendData(netFile.senderId, bt.ToArray(), netFile.sourceId);
            }

            netFileQue.RemoveAll(x => netFileList.Contains(x));
        }
    }
}
