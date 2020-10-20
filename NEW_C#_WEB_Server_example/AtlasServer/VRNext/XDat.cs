using System.Collections.Generic;
using System.Xml.Serialization;
using System.IO;
using System.Text;
using System;

namespace VRNext
{
    public static class XDat
    {
        public enum ImpportOptions { INIT, DIFFUSE, AO, ICON, MESH, XML, ALL }
        public static bool verbose = false;

        public class XBlockEntry : IDisposable
        {
            public class XDataEntry : IDisposable
            {
                private XBlockEntry Owner;
                public string Id { private set; get; }
                public int Offset { private set; get; }
                public int Length { private set; get; }

                public XDataEntry(XBlockEntry owner, string id, int offset, int length)
                {
                    Owner = owner;
                    Id = id;
                    Offset = offset;
                    Length = length;
                }

                public void Dispose()
                {
                    Owner = null;
                    Id = null;
                }

                public byte[] Data
                {
                    get
                    {
                        return Owner.ExtractData(this);
                    }
                }
            }

            public string SourceFile { private set; get; }
            public byte[] SourceData { private set; get; }
            public List<XDataEntry> EntryList { private set; get; }

            public XBlockEntry()
            {
                EntryList = new List<XDataEntry>();
            }

            /// <summary>
            /// Создает карту блоков для массива байт
            /// </summary>
            /// <param name="bt_data"></param>
            /// <param name="offset"></param>
            /// <param name="headerless"></param>
            public XBlockEntry(byte[] bt_data, bool headerless = false, int offset = 0)
            {
                EntryList = new List<XDataEntry>();
                SourceData = bt_data;

                if (bt_data != null)
                {
                    int bt_offset = offset;
                    if (headerless)
                    {
                        while (bt_offset < bt_data.Length - 1)
                        {
                            int block_len = BitConverter.ToInt32(bt_data, bt_offset);
                            bt_offset += 4;
                            EntryList.Add(new XDataEntry(this, null, bt_offset, block_len));
                            bt_offset += block_len;
                        }
                    }
                    else
                    {
                        string header = "";
                        while (bt_offset < bt_data.Length - 1)
                        {
                            bt_offset += XDat.BlockToString(bt_data, bt_offset, out header);
                            int block_len = BitConverter.ToInt32(bt_data, bt_offset);
                            bt_offset += 4;
                            EntryList.Add(new XDataEntry(this, header, bt_offset, block_len));
                            bt_offset += block_len;
                        }
                    }
                }
            }

            /// <summary>
            /// Создает карту блоков для файла на жестком диске
            /// </summary>
            /// <param name="source_file"></param>
            /// <param name="offset"></param>
            /// <param name="headerless"></param>
            public XBlockEntry(string source_file, int offset = 0, bool headerless = false)
            {
                EntryList = new List<XDataEntry>();
                SourceFile = source_file;
                if (XFileManager.IsFileAwailable(SourceFile))
                {
                    FileStream fs = new FileStream(SourceFile, FileMode.Open);
                    BinaryReader br = new BinaryReader(fs);
                    fs.Position = offset;

                    if (headerless)
                    {
                        while (fs.Position < fs.Length - 1)
                        {
                            int block_len = br.ReadInt32();
                            EntryList.Add(new XDataEntry(this, null, (int)fs.Position, block_len));
                            fs.Position += block_len;
                        }
                    }
                    else
                    {
                        string header = "";
                        while (fs.Position < fs.Length - 1)
                        {
                            int block_len = br.ReadInt32();
                            byte[] block_bt = br.ReadBytes(block_len);
                            header = Encoding.ASCII.GetString(block_bt);
                            block_len = br.ReadInt32();
                            EntryList.Add(new XDataEntry(this, header, (int)fs.Position, block_len));
                            fs.Position += block_len;
                        }
                    }

                    br.Close();
                    fs.Close();
                }
            }

            /// <summary>
            /// Извлекает данные для блока
            /// </summary>
            /// <param name="entry"></param>
            /// <returns></returns>
            public byte[] ExtractData(XDataEntry entry)
            {
                byte[] bt_data = null;
                if (SourceData != null)
                {
                    bt_data = new byte[entry.Length];
                    Array.Copy(SourceData, entry.Offset, bt_data, 0, entry.Length);
                }
                else if (SourceFile != null)
                {
                    if (XFileManager.IsFileAwailable(SourceFile))
                    {
                        FileStream fs = new FileStream(SourceFile, FileMode.Open);
                        BinaryReader br = new BinaryReader(fs);
                        fs.Position = entry.Offset;
                        bt_data = br.ReadBytes(entry.Length);
                        br.Close();
                        fs.Close();
                    }
                }
                return bt_data;
            }

