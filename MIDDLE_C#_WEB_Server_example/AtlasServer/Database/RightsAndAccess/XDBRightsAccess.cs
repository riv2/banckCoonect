
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBRightsAccess
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("owner")]
        public string Owner { set; get; }

        [JsonProperty("permission_uid")]
        public long PermissionUid { set; get; }

        [JsonProperty("role_uid")]
        public long RoleUid { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("action")]
        public string Action { set; get; }

        public XDBRightsAccess()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBRightsAccess FromBytes(byte[] bt_data)
        {
            XDBRightsAccess db_access = new XDBRightsAccess();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_access.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_access.Owner = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_access.PermissionUid = br.ReadInt64();
                db_access.RoleUid = br.ReadInt64();

                len = br.ReadInt32();
                db_access.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_access.Action = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                
                db_access = null;
            }

            br.Close();
            ms.Close();
            return db_access;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_access"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBRightsAccess db_access)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_access.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_access.Owner)));
            bw.Write(Encoding.UTF8.GetBytes(db_access.Owner));

            bw.Write(BitConverter.GetBytes((long)db_access.PermissionUid));
            bw.Write(BitConverter.GetBytes((long)db_access.RoleUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_access.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_access.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_access.Action)));
            bw.Write(Encoding.UTF8.GetBytes(db_access.Action));

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
        public bool CompareTo(XDBRightsAccess db_access)
        {
            if (db_access == null) return false;
            if (db_access.Uid != Uid) return false;
            return true;
        }
    }
}
