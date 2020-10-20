
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

    public class XDBCompanyMember
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("branch_uid")]
        public long BranchUid { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("is_default")]
        public int IsDefault { set; get; }

        [JsonProperty("is_admin")]
        public int IsAdmin { set; get; }

        [JsonProperty("is_owner")]
        public int IsOwner { set; get; }

        public XDBCompanyMember()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCompanyMember FromBytes(byte[] bt_data)
        {
            XDBCompanyMember db_settings = new XDBCompanyMember();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_settings.Uid = br.ReadInt64();
                db_settings.CompanyUid = br.ReadInt64();
                db_settings.BranchUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_settings.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_settings.IsDefault = br.ReadInt32();
                db_settings.IsAdmin = br.ReadInt32();
                db_settings.IsOwner = br.ReadInt32();
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
        public static byte[] ToBytes(XDBCompanyMember db_settings)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_settings.Uid));
            bw.Write(BitConverter.GetBytes((long)db_settings.CompanyUid));
            bw.Write(BitConverter.GetBytes((long)db_settings.BranchUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_settings.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_settings.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)db_settings.IsDefault));
            bw.Write(BitConverter.GetBytes((int)db_settings.IsAdmin));
            bw.Write(BitConverter.GetBytes((int)db_settings.IsOwner));

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
        public bool CompareTo(XDBCompanyMember db_settings)
        {
            if (db_settings == null) return false;
            if (db_settings.CompanyUid != CompanyUid) return false;
            if (db_settings.BranchUid != BranchUid) return false;
            if (db_settings.MemberUniq != MemberUniq) return false;
            if (db_settings.IsDefault != IsDefault) return false;
            if (db_settings.IsAdmin != IsAdmin) return false;
            if (db_settings.IsOwner != IsOwner) return false;
            return true;
        }
    }
}
