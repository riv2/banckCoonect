
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Настройки программного комплекса
    /// </summary>
    [Serializable]
    public class XDBMemberSettings
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("value")]
        public string Value { set; get; }

        [JsonProperty("owner")]
        public string Owner { set; get; }

        public XDBMemberSettings()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBMemberSettings FromBytes(byte[] bt_data)
        {
            XDBMemberSettings db_settings = new XDBMemberSettings();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_settings.Uid = br.ReadInt32();

                int len = br.ReadInt32();
                db_settings.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Value = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Owner = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                
                db_settings = null;
            }

            br.Close();
            ms.Close();
            return db_settings;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_settings"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBMemberSettings db_settings)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)db_settings.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Value)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Value));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Owner)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Owner));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_settings"></param>
        /// <returns></returns>
        public bool CompareTo(XDBMemberSettings db_settings)
        {
            if (db_settings == null) return false;
            if (db_settings.Uid != Uid) return false;
            return true;
        }
    }
}
