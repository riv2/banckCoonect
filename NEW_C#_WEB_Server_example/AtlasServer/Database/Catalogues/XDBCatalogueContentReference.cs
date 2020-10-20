using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    public class XDBCatalogueContentReferenceType
    {
        public const int RESOURCE = 0;
        public const int MATERIAL = 1;
        public const int OBJECT = 2;
    }


    /// <summary>
    /// Связывает контент с другими элементами контента
    /// </summary>
    [Serializable]
    public class XDBCatalogueContentReference
    {
        /// <summary>
        /// Уникальный идентификатор
        /// </summary>
        public long Uid { set; get; }

        /// <summary>
        /// Уникальный идентификатор исходного элемента - владельца связи
        /// </summary>
        [JsonProperty("content_uniq")]
        public string ContentUniq { set; get; }

        /// <summary>
        /// Слот свзяи в сиходном элементе
        /// </summary>
        [JsonProperty("content_slot")]
        public int ContentSlot { set; get; }

        /// <summary>
        /// Уникальный идентификатор связываемого элемента
        /// </summary>
        [JsonProperty("reference_uniq")]
        public string ReferenceUniq { set; get; }

        /// <summary>
        /// Тип связываемого элемента
        /// </summary>
        [JsonProperty("reference_type")]
        public int ReferenceType { set; get; }

        /// <summary>
        /// Дата последнего обновления информации о товаре
        /// Если значение изменилось, требуется обновление всех данных.
        /// </summary>
        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        /// <summary>
        /// Дата удаления записи, если не 0, значит запись больше не актуальна и требует удаления.
        /// </summary>
        [JsonProperty("date_deleted")]
        public long DateDeleted { set; get; }

        public XDBCatalogueContentReference()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueContentReference FromBytes(byte[] bt_data)
        {
            XDBCatalogueContentReference db_reference = new XDBCatalogueContentReference();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_reference.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_reference.ContentUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_reference.ContentSlot = br.ReadInt32();

                len = br.ReadInt32();
                db_reference.ReferenceUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_reference.ReferenceType = br.ReadInt32();
                db_reference.DateModified = br.ReadInt64();
                db_reference.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_reference = null;
            }

            br.Close();
            ms.Close();
            return db_reference;
        }

        /// <summary>
        /// Десериализует данные щаголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueContentReference HandlerFromBytes(byte[] bt_data)
        {
            XDBCatalogueContentReference db_reference = new XDBCatalogueContentReference();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_reference.Uid = br.ReadInt64();
                int len = br.ReadInt32();
                db_reference.ContentUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_reference.DateModified = br.ReadInt64();
                db_reference.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_reference = null;
            }

            br.Close();
            ms.Close();
            return db_reference;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_reference"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueContentReference db_reference)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_reference.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ContentUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ContentUniq));
            bw.Write(BitConverter.GetBytes((int)db_reference.ContentSlot));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ReferenceUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ReferenceUniq));
            bw.Write(BitConverter.GetBytes((int)db_reference.ReferenceType));
            bw.Write(BitConverter.GetBytes((long)db_reference.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_reference.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков в массив байт.
        /// </summary>
        /// <param name="db_reference"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytes(XDBCatalogueContentReference db_reference)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_reference.Uid));
            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ContentUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ContentUniq));
            bw.Write(BitConverter.GetBytes((long)db_reference.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_reference.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_reference"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueContentReference db_reference)
        {
            if (db_reference == null) return false;
            if (db_reference.ContentUniq != ContentUniq) return false;
            if (db_reference.ContentSlot != ContentSlot) return false;
            if (db_reference.ReferenceType != ReferenceType) return false;
            if (db_reference.ReferenceUniq != ReferenceUniq) return false;
            return true;
        }
    }
}