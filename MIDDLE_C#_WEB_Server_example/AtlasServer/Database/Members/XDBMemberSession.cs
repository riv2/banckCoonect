
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Сессия пользователя
    /// содержит информацию о текущей сессии ползователя
    /// </summary>
    [Serializable]
    public class XDBMemberSession
    {
        [JsonProperty("uid")]
        public int Uid { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("session_uniq")]
        public string SessionUniq { set; get; }

        [JsonProperty("date_expires")]
        public long DateExpires { set; get; }

        public XDBMemberSession()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBMemberSession FromBytes(byte[] bt_data)
        {
            XDBMemberSession db_settings = new XDBMemberSession();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_settings.Uid = br.ReadInt32();

                int len = br.ReadInt32();
                db_settings.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.SessionUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_settings.DateExpires = br.ReadInt64();
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
        public static byte[] ToBytes(XDBMemberSession db_settings)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)db_settings.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.SessionUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.SessionUniq));

            bw.Write(BitConverter.GetBytes((long)db_settings.DateExpires));

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
        public bool CompareTo(XDBMemberSession db_settings)
        {
            if (db_settings == null) return false;
            if (db_settings.SessionUniq != SessionUniq) return false;
            return true;
        }
    }
}
