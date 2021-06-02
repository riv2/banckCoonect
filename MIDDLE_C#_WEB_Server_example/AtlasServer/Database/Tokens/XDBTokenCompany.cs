
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDTokenCompany
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("token_uid")]
        public long TokenUid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        public XDTokenCompany()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDTokenCompany FromBytes(byte[] bt_data)
        {
            XDTokenCompany db_token = new XDTokenCompany();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_token.Uid = br.ReadInt64();
                db_token.TokenUid = br.ReadInt64();
                db_token.CompanyUid = br.ReadInt64();
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
        /// <param name="db_company"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDTokenCompany db_company)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_company.Uid));
            bw.Write(BitConverter.GetBytes((long)db_company.TokenUid));
            bw.Write(BitConverter.GetBytes((long)db_company.CompanyUid));

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
        public bool CompareTo(XDTokenCompany db_access)
        {
            if (db_access == null) return false;
            if (db_access.TokenUid != TokenUid) return false;
            if (db_access.CompanyUid != CompanyUid) return false;
            return true;
        }
    }
}
