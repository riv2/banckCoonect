
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Профиль компании-партнера.
    /// Содержит основную информацию о компании
    /// </summary>
    [Serializable]
    public class XDBCompany
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("country")]
        public string Country { set; get; }

        [JsonProperty("city")]
        public string City { set; get; }

        [JsonProperty("hq_address")]
        public string HQAddress { set; get; }

        [JsonProperty("logo")]
        public string Logo { set; get; }

        [JsonProperty("corporate_id")]
        public string ExternalId { set; get; }

        [JsonProperty("followers")]
        public string Followers { set; get; }

        public XDBCompany()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCompany FromBytes(byte[] bt_data)
        {
            XDBCompany db_settings = new XDBCompany();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_settings.Uid = br.ReadInt32();

                int len = br.ReadInt32();
                db_settings.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Country = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.City = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.HQAddress = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Logo = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.ExternalId = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Followers = Encoding.UTF8.GetString(br.ReadBytes(len));
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
        public static byte[] ToBytes(XDBCompany db_settings)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)db_settings.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Country)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Country));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.City)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.City));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.HQAddress)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.HQAddress));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Logo)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Logo));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.ExternalId)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.ExternalId));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Followers)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Followers));

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
        public bool CompareTo(XDBCompany db_settings)
        {
            if (db_settings == null) return false;
            if (db_settings.Name != Name) return false;
            if (db_settings.Country != Country) return false;
            if (db_settings.City != City) return false;
            if (db_settings.HQAddress != HQAddress) return false;
            if (db_settings.ExternalId != ExternalId) return false;
            return true;
        }
    }
}
