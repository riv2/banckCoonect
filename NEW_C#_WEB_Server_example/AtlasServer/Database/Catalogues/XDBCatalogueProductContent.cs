using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    public class XDBCatalogueProductContentType
    {
        public const int THUMBNAIL = 0;
        public const int MATERIAL = 1;
        public const int OBJECT = 2;
    }

    /// <summary>
    /// Связывает продукт с его компонентами, для его корректного отображения в программе.
    /// Реализует механизм отношений: один продукт - несколько компонент.
    /// </summary>
    [Serializable]
    public class XDBCatalogueProductContent
    {
        /// <summary>
        /// Уникальный идентификатор
        /// </summary>

        public long Uid { set; get; }

        /// <summary>
        /// Уникальный идентификатор исходного продукта - владельца связи
        /// </summary>
        [JsonProperty("product_uniq")]
        public string ProductUniq { set; get; }

        /// <summary>
        /// Уникальный идентификатор связываемого компонента
        /// </summary>
        [JsonProperty("content_uniq")]
        public string ContentUniq { set; get; }

        /// <summary>
        /// Тип связываемого компонента
        /// </summary>
        [JsonProperty("content_type")]
        public int ContentType { set; get; }

        /// <summary>
        /// Дата последнего обновления информации о компоненте
        /// Если значение изменилось, требуется обновление всех данных.
        /// </summary>
        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        /// <summary>
        /// Дата удаления записи, если не 0, значит запись больше не актуальна и требует удаления.
        /// </summary>
        [JsonProperty("date_deleted")]
        public long DateDeleted { set; get; }

        public XDBCatalogueProductContent()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProductContent FromBytes(byte[] bt_data)
        {
            XDBCatalogueProductContent db_reference = new XDBCatalogueProductContent();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_reference.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_reference.ProductUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_reference.ContentType = br.ReadInt32();

                len = br.ReadInt32();
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
        /// Десериализует данные щаголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProductContent HandlerFromBytes(byte[] bt_data)
        {
            XDBCatalogueProductContent db_reference = new XDBCatalogueProductContent();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_reference.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_reference.ProductUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_reference.ContentUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
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
        public static byte[] ToBytes(XDBCatalogueProductContent db_reference)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_reference.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ProductUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ProductUniq));

            bw.Write(BitConverter.GetBytes((int)db_reference.ContentType));

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
        /// Сериализует данные заголовков в массив байт.
        /// </summary>
        /// <param name="db_reference"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytes(XDBCatalogueProductContent db_reference)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_reference.Uid));
            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ProductUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ProductUniq));
            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ContentUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ContentUniq));

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
        public bool CompareTo(XDBCatalogueProductContent db_reference)
        {
            if (db_reference == null) return false;
            if (db_reference.ProductUniq != ProductUniq) return false;
            if (db_reference.ContentType != ContentType) return false;
            if (db_reference.ContentUniq != ContentUniq) return false;
            return true;
        }
    }
}