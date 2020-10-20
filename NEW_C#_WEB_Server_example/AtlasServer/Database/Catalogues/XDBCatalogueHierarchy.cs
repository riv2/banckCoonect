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
    public class XDBCatalogueHierarchy
    {
        /// <summary>
        /// Уникальный идентификатор уровня вложенности (директории)
        /// </summary>
        [JsonProperty("uniq")]
        public string Uniq { set; get; }

        /// <summary>
        /// Владелец информации
        /// </summary>
        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        /// <summary>
        /// Тип товара (Объект(1) или Материал(2))
        /// </summary>
        [JsonProperty("product_type")]
        public int ProductType { set; get; }

        /// <summary>
        /// Наименование уровня вложенности (директории)
        /// </summary>
        [JsonProperty("name")]
        public string Name { set; get; }

        /// <summary>
        /// Уникальный идентификатор уровня вложенности (директории)
        /// </summary>
        [JsonProperty("path")]
        public string Path { set; get; }

        /// <summary>
        /// Уникальный идентификатор родительского уровня вложенности.
        /// Если пустой или NULL - значит корневая директория.
        /// </summary>
        [JsonProperty("parent_uniq")]
        public string ParentUniq { set; get; }

        /// <summary>
        /// Каталог, которому соотетствует данная запись
        /// </summary>
        [JsonProperty("catalogue_uid")]
        public long CatalogueUid { set; get; }

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

        public XDBCatalogueHierarchy()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueHierarchy FromBytes(byte[] bt_data)
        {
            XDBCatalogueHierarchy db_hierarchy = new XDBCatalogueHierarchy();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_hierarchy.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_hierarchy.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_hierarchy.ProductType = br.ReadInt32();

                len = br.ReadInt32();
                db_hierarchy.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_hierarchy.Path = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_hierarchy.ParentUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_hierarchy.CatalogueUid = br.ReadInt64();
                br.ReadInt64();
                db_hierarchy.DateModified = br.ReadInt64();
                db_hierarchy.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_hierarchy = null;
            }

            br.Close();
            ms.Close();
            return db_hierarchy;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_hierarchy"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueHierarchy db_hierarchy)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_hierarchy.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_hierarchy.Uniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_hierarchy.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_hierarchy.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)db_hierarchy.ProductType));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_hierarchy.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_hierarchy.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_hierarchy.Path)));
            bw.Write(Encoding.UTF8.GetBytes(db_hierarchy.Path));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_hierarchy.ParentUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_hierarchy.ParentUniq));

            bw.Write(BitConverter.GetBytes((long)db_hierarchy.CatalogueUid));
            bw.Write(BitConverter.GetBytes((long)0));
            bw.Write(BitConverter.GetBytes((long)db_hierarchy.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_hierarchy.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_hierarchy"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueHierarchy db_hierarchy)
        {
            if (db_hierarchy == null) return false;
            if (db_hierarchy.Path != Path) return false;
            if (db_hierarchy.Name != Name) return false;
            if (db_hierarchy.MemberUniq != MemberUniq) return false;
            if (db_hierarchy.ParentUniq != ParentUniq) return false;
            if (db_hierarchy.ProductType != ProductType) return false;
            if (db_hierarchy.CatalogueUid != CatalogueUid) return false;
            return true;
        }
    }
}