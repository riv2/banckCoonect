
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Профиль пользователя системы,
    /// содержит информацию, характеризующую пользователя
    /// </summary>
    [Serializable]
    public class XDBCompanyBranch
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("address")]
        public string Address { set; get; }

        public XDBCompanyBranch()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCompanyBranch FromBytes(byte[] bt_data)
        {
            XDBCompanyBranch db_settings = new XDBCompanyBranch();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_settings.Uid = br.ReadInt64();
                db_settings.CompanyUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_settings.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_settings.Address = Encoding.UTF8.GetString(br.ReadBytes(len));
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
        public static byte[] ToBytes(XDBCompanyBranch db_settings)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_settings.Uid));
            bw.Write(BitConverter.GetBytes((long)db_settings.CompanyUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.Address)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.Address));

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
        public bool CompareTo(XDBCompanyBranch db_settings)
        {
            if (db_settings == null) return false;
            if (db_settings.CompanyUid != CompanyUid) return false;
            if (db_settings.Name != Name) return false;
            if (db_settings.Address != Address) return false;
            return true;
        }
    }
}
