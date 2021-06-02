using System.Collections.Generic;
using System.Text;

namespace VRNext.Network
{
    internal class NetFlexFileSender
    {

        private List<NetFlexFile> netFileQue = new List<NetFlexFile>();
        private long que_check_ts = 0;
        private bool is_compress_enabled = true;

        private static string GetNetworkClientId()
        {
            return NetFlex.Instance().GetNetworkClientId();
        }

        public void SetCompressEnabled(bool enabled)
        {
            is_compress_enabled = enabled;
        }

        public void Update()
        {
            CheckFileQue();
            CheckFilesToSend();
        }

        private void CheckFileQue()
        {
            netFileQue.RemoveAll(x => (x == null));
            if (NetFlex.GetTimePassed(que_check_ts) > 30f)
            {
                que_check_ts = NetFlex.GetAbsoluteTime();
                netFileQue.RemoveAll(x => x.important && NetFlex.GetTimePassed(x.ts_network) > NetFlex.AWAIT_IMPORTANT_TIME);
                netFileQue.RemoveAll(x => x.important == false && NetFlex.GetTimePassed(x.ts_network) > NetFlex.AWAIT_UNIMPORTANT_TIME);
                netFileQue.RemoveAll(x => x.fileTransferAccepted == false && ((x.transfer_attempts > 3 && x.important == false) || x.transfer_attempts > 10));
            }
        }

        private void CheckFilesToSend()
        {
            if (NetFlex.Instance().GetConnectionStatus() != NetFlex.NetConnectionStatus.ONLINE)
                return;

            for (int i = 0; i < netFileQue.Count; i++)
            {
                if (netFileQue[i].data_parts == null)
                {
                    netFileQue[i].data_parts = NetFlexFile.DataToParts(netFileQue[i].data_solid);
                    netFileQue[i].fPartsTotal = netFileQue[i].data_parts.Count;
                    netFileQue[i].data_solid = null;
                }

                if (netFileQue[i].data_parts.Count != 0 && NetFlex.GetTime() - netFileQue[i].ts_transfer > 30f)
                {
                    netFileQue[i].senderId = GetNetworkClientId();
                    netFileQue[i].transfer_attempts++;
                    netFileQue[i].ts_transfer = NetFlex.GetTime();

                    if (netFileQue[i].transfer_attempts > 1)
                    {
                        // Если это первая попытка то все ок, а вот если вторая и больше то надо бы это зафиксировать
                        XLogger.LogWarning("[NetFlexFileSender] Sending attempt of NetFlexFile <" + netFileQue[i].uniq + "> with command " + netFileQue[i].commandId + " " + netFileQue[i].message + " to <" + netFileQue[i].targetId + ">: " + netFileQue[i].transfer_attempts);
                    }

                    List<byte> bt = new List<byte>();
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_INIT_RECIEVE));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(netFileQue[i].uniq)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(netFileQue[i].sourceId)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SENDER_ID, Encoding.UTF8.GetBytes(netFileQue[i].senderId)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_TARGET_ID, Encoding.UTF8.GetBytes(netFileQue[i].targetId)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_SECURITY_KEY, Encoding.UTF8.GetBytes(netFileQue[i].targetId)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_PARTS_COUNT, System.BitConverter.GetBytes(netFileQue[i].fPartsTotal)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SIZE, System.BitConverter.GetBytes(netFileQue[i].fSize)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_HASH, Encoding.UTF8.GetBytes(netFileQue[i].data_hash)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_COMPRESS, System.BitConverter.GetBytes(netFileQue[i].compress ? 1 : 0)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_COMMAND, Encoding.UTF8.GetBytes(netFileQue[i].commandId)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_MESSAGE, Encoding.UTF8.GetBytes(netFileQue[i].message)));

