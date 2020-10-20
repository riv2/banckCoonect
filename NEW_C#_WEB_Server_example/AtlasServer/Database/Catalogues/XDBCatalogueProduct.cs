using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Характеристики продукта
    /// </summary>
    public class XDBCatalogueFlag
    {
        /* Характеристики материалов */

        /// <summary>
        /// Матовая поверхность
        /// </summary>
        public const int SURFACE_MATTE = 0x00;

        /// <summary>
        /// Глянцевая поверхность
        /// </summary>
        public const int SURFACE_GLOSSY = 0x01;

        /// <summary>
        /// Считать как мозаику
        /// </summary>
        public const int COUNTING_MOSAIC = 0x02;

        /// <summary>
        /// Считать как декор
        /// </summary>
        public const int COUNTING_DECORE = 0x04;

        /// <summary>
        /// Считать как рулоны
        /// </summary>
        public const int COUNTING_ROLLS = 0x08;

        /// <summary>
        /// Считать как литры
        /// </summary>
        public const int COUNTING_PAINTS = 0x10;

        /* Характеристики объектов */

        /// <summary>
        /// Отражающая поверхность (зеркало)
        /// </summary>
        public const int SURFACE_MIRROR = 0x01;
    }

    /// <summary>
    /// Статус товара
    /// </summary>
    public class XDBCatalogueStatus
    {
        /// <summary>
        /// Товар недоступен для выбора в каталоге
        /// </summary>
        public const int UNAVAILABLE = 0;

        /// <summary>
        /// Товар можно выбрать в каталоге без учета остатков
        /// </summary>
        public const int AVAILABLE = 1;

        /// <summary>
        /// Товар можно выбрать в каталоге, и учитывает остатки
        /// </summary>
        public const int INSTOCK = 2;
    }

    /// <summary>
    /// Организует доступ к таблице товаров, хранящей их характеристики
    /// </summary>
    [Serializable]

    public class XDBCatalogueProduct
    {
        /// <summary>
        /// Уникальный идентификатор товара в рамках всех каталогов.
        /// Определяется совокупностью идентификаторов текущего каталога и идентификатором товара внутри него.
        /// </summary>
        [JsonProperty("uniq")]
        public string Uniq { set; get; }

        /// <summary>
        /// Владелец записи
        /// </summary>
        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        /// <summary>
        /// Производитель
        /// </summary>
        [JsonProperty("manufactorer")]
        public string Manufactorer { set; get; }

        /// <summary>
        /// Наименование
        /// </summary>
        [JsonProperty("name")]
        public string Name { set; get; }

        /// <summary>
        /// Размерность по оси X в миллиметрах (ширина)
        /// </summary>
        [JsonProperty("product_type")]
        public int ProductType { set; get; }

        /// <summary>
        /// Размерность по оси X в миллиметрах (ширина)
        /// </summary>
        [JsonProperty("dim_x")]
        public int DimX { set; get; }

        /// <summary>
        /// Размерность по оси Y в миллиметрах (высота)
        /// </summary>
        [JsonProperty("dim_y")]
        public int DimY { set; get; }

        /// <summary>
        /// Размерность по оси Z в миллиметрах (толщина)
        /// </summary>
        [JsonProperty("dim_z")]
        public int DimZ { set; get; }

        /// <summary>
        /// Характеристики товара (битовая маска)
        /// </summary>
        [JsonProperty("flags")]
        public long Flags { set; get; }

        /// <summary>
        /// Статус товара
        /// </summary>
        [JsonProperty("status")]
        public int Status { set; get; }

        /// <summary>
        /// Каталог, к которому относится данная хапись
        /// </summary>
        [JsonProperty("catalogue_uid")]
        public long CatalogueUid { set; get; }

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

        public XDBCatalogueProduct()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProduct FromBytes(byte[] bt_data)
        {
            XDBCatalogueProduct db_product = new XDBCatalogueProduct();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_product.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_product.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_product.Manufactorer = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_product.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_product.DimX = br.ReadInt32();
                db_product.DimY = br.ReadInt32();
                db_product.DimZ = br.ReadInt32();
                db_product.Flags = br.ReadInt64();
                db_product.Status = br.ReadInt32();
                db_product.CatalogueUid = br.ReadInt64();
                db_product.DateModified = br.ReadInt64();
                db_product.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_product = null;
            }

            br.Close();
            ms.Close();
            return db_product;
        }

        /// <summary>
        /// Десериализует данные заголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueProduct HandlerFromBytesLTS(byte[] bt_data)
        {
            XDBCatalogueProduct db_product = new XDBCatalogueProduct();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_product.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_product.DateModified = br.ReadInt64();
                db_product.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_product = null;
            }

            br.Close();
            ms.Close();
            return db_product;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_product"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueProduct db_product)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_product.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_product.Uniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_product.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_product.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_product.Manufactorer)));
            bw.Write(Encoding.UTF8.GetBytes(db_product.Manufactorer));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_product.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_product.Name));

            bw.Write(BitConverter.GetBytes((int)db_product.DimX));
            bw.Write(BitConverter.GetBytes((int)db_product.DimY));
            bw.Write(BitConverter.GetBytes((int)db_product.DimZ));
            bw.Write(BitConverter.GetBytes((long)db_product.Flags));
            bw.Write(BitConverter.GetBytes((int)db_product.Status));
            bw.Write(BitConverter.GetBytes((long)db_product.CatalogueUid));
            bw.Write(BitConverter.GetBytes((long)db_product.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_product.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков в массив байт.
        /// </summary>
        /// <param name="db_product"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytesLTS(XDBCatalogueProduct db_product)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_product.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_product.Uniq));
            bw.Write(BitConverter.GetBytes((long)db_product.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_product.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_product"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueProduct db_product)
        {
            if (db_product == null) return false;
            if (db_product.Manufactorer != Manufactorer) return false;
            if (db_product.Name != Name) return false;
            if (db_product.DimX != DimX) return false;
            if (db_product.DimY != DimY) return false;
            if (db_product.DimZ != DimZ) return false;
            if (db_product.Flags != Flags) return false;
            if (db_product.Status != Status) return false;
            return true;
        }
    }
}