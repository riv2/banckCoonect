using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    public class XDBCatalogueContentType
    {
        public const int THUMBNAIL = 0;
        public const int MATERIAL = 1;
        public const int OBJECT = 2;
    }

    /// <summary>
    /// Организует доступ к справочнику контента (объект или материал)
    /// </summary>
    [Serializable]
    public class XDBCatalogueContent
    {
        /// <summary>
        /// Уникальный идентификатор объекта в рамках всех каталогов.
        /// Определяется совокупностью идентификаторов текущего каталога и идентификатором товара внутри него.
        /// </summary>
        [JsonProperty("uniq")]
        public string Uniq { set; get; }

        /// <summary>
        /// Идентификатор каталога
        /// </summary>
        [JsonProperty("catalogue_uid")]
        public long CatalogueUid { set; get; }

        /// <summary>
        /// Тип контента
        /// </summary>
        [JsonProperty("content_type")]
        public int ContentType { set; get; }

        /// <summary>
        /// Идентификатор каталога
        /// </summary>
        [JsonProperty("path_source")]
        public string PathSource { set; get; }

        /// <summary>
        /// Дата последнего обновления информации в записе
        /// Если значение изменилось, требуется обновление всех данных.
        /// </summary>
        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        /// <summary>
        /// Дата удаления записи, если не 0, значит запись больше не актуальна и требует удаления.
        /// </summary>
        [JsonProperty("date_deleted")]
        public long DateDeleted { set; get; }

        public XDBCatalogueContent()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueContent FromBytes(byte[] bt_data)
        {
            XDBCatalogueContent db_object = new XDBCatalogueContent();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_object.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_object.CatalogueUid = br.ReadInt64();
                db_object.ContentType = br.ReadInt32();

                len = br.ReadInt32();
                db_object.PathSource = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_object.DateModified = br.ReadInt64();
                db_object.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_object = null;
            }

            br.Close();
            ms.Close();
            return db_object;
        }

        /// <summary>
        /// Десериализует данные заголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueContent HandlerFromBytes(byte[] bt_data)
        {
            XDBCatalogueContent db_object = new XDBCatalogueContent();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_object.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_object.CatalogueUid = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_object = null;
            }

            br.Close();
            ms.Close();
            return db_object;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_object"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueContent db_object)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_object.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_object.Uniq));

            bw.Write(BitConverter.GetBytes((long)db_object.CatalogueUid));

            bw.Write(BitConverter.GetBytes((int)db_object.ContentType));
            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_object.PathSource)));
            bw.Write(Encoding.UTF8.GetBytes(db_object.PathSource));

            bw.Write(BitConverter.GetBytes((long)db_object.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_object.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков  в массив байт.
        /// </summary>
        /// <param name="db_object"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytes(XDBCatalogueContent db_object)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_object.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_object.Uniq));
            bw.Write(BitConverter.GetBytes((long)db_object.CatalogueUid));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков  в массив байт.
        /// </summary>
        /// <param name="db_object"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytesLTS(XDBCatalogueContent db_object)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_object.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_object.Uniq));
            bw.Write(BitConverter.GetBytes((long)db_object.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_object.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_object"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueContent db_object)
        {
            if (db_object == null) return false;
            if (db_object.PathSource != PathSource) return false;
            if (db_object.ContentType != ContentType) return false;
            return true;
        }
    }
}