            public void Dispose()
            {
                foreach (XDataEntry entry in EntryList)
                    entry.Dispose();

                EntryList.Clear();
                EntryList = null;
                SourceData = null;
                SourceFile = null;
            }

            public XDataEntry FindDataEntry(string id)
            {
                return EntryList.Find(x => x.Id == id);
            }

            public XDataEntry GetDataEntry(int index)
            {
                return (index >= 0 && index < EntryList.Count) ? EntryList[index] : null;
            }

            public bool BlockExists(string id)
            {
                return EntryList.Find(x => x.Id == id) != null;
            }

            public byte[] FindBlockData(string id)
            {
                XDataEntry entry = EntryList.Find(x => x.Id == id);
                return (entry == null) ? null : ExtractData(entry);
            }

            public byte[] GetBlockData(int index)
            {
                if (index >= 0 && index < EntryList.Count)
                {
                    return ExtractData(EntryList[index]);
                }
                else
                {
                    return null;
                }
            }

            public List<byte[]> FindAllBlockData(string id)
            {
                List<XDataEntry> list = EntryList.FindAll(x => x.Id == id);
                List<byte[]> bt_list = new List<byte[]>();
                foreach (XDataEntry entry in list)
                {
                    bt_list.Add(ExtractData(entry));
                }
                return bt_list;
            }

            public string FindBlockString(string id, bool utf = false)
            {
                byte[] block_data = FindBlockData(id);
                if (block_data == null)
                {
                    XLogger.LogError("XDat: block " + id + " not found!");
                    return null;
                }

                try
                {
                    return (utf) ? Encoding.UTF8.GetString(block_data) : Encoding.ASCII.GetString(block_data);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return null;
                }
            }

            public int FindBlockInt(string id)
            {
                byte[] block_data = FindBlockData(id);
                if (block_data == null)
                {
                    XLogger.LogError("XDat: block " + id + " not found!");
                    return 0;
                }

                try
                {
                    return BitConverter.ToInt32(block_data, 0);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return 0;
                }
            }

            public float FindBlockFloat(string id)
            {
                byte[] block_data = FindBlockData(id);
                if (block_data == null)
                {
                    XLogger.LogError("XDat: block " + id + " not found!");
                    return 0;
                }

                try
                {
                    return BitConverter.ToSingle(block_data, 0);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return 0;
                }
            }

            public long FindBlockLong(string id, long default_value = 0)
            {
                byte[] block_data = FindBlockData(id);
                if (block_data == null)
                {
                    XLogger.LogError("XDat: block " + id + " not found!");
                    return default_value;
                }

                try
                {
                    return BitConverter.ToInt64(block_data, 0);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return default_value;
                }
            }

            public bool FindBlockBool(string id)
            {
                byte[] block_data = FindBlockData(id);
                if (block_data == null)
                {
                    XLogger.LogError("XDat: block " + id + " not found!");
                    return false;
                }

                try
                {
                    return BitConverter.ToBoolean(block_data, 0);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return false;
                }
            }

            public double FindBlockDouble(string id)
            {
                byte[] block_data = FindBlockData(id);
                if (block_data == null)
                {
                    XLogger.LogError("XDat: block " + id + " not found!");
                    return 0;
                }

                try
                {
                    return BitConverter.ToDouble(block_data, 0);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return 0;
                }
            }
        }


        static string FixedString(string value, int size) //Returns digit in string format with fixed lenght; Clamps input value if char_num exeeded;
        {
            string digit_raw = "";
            int spaces = size - value.ToString().Length;

            for (int i = 0; i < spaces; i++)
            {
                digit_raw = digit_raw + " ";
            }

            digit_raw = value.ToString() + digit_raw;
            return digit_raw;
        }

        /////****************************HEADERLESS***********************************

        public static List<byte> ToDataBlock(byte[] value)
        {
            return ToDataBlock_Headerless(value);
        }

