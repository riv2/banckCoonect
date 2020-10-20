
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBRightsGroup
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("parent_uid")]
        public long ParentUid { set; get; }

        [JsonProperty("owner")]
        public string Owner { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        public XDBRightsGroup()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBRightsGroup FromBytes(byte[] bt_data)
        {
            XDBRightsGroup db_group = new XDBRightsGroup();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_group.Uid = br.ReadInt64();
                db_group.ParentUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_group.Owner = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_group.Name = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                
                db_group = null;
            }

            br.Close();
            ms.Close();
            return db_group;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_group"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBRightsGroup db_group)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_group.Uid));
            bw.Write(BitConverter.GetBytes((long)db_group.ParentUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.Owner)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.Owner));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.Name));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_group"></param>
        /// <returns></returns>
        public bool CompareTo(XDBRightsGroup db_group)
        {
            if (db_group == null) return false;
            if (db_group.Uid != Uid) return false;
            return true;
        }
    }
}
