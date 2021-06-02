
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Объект базы данных, описывающий базовую информацию о проекте
    /// </summary>
    [Serializable]
    public class XDBServerGlobals
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("code")]
        public string Code { set; get; }

        [JsonProperty("value")]
        public string Value { set; get; }

        [JsonProperty("description")]
        public string Description { set; get; }

        public XDBServerGlobals()
        {
        }
    
        public static XDBServerGlobals FromBytes(byte[] bt_data)
        {
            XDBServerGlobals project = new XDBServerGlobals();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                project.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                project.Code = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project.Value = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project.Description = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                project = null;
            }

            br.Close();
            ms.Close();
            return project;
        }

        public static byte[] ToBytes(XDBServerGlobals project)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)project.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.Code)));
            bw.Write(Encoding.UTF8.GetBytes(project.Code));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.Value)));
            bw.Write(Encoding.UTF8.GetBytes(project.Value));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.Description)));
            bw.Write(Encoding.UTF8.GetBytes(project.Description));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}