        public static List<byte> ToDataBlock(string value)
        {
            value = (value == null) ? "" : value;
            return ToDataBlock_Headerless(Encoding.UTF8.GetBytes(value));
        }

        public static List<byte> ToDataBlock(float value)
        {
            return ToDataBlock_Headerless(BitConverter.GetBytes(value));
        }

        public static List<byte> ToDataBlock(int value)
        {
            return ToDataBlock_Headerless(BitConverter.GetBytes(value));
        }

        public static List<byte> ToDataBlock(double value)
        {
            return ToDataBlock_Headerless(BitConverter.GetBytes(value));
        }

        public static List<byte> ToDataBlock(bool value)
        {
            return ToDataBlock_Headerless(BitConverter.GetBytes(value));
        }

        ////****************************************************************************


        public static List<byte> ToDataBlock(string block_header, List<byte> block_data)
        {
            return ToDataBlock(block_header, block_data.ToArray());
        }

        public static List<byte> ToDataBlock(string block_header, byte[] block_data)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(StringToBlock(block_header));
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        public static List<byte> ToDataBlock(string block_header, string value)
        {
            return ToDataBlock(block_header, Encoding.UTF8.GetBytes(value));
        }

        public static List<byte> ToDataBlock(string block_header, int value)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(StringToBlock(block_header));
            byte[] block_data = BitConverter.GetBytes(value);
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        public static List<byte> ToDataBlock(string block_header, float value)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(StringToBlock(block_header));
            byte[] block_data = BitConverter.GetBytes(value);
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        public static List<byte> ToDataBlock(string block_header, double value)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(StringToBlock(block_header));
            byte[] block_data = BitConverter.GetBytes(value);
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        public static List<byte> ToDataBlock(string block_header, long value)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(StringToBlock(block_header));
            byte[] block_data = BitConverter.GetBytes(value);
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        public static List<byte> ToDataBlock(string block_header, bool value)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(StringToBlock(block_header));
            byte[] block_data = BitConverter.GetBytes(value);
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        private static List<byte> ToDataBlock_Headerless(byte[] block_data)
        {
            List<byte> bt = new List<byte>();
            bt.AddRange(BitConverter.GetBytes(block_data.Length));
            bt.AddRange(block_data);
            return bt;
        }

        public static int BlockToString(byte[] bt, int bt_offset, out string value)
        {
            int offset_block = BitConverter.ToInt32(bt, bt_offset);
            value = Encoding.ASCII.GetString(bt, bt_offset + 4, offset_block);
            return offset_block += 4;
        }

        public static List<byte> StringToBlock(string value)
        {
            List<byte> bt = new List<byte>();
            byte[] bt_value = Encoding.ASCII.GetBytes(value);
            bt.AddRange(BitConverter.GetBytes(bt_value.Length));
            bt.AddRange(bt_value);
            return bt;
        }

        public static List<byte[]> BlocksToBytes(byte[] bt)
        {
            List<byte[]> bt_group = new List<byte[]>();

            List<byte> bt_list = new List<byte>(bt);
            int bt_offset = 0;
            while (bt_offset < bt.Length - 1)
            {
                int block_len = BitConverter.ToInt32(bt, bt_offset);
                bt_offset += 4;
                byte[] bt_block = bt_list.GetRange(bt_offset, block_len).ToArray();
                bt_offset += block_len;
                bt_group.Add(bt_block);
            }

            return bt_group;
        }

        public static string SerializeObject<T>(this T toSerialize)
        {
            //long ts = XUtils.GetAbsoluteTime();
            XmlSerializer xmlSerializer = new XmlSerializer(toSerialize.GetType());
            using (StringWriter textWriter = new StringWriter())
            {
                xmlSerializer.Serialize(textWriter, toSerialize);
                string s = textWriter.ToString();
                int id = s.IndexOf(">");
                //Debug.Log(GetTimePassed(ts) + toSerialize.GetType().ToString());
                return s.Substring(id + 1, s.Length - id - 1);
            }
        }

        public static T XmlDeserializeFromString<T>(this string objectData)
        {
            return (T)XmlDeserializeFromString(objectData, typeof(T));
        }

        public static object XmlDeserializeFromString(this string objectData, System.Type type)
        {
            var serializer = new XmlSerializer(type);
            object result;

            using (TextReader reader = new StringReader(objectData))
            {
                result = serializer.Deserialize(reader);
            }

            return result;
        }
    }
}
