using System.Collections.Generic;

namespace VRNext.Network
{
    internal class NetFlexBlockType
    {
        internal const byte EMPTY = 0;
        internal const byte BYTE = 1;
        internal const byte STRING = 2;
    }

    internal class NetFlexDataEntry
    {
        internal byte id;
        internal string id_str;
        internal byte[] bt_data;
    }

    internal class NetFlexData
    {
        private const byte MAGIC_NUMBER = 73;
        private List<NetFlexDataEntry> data_entrys;

        internal NetFlexData(byte[] bt)
        {
            data_entrys = new List<NetFlexDataEntry>();

            List<byte> bt_list = new List<byte>(bt);
            int bt_offset = 0;

            while (bt_offset < bt.Length - 1)
            {
                byte magicNumber = (byte)System.BitConverter.ToChar(bt, bt_offset); bt_offset += 1;
                if (MAGIC_NUMBER == magicNumber)
                {
                    byte blockType = (byte)System.BitConverter.ToChar(bt, bt_offset); bt_offset += 1;
                    int block_len;
                    byte[] bt_block = null;
                    byte header;
                    string header_str = null;
                    NetFlexDataEntry de = null;

                    switch (blockType)
                    {
                        case NetFlexBlockType.EMPTY:
                            block_len = System.BitConverter.ToInt32(bt, bt_offset); bt_offset += 4;
                            bt_block = bt_list.GetRange(bt_offset, block_len).ToArray();
                            bt_offset += block_len;

                            de = new NetFlexDataEntry();
                            de.id = 0;
                            de.bt_data = bt_block;
                            data_entrys.Add(de);
                            break;
                        case NetFlexBlockType.BYTE:
                            header = bt[bt_offset]; bt_offset += 1;
                            block_len = System.BitConverter.ToInt32(bt, bt_offset); bt_offset += 4;
                            bt_block = bt_list.GetRange(bt_offset, block_len).ToArray();
                            bt_offset += block_len;

                            de = new NetFlexDataEntry();
                            de.id = header;
                            de.bt_data = bt_block;
                            data_entrys.Add(de);
                            break;
                        case NetFlexBlockType.STRING:
                            block_len = System.BitConverter.ToInt32(bt, bt_offset); bt_offset += 4;
                            header_str = System.Text.Encoding.UTF8.GetString(bt, bt_offset, block_len); bt_offset += block_len;
                            block_len = System.BitConverter.ToInt32(bt, bt_offset); bt_offset += 4;
                            bt_block = bt_list.GetRange(bt_offset, block_len).ToArray(); bt_offset += block_len;

                            de = new NetFlexDataEntry();
                            de.id = 0;
                            de.id_str = header_str;
                            de.bt_data = bt_block;
                            data_entrys.Add(de);
                            break;

                    }
                }
            }
        }

        internal bool BlockExists(byte id)
        {
            return data_entrys.Find(x => x.id == id) != null;
        }

        internal bool BlockExists(string id)
        {
            return data_entrys.Find(x => x.id_str != null && x.id_str.Equals(id)) != null;
        }

        internal byte[] FindBlockData(byte id)
        {
            try
            {
                return data_entrys.Find(x => x.id == id).bt_data;
            }
            catch { }
            return null;
        }

        internal byte[] FindBlockData(string id)
        {
            try
            {
                return data_entrys.Find(x => x.id_str != null && x.id_str.Equals(id)).bt_data;
            }
            catch { }
            return null;
        }

        internal List<byte[]> FindAllBlockData(byte id)
        {
            List<NetFlexDataEntry> dt = data_entrys.FindAll(x => x.id_str != null && x.id.Equals(id));
            List<byte[]> bt = new List<byte[]>();
            for (int i = 0; i < dt.Count; i++)
            {
                bt.Add(dt[i].bt_data);
            }

            return bt;
        }

        internal List<byte[]> FindAllBlockData(string id)
        {
            List<NetFlexDataEntry> dt = data_entrys.FindAll(x => x.id_str != null && x.id_str.Equals(id));
            List<byte[]> bt = new List<byte[]>();
            for (int i = 0; i < dt.Count; i++)
            {
                bt.Add(dt[i].bt_data);
            }

            return bt;
        }

        internal string FindBlockString(byte id)
        {
            try
            {
                return System.Text.Encoding.UTF8.GetString(FindBlockData(id));
            }
            catch { }
            return null;
        }