                    if (!NetFlex.Instance().SendData(netFileQue[i].targetId, bt.ToArray(), netFileQue[i].sourceId))
                    {
                        netFileQue[i].ts_transfer = System.Single.MinValue;
                    }
                }
            }
        }

        /// <summary>
        /// Вовзвращает прогресс отпрвки файла с указанной командой
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <returns></returns>
        internal float GetNetFileUploadProgress(string command, string message = null)
        {
            List<NetFlexFile> netFilesList = new List<NetFlexFile>();
            if (string.IsNullOrEmpty(message))
            {
                netFilesList.AddRange(netFileQue.FindAll(x => x.commandId == command));
            }
            else
            {
                netFilesList.AddRange(netFileQue.FindAll(x => x.commandId == command && x.message == message));
            }

            float progress = -1f;
            for (int i = 0; i < netFilesList.Count; i++)
            {
                progress = System.Math.Max(progress, netFilesList[i].fDataLastID / (float)netFilesList[i].fPartsTotal);
            }
            return progress;
        }

        /// <summary>
        /// Проверяет наличие команды для отправки на сервер
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <returns></returns>
        internal bool IsCommandExists(string command, string message)
        {
            NetFlexFile netFile = null;
            if (string.IsNullOrEmpty(message))
            {
                netFile = netFileQue.Find(file => file != null && file.commandId == command);
            }
            else
            {
                netFile = netFileQue.Find(file => file != null && file.commandId == command && file.message == message);
            }
            return netFile != null;
        }

        /// <summary>
        /// Обработчик запроса на передачу части файла
        /// </summary>
        /// <param name="blocks"></param>
        internal void OnRequestFileDataPart(NetFlexData blocks)
        {
            string source = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
            string uniq = blocks.FindBlockString(NetFileHandle.FILE_UNIQ);
            int fPart = blocks.FindBlockInt(NetFileHandle.FILE_PART_ID);

            NetFlexFile netFile = netFileQue.Find(x => x != null && x.sourceId == source && x.uniq == uniq);
            if (netFile != null)
            {
                netFile.fileTransferAccepted = true;
                netFile.ts_network = NetFlex.GetAbsoluteTime();
                List<byte> bt = new List<byte>();
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_PART_RECIEVE));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(source)));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(netFile.uniq)));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_PART_ID, System.BitConverter.GetBytes(fPart)));
                bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_PART_DATA, netFile.data_parts[fPart]));
                netFile.progress = (fPart + 1) / (float)netFile.fPartsTotal;

                NetFlex.Instance().SendData(netFile.targetId, bt.ToArray(), source);

                if (fPart == 0)
                {
                    if(NetFlex.Instance().debug_mode)
                    {
                        XLogger.Log("[NetFlexFileSender] Transfer of NetFlexFile <" + netFile.uniq + "> to <"  + netFile.targetId + "> has been started");
                    }
                }
            }
            else
            {
                if(NetFlex.Instance().debug_mode)
                {
                    XLogger.LogError("[NetFlexFileSender] NetFlexFile <" + uniq + "> can't be send cause not found in pool");
                }

                if (NetFlex.Instance().GetNetworkClientId() == NetFlex.Instance().ServerId)
                {
                    if (blocks.BlockExists(NetFileHandle.FILE_TARGET_ID))
                    {
                        string targetId = blocks.FindBlockString(NetFileHandle.FILE_TARGET_ID);
                        List<byte> bt = new List<byte>();
                        bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_FILE_CANCELED));
                        bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(source)));
                        bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(uniq)));
                        NetFlex.Instance().SendData(targetId, bt.ToArray(), source);
                    }
                }
                else
                {
                    List<byte> bt = new List<byte>();
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_FILE_CANCELED));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(source)));
                    bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_UNIQ, Encoding.UTF8.GetBytes(uniq)));
                    NetFlex.Instance().SendData(NetFlex.Instance().ServerId, bt.ToArray(), source);
                }
            }
        }

        /// <summary>
        /// Обработчик заверешния передачи файла
        /// </summary>
        /// <param name="blocks"></param>
        internal void OnNetFileTransferCompleted(NetFlexData blocks)
        {
            string source = blocks.FindBlockString(NetFileHandle.FILE_SOURCE_ID);
            string uniq = blocks.FindBlockString(NetFileHandle.FILE_UNIQ);

            NetFlexFile netFile = netFileQue.Find(x => x != null && x.sourceId == source && x.uniq == uniq);
            if (netFile != null)
            {
                //XLogger.Log("[NetFlexFileSender] NetFlexFile transfer complete: " + netFile.commandId + " to: " + netFile.targetId);
                netFileQue.RemoveAll(file => file.uniq == netFile.uniq);
            }
        }

        /// <summary>
        /// Добавляет NetFile в очередь на отправку
        /// </summary>
        /// <param name="netFile"></param>
        internal void AddToQue(NetFlexFile netFile)
        {
            if (netFileQue.Count > NetFlex.SENDING_QUEUE_LIMIT)
                return;

            netFileQue.Add(netFile);
        }

        /// <summary>
        /// Создает файл с командой для отправки на сервер
        /// </summary>
        /// <param name="target_id"></param>
        /// <param name="command"></param>
        /// <param name="message"></param>
        /// <param name="bt_data"></param>
        /// <param name="compress"></param>
        /// <param name="important"></param>
        internal void SendCommand(string command, string message, byte[] bt_data, bool compress, bool important = true)
        {
            if (bt_data == null)
                bt_data = new byte[1];

            NetFlexFile netFile = new NetFlexFile();
            netFile.uniq = NetFlex.Instance().GetNetworkClientId() + "_" + NetFlex.GetRandomUniq(8);
            netFile.sourceId = NetFileSource.HDD;
            netFile.senderId = GetNetworkClientId();
            netFile.targetId = NetFlex.Instance().ServerId;
            netFile.commandId = command;
            netFile.message = message;
            netFile.securityKey = GetNetworkClientId();

            netFile.data_solid = bt_data;
            netFile.data_hash = XHash.Md5(bt_data);
            netFile.compress = compress;
            netFile.important = important;

            netFile.ts_transfer = System.Single.MinValue;
            netFile.ts_network = NetFlex.GetAbsoluteTime();

            if (is_compress_enabled)
            {
                if (netFile.compress)
                {
                    NetFlex.Instance().Compressor.Compress(netFile);
                }
                else
                {
                    XLogger.Log("[NetFlex] Outcome NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " to <" + netFile.targetId + "> uncompressed size " + netFile.data_solid.Length + " bytes");
                    SendCommand(netFile);
                }
            }
            else
            {
                XLogger.Log("[NetFlex] Outcome NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " to <" + netFile.targetId + "> uncompressed size " + netFile.data_solid.Length + " bytes");
                netFile.compress = false;
                SendCommand(netFile);
            }
        }

        internal void SendCommand(NetFlexFile netFileToSend)
        {
            if (NetFlex.Instance().WebsocketMode == NetFlex.NetConectionMode.ENABLED || NetFlex.Instance().WebsocketMode == NetFlex.NetConectionMode.DUPLEX)
            {
                NetFlexFile netFile = new NetFlexFile(netFileToSend);
                netFile.sourceId = NetFileSource.WEB_SOCKET;
                netFile.ts_transfer = System.Single.MinValue;
                netFile.ts_network = NetFlex.GetAbsoluteTime();

                // Отправляем файл через WebSocket, если он активирован
                AddToQue(netFile);
            }

            if (NetFlex.Instance().WebsocketMode == NetFlex.NetConectionMode.SECURE || NetFlex.Instance().WebsocketMode == NetFlex.NetConectionMode.DUPLEX)
            {
                NetFlexFile netFile = new NetFlexFile(netFileToSend);
                netFile.sourceId = NetFileSource.WEB_SOCKET_SSL;
                netFile.ts_transfer = System.Single.MinValue;
                netFile.ts_network = NetFlex.GetAbsoluteTime();

                // Отправляем файл через WebSocket, если он активирован
                AddToQue(netFile);
            }
        }

        /// <summary>
        /// Отправляет эхо-запрос
        /// </summary>
        /// <param name="target_id"></param>
        internal void SendEchoRequest(string target_id)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_ECHO_REQUEST));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SENDER_ID, Encoding.UTF8.GetBytes(NetFlex.Instance().GetNetworkClientId())));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(NetFileSource.TNET)));
            NetFlex.Instance().SendData(target_id, bt.ToArray(), NetFileSource.TNET);
        }

        /// <summary>
        /// Отправляет эхо-ответ
        /// </summary>
        /// <param name="target_id"></param>
        internal void SendEchoResponse(string target_id)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.MSG_COMMAND_ID, NetFileCommand.NETFILE_ECHO_RESPONSE));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SENDER_ID, Encoding.UTF8.GetBytes(NetFlex.Instance().GetNetworkClientId())));
            bt.AddRange(NetFlexData.DataToBlock(NetFileHandle.FILE_SOURCE_ID, Encoding.UTF8.GetBytes(NetFileSource.TNET)));
            NetFlex.Instance().SendData(target_id, bt.ToArray(), NetFileSource.TNET);
        }

        /// <summary>
        /// Отменяет отправку команды на сервер
        /// </summary>
        /// <param name="command"></param>
        /// <param name="message"></param>
        internal void CancelCommand(string command, string message)
        {
            if (string.IsNullOrEmpty(message))
            {
                netFileQue.RemoveAll(file => file.commandId == command);
            }
            else
            {
                netFileQue.RemoveAll(file => file.commandId == command && file.message == message);
            }
        }
    }
}
