using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    public enum XMatType { Wallpaper, Ceramic, Plaster, NaturalWood, Laminat, Plastic, Leather, Metal, Paint }
    public class XDBCatalogueMaterialType
    {
        /// <summary>
        /// Поверхность обоев
        /// </summary>
        public const int WALLPAPERS = 0;

        /// <summary>
        /// Поверхность керамической плитки
        /// </summary>
        public const int CERAMIC = 1;

        /// <summary>
        /// Поверхность штукатурки
        /// </summary>
        public const int PLASTER = 2;
        
        /// <summary>
        /// Поверхность дерева
        /// </summary>
        public const int WOOD = 3;

        /// <summary>
        /// Поверхность ламината
        /// </summary>
        public const int LAMINATE = 4;

        /// <summary>
        /// Поверхность пластика
        /// </summary>
        public const int PLASTIC = 5;

        /// <summary>
        /// Поверхность кожи
        /// </summary>
        public const int LEATHER = 6;

        /// <summary>
        /// Поверхность металлов
        /// </summary>
        public const int METAL = 7;

        /// <summary>
        /// Поверхность красок
        /// </summary>
        public const int PAINT = 8;
    }

    /// <summary>
    /// Организует доступ к таблице товаров, хранящей их характеристики
    /// </summary>
    [Serializable]
    public class XDBCatalogueMaterial
    {
        /// <summary>
        /// Уникальный идентификатор материала в рамках всех каталогов.
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
        /// Информация о цвете канала DIFFUSE
        /// </summary>
        [JsonProperty("diffuse")]
        public long DiffuseColor { set; get; }

        /// <summary>
        /// Информация о цвете канала SPECUALR
        /// </summary>
        [JsonProperty("specular")]
        public long SpecularColor { set; get; }

        /// <summary>
        /// Информация о цвете канала REFLECTION
        /// </summary>
        [JsonProperty("reflection")]
        public long ReflectionColor { set; get; }

        /// <summary>
        /// Информация о цвете канала IOR
        /// </summary>
        [JsonProperty("ior")]
        public long IorColor { set; get; }

        /// <summary>
        /// Тип поверхности материала
        /// </summary>
        [JsonProperty("material_type")]
        public int MaterialType { set; get; }

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

        public XDBCatalogueMaterial()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueMaterial FromBytes(byte[] bt_data)
        {
            XDBCatalogueMaterial db_material = new XDBCatalogueMaterial();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_material.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_material.CatalogueUid = br.ReadInt64();
                db_material.DiffuseColor = br.ReadInt64();
                db_material.SpecularColor = br.ReadInt64();
                db_material.ReflectionColor = br.ReadInt64();
                db_material.IorColor = br.ReadInt64();

                db_material.MaterialType = br.ReadInt32();
                db_material.DateModified = br.ReadInt64();
                db_material.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_material = null;
            }

            br.Close();
            ms.Close();
            return db_material;
        }

        /// <summary>
        /// Десериализует данные заголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueMaterial HandlerFromBytesLTS(byte[] bt_data)
        {
            XDBCatalogueMaterial db_material = new XDBCatalogueMaterial();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_material.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_material.DateModified = br.ReadInt64();
                db_material.DateDeleted = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_material = null;
            }

            br.Close();
            ms.Close();
            return db_material;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_material"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueMaterial db_material)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_material.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_material.Uniq));

            bw.Write(BitConverter.GetBytes((long)db_material.CatalogueUid));
            bw.Write(BitConverter.GetBytes((long)db_material.DiffuseColor));
            bw.Write(BitConverter.GetBytes((long)db_material.SpecularColor));
            bw.Write(BitConverter.GetBytes((long)db_material.ReflectionColor));
            bw.Write(BitConverter.GetBytes((long)db_material.IorColor));

            bw.Write(BitConverter.GetBytes((int)db_material.MaterialType));
            bw.Write(BitConverter.GetBytes((long)db_material.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_material.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков  в массив байт.
        /// </summary>
        /// <param name="db_material"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytes(XDBCatalogueMaterial db_material)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_material.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_material.Uniq));
            bw.Write(BitConverter.GetBytes((long)db_material.CatalogueUid));
            bw.Write(BitConverter.GetBytes((long)db_material.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_material.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков  в массив байт.
        /// </summary>
        /// <param name="db_material"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytesLTS(XDBCatalogueMaterial db_material)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_material.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_material.Uniq));
            bw.Write(BitConverter.GetBytes((long)db_material.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_material.DateDeleted));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_material"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueMaterial db_material)
        {
            if (db_material == null) return false;
            if (db_material.CatalogueUid != CatalogueUid) return false;
            if (db_material.DiffuseColor != DiffuseColor) return false;
            if (db_material.SpecularColor != SpecularColor) return false;
            if (db_material.ReflectionColor != ReflectionColor) return false;
            if (db_material.IorColor != IorColor) return false;
            if (db_material.MaterialType != MaterialType) return false;
            return true;
        }
    }
}