        internal string FindBlockString(string id)
        {
            try
            {
                return System.Text.Encoding.UTF8.GetString(FindBlockData(id));
            }
            catch { }
            return null;
        }

        internal int FindBlockInt(byte id)
        {
            try
            {
                return System.BitConverter.ToInt32(FindBlockData(id), 0);
            }
            catch { }
            return 0;
        }

        internal bool FindBlockBool(byte id)
        {
            try
            {
                return System.BitConverter.ToBoolean(FindBlockData(id), 0);
            }
            catch { }
            return false;
        }

        internal int FindBlockInt(string id)
        {
            try
            {
                return System.BitConverter.ToInt32(FindBlockData(id), 0);
            }
            catch { }
            return 0;
        }

        internal float FindBlockFloat(byte id)
        {
            try
            {
                return System.BitConverter.ToSingle(FindBlockData(id), 0);
            }
            catch { }
            return 0f;
        }

        internal float FindBlockFloat(string id)
        {
            try
            {
                return System.BitConverter.ToSingle(FindBlockData(id), 0);
            }
            catch { }
            return 0f;
        }

        internal byte FindBlockByte(byte id)
        {
            try
            {
                return FindBlockData(id)[0];
            }
            catch { }
            return 0;
        }

        internal byte FindBlockByte(string id)
        {
            try
            {
                return FindBlockData(id)[0];
            }
            catch { }
            return 0;
        }

        internal long FindBlockLong(byte id)
        {
            try
            {
                return System.BitConverter.ToInt64(FindBlockData(id), 0);
            }
            catch { }
            return 0;
        }

        internal long FindBlockLong(string id)
        {
            try
            {
                return System.BitConverter.ToInt64(FindBlockData(id), 0);
            }
            catch { }
            return 0;
        }

        internal static List<byte> DataToBlock(byte header, string value)
        {
            return DataToBlockInternal(header, System.Text.Encoding.UTF8.GetBytes(value));
        }

        internal static List<byte> DataToBlock(byte header, bool value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        internal static List<byte> DataToBlock(byte header, byte value)
        {
            return DataToBlockInternal(header, value);
        }

        internal static List<byte> DataToBlock(byte header, int value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        internal static List<byte> DataToBlock(byte header, float value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        internal static List<byte> DataToBlock(byte header, byte[] value)
        {
            return DataToBlockInternal(header, value);
        }

        internal static List<byte> DataToBlock(string header, byte[] value)
        {
            return DataToBlockInternal(header, value);
        }

        internal static List<byte> DataToBlock(string header, string value)
        {
            return DataToBlockInternal(header, System.Text.Encoding.UTF8.GetBytes(value));
        }

        internal static List<byte> DataToBlock(string header, float value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        internal static List<byte> DataToBlock(string header, int value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        internal static List<byte> DataToBlock(string header, double value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        internal static List<byte> DataToBlock(string header, bool value)
        {
            return DataToBlockInternal(header, System.BitConverter.GetBytes(value));
        }

        private static List<byte> DataToBlockInternal(byte block_header, byte block_data)
        {
            return DataToBlockInternal(block_header, new byte[1] { block_data });
        }

        private static List<byte> DataToBlockInternal(byte block_header, byte[] block_data)
        {
            List<byte> bt = new List<byte>();
            bt.Add(MAGIC_NUMBER);
            bt.Add(NetFlexBlockType.BYTE);
            bt.Add(block_header);
            bt.AddRange(System.BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        private static List<byte> DataToBlockInternal(byte block_header, string value)
        {
            List<byte> bt = new List<byte>();
            byte[] block_data = System.Text.Encoding.UTF8.GetBytes(value);
            bt.Add(MAGIC_NUMBER);
            bt.Add(NetFlexBlockType.BYTE);
            bt.Add(block_header);
            bt.AddRange(System.BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        private static List<byte> DataToBlockInternal(string block_header, byte[] block_data)
        {
            List<byte> bt = new List<byte>();
            bt.Add(MAGIC_NUMBER);
            bt.Add(NetFlexBlockType.STRING);

            byte[] bt_value = System.Text.Encoding.UTF8.GetBytes(block_header);
            bt.AddRange(System.BitConverter.GetBytes(bt_value.Length));
            bt.AddRange(bt_value);

            bt.AddRange(System.BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        private static List<byte> DataToBlockInternal(byte[] block_data)
        {
            List<byte> bt = new List<byte>();
            bt.Add(MAGIC_NUMBER);
            bt.Add(NetFlexBlockType.EMPTY);
            bt.AddRange(System.BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }
    }
}