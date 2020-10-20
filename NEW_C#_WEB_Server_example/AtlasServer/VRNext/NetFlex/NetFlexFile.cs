using System.IO;
using System.Collections.Generic;

namespace VRNext.Network
{
    internal class NetFileSource
    {
        public const string HDD = "hdd";
        public const string TNET = "tn";
        public const string WEB_SOCKET = "ws";
        public const string WEB_SOCKET_SSL = "wss";
    }

    internal class NetFileCommand
    {
        public const byte NETFILE_INIT_RECIEVE = 1;
        public const byte NETFILE_PART_REQUEST = 2;
        public const byte NETFILE_PART_RECIEVE = 3;
        public const byte NETFILE_FILE_COMPLETE = 4;
        public const byte NETFILE_FILE_CANCELED = 5;
        public const byte NETFILE_INIT_RECIEVE_SOLID = 11;
        public const byte NETFILE_FILE_COMPLETE_SOLID = 12;
        public const byte NETFILE_FILE_CANCELED_SOLID = 15;
        public const byte NETFILE_ECHO_REQUEST = 42;
        public const byte NETFILE_ECHO_RESPONSE = 142;
    }

    internal class NetFileHandle
    {
        public const byte MSG_COMMAND_ID = 1;
        public const byte MSG_SECURITY_KEY = 2;
        public const byte FILE_VERSION = 3;
        public const byte FILE_DATA = 4;
        public const byte FILE_UNIQ = 5;
        public const byte FILE_SENDER_ID = 6;
        public const byte FILE_TARGET_ID = 7;
        public const byte FILE_SOURCE_ID = 8;
        public const byte FILE_PARTS_COUNT = 9;
        public const byte FILE_SIZE = 10;
        public const byte FILE_HASH = 11;
        public const byte FILE_COMPRESS = 12;
        public const byte FILE_COMMAND = 13;
        public const byte FILE_MESSAGE = 14;
        public const byte FILE_PART_ID = 15;
        public const byte FILE_PART_DATA = 16;
        //public const byte MESSAGE_ID = 9;
        //public const byte MESSAGE_RESPOND_ID = 10;
    };

    [System.Serializable]
    public class NetFlexFile
    {
        public string sourceId;
        public string targetId;
        public string senderId;
        public string securityKey;
        public bool important = false;
        public string commandId;
        public string message;
        public bool compress;

        public string uniq;
        public string data_hash;
        public int fPartsTotal = 0;
        public int fDataLastID = 0;
        public int fSize;
        public bool fileTransferInitiated = false;
        public bool fileTransferAccepted = false;
        public bool fileTransferCanceled = false;
        public long ts_network = 0L;
        public float ts_transfer = 0f;
        public float progress = 0f;
        public int transfer_attempts = 0;

        public bool wait_part_request = false;
        public long ts_part_request = 0;

        public byte[] data_solid;
        public List<byte[]> data_parts;

        public NetFlexFile()
        {
        }

        public NetFlexFile(NetFlexFile netFile)
        {
            uniq = netFile.uniq;
            sourceId = netFile.sourceId;
            targetId = netFile.targetId;
            senderId = netFile.senderId;
            securityKey = netFile.securityKey;
            commandId = netFile.commandId;
            message = netFile.message;
            data_hash = netFile.data_hash;
            ts_network = netFile.ts_network;
            ts_transfer = netFile.ts_transfer;
            fSize = netFile.fSize;
            fPartsTotal = netFile.fPartsTotal;
            fDataLastID = netFile.fDataLastID;
            important = netFile.important;
            compress = netFile.compress;
            data_solid = netFile.data_solid;
            fileTransferInitiated = netFile.fileTransferInitiated;
            fileTransferAccepted = netFile.fileTransferAccepted;
            fileTransferCanceled = netFile.fileTransferCanceled;
            transfer_attempts = netFile.transfer_attempts;
            wait_part_request = netFile.wait_part_request;
            ts_part_request = netFile.ts_part_request;
            progress = netFile.progress;
        }

