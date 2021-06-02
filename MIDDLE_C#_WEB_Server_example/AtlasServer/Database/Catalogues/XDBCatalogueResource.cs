using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    public class XDBResourceType
    {
        /// <summary>
        /// Thumbnail picture (icon)
        /// </summary>
        public const string TMB = "tmb";

        /// <summary>
        /// Texture picture
        /// </summary>
        public const string XTX = "xtx";

        /// <summary>
        /// Data material
        /// </summary>
        public const string XDM = "xdm";

        /// <summary>
        /// Data object
        /// </summary>
        public const string XDO = "xdo";

        /// <summary>
        /// Data object concent
        /// </summary>
        public const string DAT = "dat";
    }


    /// <summary>
    /// Организует доступ к таблице ресурсов, хранящей только базовую информацию о файлах
    /// </summary>
    [Serializable]
    public class XDBCatalogueResource
    {
        /// <summary>
        /// Уникальный идентификатор ресурса в рамках всех каталогов.
        /// Определяется совокупностью идентификаторов текущего каталога и идентификатором ресурса внутри него.
        /// </summary>
        [JsonProperty("uniq")]
        public string Uniq { set; get; }

        /// <summary>
        /// Имя файла источника ресурса
        /// </summary>
        [JsonProperty("path_source")]
        public string PathSource { set; get; }

        /// <summary>
        /// Контрольная сумма данных ресурса.
        /// Если значение изменилось, требуется повторная загрузка контента.
        /// Если пустой или NULL, значит ресурс еще не загружен.
        /// </summary>
        [JsonProperty("checksum")]
        public string Checksum { set; get; }

        /// <summary>
        /// Контрольная сумма данных ресурса.
        /// Если значение изменилось, требуется повторная загрузка контента.
        /// Если пустой или NULL, значит ресурс еще не загружен.
        /// </summary>
        [JsonProperty("resource_type")]
        public string ResourceType { set; get; }

        /// <summary>
        /// Размерность ресурса, для текстур это (ширина (shift_left x 32) + высота)
        /// </summary>
        [JsonProperty("size_factor")]
        public long SizeFactor { set; get; }

        /// <summary>
        /// Если значение изменилось, требуется обновление всех данных, кроме контента.
        /// Контент обновляется только при изменении ContentsChecksum.
        /// Если 0, значит ресурс еще не загружен.
        /// </summary>
        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        public XDBCatalogueResource()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueResource FromBytes(byte[] bt_data)
        {
            XDBCatalogueResource db_resource = new XDBCatalogueResource();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_resource.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_resource.PathSource = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_resource.Checksum = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_resource.SizeFactor = br.ReadInt64();
                db_resource.DateModified = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_resource = null;
            }

            br.Close();
            ms.Close();
            return db_resource;
        }

        /// <summary>
        /// Десериализует данные заголовков из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueResource HandlerFromBytesLTS(byte[] bt_data)
        {
            XDBCatalogueResource db_resource = new XDBCatalogueResource();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_resource.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_resource.DateModified = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_resource = null;
            }

            br.Close();
            ms.Close();
            return db_resource;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_resource"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogueResource db_resource)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_resource.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_resource.Uniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_resource.PathSource)));
            bw.Write(Encoding.UTF8.GetBytes(db_resource.PathSource));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_resource.Checksum)));
            bw.Write(Encoding.UTF8.GetBytes(db_resource.Checksum));

            bw.Write(BitConverter.GetBytes((long)db_resource.SizeFactor));
            bw.Write(BitConverter.GetBytes((long)db_resource.DateModified));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сериализует данные заголовков в массив байт.
        /// </summary>
        /// <param name="db_resource"></param>
        /// <returns></returns>
        public static byte[] HandlerToBytesLTS(XDBCatalogueResource db_resource)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_resource.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_resource.Uniq));
            bw.Write(BitConverter.GetBytes((long)db_resource.DateModified));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_resource"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogueResource db_resource)
        {
            if (db_resource == null) return false;
            if (db_resource.Uniq != Uniq) return false;
            if (db_resource.ResourceType != ResourceType) return false;
            if (db_resource.Checksum != Checksum) return false;
            if (db_resource.SizeFactor != SizeFactor) return false;
            if (db_resource.PathSource != PathSource) return false;
            return true;
        }
    }
}