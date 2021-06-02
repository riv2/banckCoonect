
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBToken
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("value")]
        public string Value { set; get; }

        [JsonProperty("type")]
        public string Type { set; get; }

        public XDBToken()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBToken FromBytes(byte[] bt_data)
        {
            XDBToken db_token = new XDBToken();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_token.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_token.Value = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_token.Type = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                
                db_token = null;
            }

            br.Close();
            ms.Close();
            return db_token;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_token"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBToken db_token)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_token.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_token.Value)));
            bw.Write(Encoding.UTF8.GetBytes(db_token.Value));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_token.Type)));
            bw.Write(Encoding.UTF8.GetBytes(db_token.Type));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_token"></param>
        /// <returns></returns>
        public bool CompareTo(XDBToken db_token)
        {
            if (db_token == null) return false;
            if (db_token.Value != Value) return false;
            if (db_token.Type != Type) return false;
            return true;
        }
    }
}