        public NetFlexFile(byte[] bt_data)
        {
            NetFlexData blocks = new NetFlexData(bt_data);
            uniq = blocks.FindBlockString(1);
            sourceId = blocks.FindBlockString(2);
            targetId = blocks.FindBlockString(3);
            senderId = blocks.FindBlockString(4);
            securityKey = blocks.FindBlockString(5);
            commandId = blocks.FindBlockString(6);
            message = blocks.FindBlockString(7);
            data_hash = blocks.FindBlockString(8);
            ts_network = blocks.FindBlockLong(9);
            ts_transfer = blocks.FindBlockFloat(10);
            fSize = blocks.FindBlockInt(11);
            fPartsTotal = blocks.FindBlockInt(12);
            fDataLastID = blocks.FindBlockInt(13);
            important = blocks.FindBlockBool(14);
            compress = blocks.FindBlockBool(15);

            if(blocks.BlockExists(16))
            {
                data_solid = blocks.FindBlockData(16);
            }
        }

        public NetFlexFile(string uniq, string commandId, string message, string senderId, string targetId)
        {
            this.uniq = uniq;
            this.senderId = senderId;
            this.targetId = targetId;
            this.commandId = commandId;
            this.message = message;
        }

        /// <summary>
        /// Подготавливает файл к загрузке
        /// </summary>
        /// <param name="fileSize"></param>
        /// <param name="partsQuantity"></param>
        /// <param name="dataHash"></param>
        /// <param name="compressFlag"></param>
        /// <param name="securityKey"></param>
        public void PrepareForReceive(int fileSize, int partsQuantity, int compressFlag, string dataHash, string securityKey)
        {
            data_parts = new List<byte[]>();
            fDataLastID = -1;
            fSize = fileSize;
            fPartsTotal = partsQuantity;
            data_hash = dataHash;
            compress = (compressFlag == 1);
            this.securityKey = securityKey;
        }

        public byte[] ToBytes()
        {
            List<byte> bt_data = new List<byte>();
            bt_data.AddRange(NetFlexData.DataToBlock(1, uniq));
            bt_data.AddRange(NetFlexData.DataToBlock(2, sourceId));
            bt_data.AddRange(NetFlexData.DataToBlock(3, targetId));
            bt_data.AddRange(NetFlexData.DataToBlock(4, senderId));
            bt_data.AddRange(NetFlexData.DataToBlock(5, securityKey));
            bt_data.AddRange(NetFlexData.DataToBlock(6, commandId));
            bt_data.AddRange(NetFlexData.DataToBlock(7, message));
            bt_data.AddRange(NetFlexData.DataToBlock(8, data_hash));
            bt_data.AddRange(NetFlexData.DataToBlock(9, ts_network));
            bt_data.AddRange(NetFlexData.DataToBlock(10, ts_transfer));
            bt_data.AddRange(NetFlexData.DataToBlock(11, fSize));
            bt_data.AddRange(NetFlexData.DataToBlock(12, fPartsTotal));
            bt_data.AddRange(NetFlexData.DataToBlock(13, fDataLastID));
            bt_data.AddRange(NetFlexData.DataToBlock(14, important));
            bt_data.AddRange(NetFlexData.DataToBlock(15, compress));

            if (data_solid != null)
            {
                bt_data.AddRange(NetFlexData.DataToBlock(16, data_solid));
            }
            return bt_data.ToArray();
        }

        /// <summary>
        /// Дробит сообщение на пакеты
        /// </summary>
        /// <param name="data"></param>
        public static List<byte[]> DataToParts(byte[] data)
        {
            int partsQuantity = (int)System.Math.Ceiling(data.Length / (double)NetFlex.PACKET_SIZE_BYTES);
            List<byte[]> bytes_parts = new List<byte[]>();
            for (int i = 0; i < partsQuantity; i++)
            {
                int part_start = i * NetFlex.PACKET_SIZE_BYTES;
                int part_end = (i + 1) * NetFlex.PACKET_SIZE_BYTES;
                int part_size = System.Math.Min(part_end, data.Length) - part_start;

                byte[] part = new byte[part_size];
                System.Array.Copy(data, part_start, part, 0, part_size);
                bytes_parts.Add(part);
            }
            return bytes_parts;
        }

        /// <summary>
        /// Собирает сообщение из пакетов 
        /// </summary>
        /// <returns></returns>
        public static byte[] PartsToData(List<byte[]> dataParts)
        {
            MemoryStream ms;
            BinaryWriter br;
            byte[] bt_data = null;

            try
            {
                ms = new MemoryStream();
                br = new BinaryWriter(ms);

                for (int i = 0; i < dataParts.Count; i++)
                    br.Write(dataParts[i]);

                bt_data = ms.ToArray();
                br.Close();
                ms.Close();
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
            }
            return bt_data;
        }
    }
}
