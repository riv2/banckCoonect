using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Организует доступ к таблице иерархии ресурсов
    /// </summary>
    [Serializable]
    public class XDBCatalogueProductGroup
    {
        /// <summary>
        /// Уникальный идентификатор связи
        /// </summary>
        public long Uid { set; get; }

        /// <summary>
        /// Владелец информации
        /// </summary>
        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        /// <summary>
        /// Уникальный идентификатор группы каталога
        /// </summary>
        [JsonProperty("group_uniq")]
        public string GroupUniq { set; get; }

        /// <summary>
        /// Уникальный идентификатор продукта
        /// </summary>
        [JsonProperty("product_uniq")]
        public string ProductUniq { set; get; }

        /// <summary>
        /// Дата последнего обновления записи
        /// Если значение изменилось, требуется обновление всех данных.
        /// </summary>
        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        /// <summary>
        /// Дата удаления записи, если не 0, значит запись больше не актуальна и требует удаления.
        /// </summary>
        [JsonProperty("date_deleted")]
        public long DateDeleted { set; get; }

        public XDBCatalogueProductGroup()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProductGroup FromBytes(byte[] bt_data)
        {
            XDBCatalogueProductGroup db_group = new XDBCatalogueProductGroup();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_group.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_group.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_group.GroupUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_group.ProductUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_group.DateModified = br.ReadInt64();
                db_group.DateDeleted = br.ReadInt64();
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
        /// Десериализует данные заголовокв  из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProductGroup HandlerFromBytesLTS(byte[] bt_data)
        {
            XDBCatalogueProductGroup db_group = new XDBCatalogueProductGroup();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_group.Uid = br.ReadInt64();
                int len = br.ReadInt32();
                db_group.ProductUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_group.DateModified = br.ReadInt64();
                db_group.DateDeleted = br.ReadInt64();
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
        public static byte[] ToBytes(XDBCatalogueProductGroup db_group)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_group.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.GroupUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.GroupUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.ProductUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.ProductUniq));

            bw.Write(BitConverter.GetBytes((long)db_group.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_group.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовка в массив байт.
        /// </summary>
        /// <param name="db_group"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytesLTS(XDBCatalogueProductGroup db_group)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_group.Uid));
            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.ProductUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.ProductUniq));
            bw.Write(BitConverter.GetBytes((long)db_group.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_group.DateDeleted));

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
        public bool CompareTo(XDBCatalogueProductGroup db_group)
        {
            if (db_group == null) return false;
            if (db_group.MemberUniq != MemberUniq) return false;
            if (db_group.GroupUniq != GroupUniq) return false;
            if (db_group.ProductUniq != ProductUniq) return false;
            return true;
        }
    }
}