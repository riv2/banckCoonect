
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBRightsPermission
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("code")]
        public string Code { set; get; }

        [JsonProperty("date_deleted")]
        public long DateDeleted { set; get; }

        public XDBRightsPermission()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBRightsPermission FromBytes(byte[] bt_data)
        {
            XDBRightsPermission db_permission = new XDBRightsPermission();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_permission.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_permission.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_permission.Code = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_permission.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                
                db_permission = null;
            }

            br.Close();
            ms.Close();
            return db_permission;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_pemission"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBRightsPermission db_pemission)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_pemission.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_pemission.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_pemission.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_pemission.Code)));
            bw.Write(Encoding.UTF8.GetBytes(db_pemission.Code));

            bw.Write(BitConverter.GetBytes((long)db_pemission.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_pemission"></param>
        /// <returns></returns>
        public bool CompareTo(XDBRightsPermission db_pemission)
        {
            if (db_pemission == null) return false;
            if (db_pemission.Code != Code) return false;
            return true;
        }
    }
}
