
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    public class XDBCompanyRelationType
    {
        public const int SHARED_CATALOGUES_ACCESS = 1;
        public const int FULL_CATALOGUES_ACCESS = 2;
    }

    /// <summary>
    /// Профиль компании-партнера.
    /// Содержит основную информацию о компании
    /// </summary>
    [Serializable]

    public class XDBCompanyRelation
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("partner_uid")]
        public long PartnerUid { set; get; }

        [JsonProperty("relation_type")]
        public int RelationType { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("date_created")]
        public long DateCreated { set; get; }

        public XDBCompanyRelation()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCompanyRelation FromBytes(byte[] bt_data)
        {
            XDBCompanyRelation db_settings = new XDBCompanyRelation();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_settings.Uid = br.ReadInt64();
                db_settings.CompanyUid = br.ReadInt64();
                db_settings.PartnerUid = br.ReadInt64();
                db_settings.RelationType = br.ReadInt32();

                int len = br.ReadInt32();
                db_settings.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_settings.DateCreated = br.ReadInt64();
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
        public static byte[] ToBytes(XDBCompanyRelation db_settings)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_settings.Uid));
            bw.Write(BitConverter.GetBytes((long)db_settings.CompanyUid));
            bw.Write(BitConverter.GetBytes((long)db_settings.PartnerUid));
            bw.Write(BitConverter.GetBytes((int)db_settings.RelationType));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.MemberUniq));

            bw.Write(BitConverter.GetBytes((long)db_settings.DateCreated));
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
        public bool CompareTo(XDBCompanyRelation db_settings)
        {
            if (db_settings == null) return false;
            if (db_settings.CompanyUid != CompanyUid) return false;
            if (db_settings.PartnerUid != PartnerUid) return false;
            if (db_settings.RelationType != RelationType) return false;
            return true;
        }
    }
}
