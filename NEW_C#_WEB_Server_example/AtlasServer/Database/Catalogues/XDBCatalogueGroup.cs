using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Тип товара
    /// </summary>
    public class XDBCatalogueProductType
    {
        /// <summary>
        /// Неопределенный тип
        /// </summary>
        public const int UNDEFINED = 0;

        /// <summary>
        /// Объект
        /// </summary>
        public const int OBJECT = 1;

        /// <summary>
        /// Материал
        /// </summary>
        public const int MATERIAL = 2;
    }

    /// <summary>
    /// Организует доступ к таблице иерархии ресурсов
    /// </summary>
    [Serializable]

    public class XDBCatalogueGroup
    {
        /// <summary>
        /// Уникальный идентификатор группы товаров
        /// </summary>
        [JsonProperty("uniq")]
        public string Uniq { set; get; }

        /// <summary>
        /// Владелец информации
        /// </summary>
        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        /// <summary>
        /// Каталог, которому соотетствует данная запись
        /// </summary>
        [JsonProperty("catalogue_uid")]
        public long CatalogueUid { set; get; }

        /// <summary>
        /// Элемент иерархии
        /// </summary>
        [JsonProperty("hierarchy_uniq")]
        public string HierarchyUniq { set; get; }

        /// <summary>
        /// Тип товаров в группе
        /// </summary>
        [JsonProperty("product_type")]
        public int ProductType { set; get; }

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

        public XDBCatalogueGroup()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueGroup FromBytes(byte[] bt_data)
        {
            XDBCatalogueGroup db_group = new XDBCatalogueGroup();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_group.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_group.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_group.CatalogueUid = br.ReadInt64();

                len = br.ReadInt32();
                db_group.HierarchyUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_group.ProductType = br.ReadInt32();
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
        /// Десериализует данные заголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueGroup HandlerFromBytesLTS(byte[] bt_data)
        {
            XDBCatalogueGroup db_group = new XDBCatalogueGroup();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_group.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));
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
        public static byte[] ToBytes(XDBCatalogueGroup db_group)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.Uniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.MemberUniq));

            bw.Write(BitConverter.GetBytes((long)db_group.CatalogueUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.HierarchyUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.HierarchyUniq));

            bw.Write(BitConverter.GetBytes((int)db_group.ProductType));
            bw.Write(BitConverter.GetBytes((long)db_group.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_group.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков в массив байт.
        /// </summary>
        /// <param name="db_group"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytesLTS(XDBCatalogueGroup db_group)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.Uniq));
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
        public bool CompareTo(XDBCatalogueGroup db_group)
        {
            if (db_group == null) return false;
            if (db_group.MemberUniq != MemberUniq) return false;
            if (db_group.CatalogueUid != CatalogueUid) return false;
            if (db_group.HierarchyUniq != HierarchyUniq) return false;
            if (db_group.ProductType != ProductType) return false;
            return true;
        }
    }
}