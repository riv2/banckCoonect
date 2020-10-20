
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBTokenVariable
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("token_uid")]
        public long TokenUid { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("value")]
        public string Value { set; get; }

        [JsonProperty("owner")]
        public string Owner { set; get; }

        public XDBTokenVariable()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBTokenVariable FromBytes(byte[] bt_data)
        {
            XDBTokenVariable db_variable = new XDBTokenVariable();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_variable.Uid = br.ReadInt64();
                db_variable.TokenUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_variable.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_variable.Value = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_variable.Owner = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                
                db_variable = null;
            }

            br.Close();
            ms.Close();
            return db_variable;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_variable"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBTokenVariable db_variable)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_variable.Uid));
            bw.Write(BitConverter.GetBytes((long)db_variable.TokenUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_variable.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_variable.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_variable.Value)));
            bw.Write(Encoding.UTF8.GetBytes(db_variable.Value));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_variable.Owner)));
            bw.Write(Encoding.UTF8.GetBytes(db_variable.Owner));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_access"></param>
        /// <returns></returns>
        public bool CompareTo(XDBTokenVariable db_variable)
        {
            if (db_variable == null) return false;
            if (db_variable.TokenUid != TokenUid) return false;
            if (db_variable.Name != Name) return false;
            if (db_variable.Value != Value) return false;
            return true;
        }
    }
}
