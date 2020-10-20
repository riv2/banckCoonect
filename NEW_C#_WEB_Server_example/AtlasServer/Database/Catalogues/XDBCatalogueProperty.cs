using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Описывает конфигурацию каталога
    /// </summary>
    [Serializable]
    public class XDBCatalogueProperty
    {
        public long Uid { set; get; }

        [JsonProperty("catalogue_uid")]
        public long CatalogueUid { set; get; }

        [JsonProperty("code")]
        public string Code { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }
        
        [JsonProperty("value")]
        public string Value { set; get; }

        public XDBCatalogueProperty()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProperty FromBytes(byte[] bt_data)
        {
            XDBCatalogueProperty db_property = new XDBCatalogueProperty();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_property.Uid = br.ReadInt64();
                db_property.CatalogueUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_property.Code = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_property.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_property.Value = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                db_property = null;
            }

            br.Close();
            ms.Close();
            return db_property;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_property"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueProperty db_property)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_property.Uid));
            bw.Write(BitConverter.GetBytes((long)db_property.CatalogueUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_property.Code)));
            bw.Write(Encoding.UTF8.GetBytes(db_property.Code));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_property.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_property.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_property.Value)));
            bw.Write(Encoding.UTF8.GetBytes(db_property.Value));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_property"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueProperty db_property)
        {
            if (db_property == null) return false;
            if (db_property.CatalogueUid != CatalogueUid) return false;
            if (db_property.Code != Code) return false;
            if (db_property.Name != Name) return false;
            if (db_property.Value != Value) return false;
            return true;
        }
    }
}
