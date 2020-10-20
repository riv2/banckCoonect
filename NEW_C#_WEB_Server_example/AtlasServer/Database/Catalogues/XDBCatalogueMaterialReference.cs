using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    public class XDBCatalogueMaterialReferenceType
    {
        public const int RESOURCE = 0;
    }

    public class XDBCatalogueMaterialReferenceCahnnel
    {
        public const int DIFFUSE = 0;
        public const int SPECULAR = 1;
        public const int REFLECTION = 2;
        public const int IOR = 3;
    }

    /// <summary>
    /// Связывает ресурсы с материалами
    /// Реализует механизм отношений: один материал - несколько ресурсов и один ресурс - несколько материалов.
    /// </summary>
    [Serializable]
    public class XDBCatalogueMaterialReference
    {
        /// <summary>
        /// Уникальный идентификатор

        public long Uid { set; get; }

        /// <summary>
        /// Уникальный идентификатор исходного материала - владельца связи
        /// </summary>
        [JsonProperty("material_uniq")]
        public string MaterialUniq { set; get; }

        /// <summary>
        /// Канал исходного материала, к которому относится данная связь
        /// </summary>
        [JsonProperty("material_channel")]
        public int MaterialChannel { set; get; }

        /// <summary>
        /// Уникальный идентификатор связываемого ресурса
        /// </summary>
        [JsonProperty("reference_uniq")]
        public string ReferenceUniq { set; get; }

        /// <summary>
        /// Тип связываемого ерсурса (0 - ресурс, 1 - другой материал)
        /// </summary>
        [JsonProperty("reference_type")]
        public int ReferenceType { set; get; }

        /// <summary>
        /// Тип связываемого ерсурса (0 - ресурс, 1 - другой материал)
        /// </summary>
        [JsonProperty("dim_x")]
        public double DimX { set; get; }

        /// <summary>
        /// Тип связываемого ерсурса (0 - ресурс, 1 - другой материал)
        /// </summary>
        [JsonProperty("dim_y")]
        public double DimY { set; get; }

        /// <summary>
        /// Тип связываемого ерсурса (0 - ресурс, 1 - другой материал)
        /// </summary>
        [JsonProperty("offset_x")]
        public double OffsetX { set; get; }

        /// <summary>
        /// Тип связываемого ерсурса (0 - ресурс, 1 - другой материал)
        /// </summary>
        [JsonProperty("offset_y")]
        public double OffsetY { set; get; }

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

        public XDBCatalogueMaterialReference()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogueMaterialReference FromBytes(byte[] bt_data)
        {
            XDBCatalogueMaterialReference db_reference = new XDBCatalogueMaterialReference();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_reference.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_reference.MaterialUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_reference.MaterialChannel = br.ReadInt32();

                len = br.ReadInt32();
                db_reference.ReferenceUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
                db_reference.ReferenceType = br.ReadInt32();

                db_reference.DimX = br.ReadDouble();
                db_reference.DimY = br.ReadDouble();
                db_reference.OffsetX = br.ReadDouble();
                db_reference.OffsetY = br.ReadDouble();
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
        public static XDBCatalogueMaterialReference HandlerFromBytesLTS(byte[] bt_data)
        {
            XDBCatalogueMaterialReference db_reference = new XDBCatalogueMaterialReference();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_reference.Uid = br.ReadInt64();
                int len = br.ReadInt32();
                db_reference.MaterialUniq = Encoding.UTF8.GetString(br.ReadBytes(len));
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
        public static byte[] ToBytes(XDBCatalogueMaterialReference db_reference)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_reference.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.MaterialUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.MaterialUniq));
            bw.Write(BitConverter.GetBytes((int)db_reference.MaterialChannel));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.ReferenceUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.ReferenceUniq));
            bw.Write(BitConverter.GetBytes((int)db_reference.ReferenceType));

            bw.Write(BitConverter.GetBytes((double)db_reference.DimX));
            bw.Write(BitConverter.GetBytes((double)db_reference.DimY));
            bw.Write(BitConverter.GetBytes((double)db_reference.OffsetX));
            bw.Write(BitConverter.GetBytes((double)db_reference.OffsetY));
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
        public static byte[] HandlerToBytesLTS(XDBCatalogueMaterialReference db_reference)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_reference.Uid));
            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_reference.MaterialUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_reference.MaterialUniq));
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
        public bool CompareTo(XDBCatalogueMaterialReference db_reference)
        {
            if (db_reference == null) return false;
            if (db_reference.MaterialChannel != MaterialChannel) return false;
            if (db_reference.DimX != DimX) return false;
            if (db_reference.DimY != DimY) return false;
            if (db_reference.OffsetX != OffsetX) return false;
            if (db_reference.OffsetY != OffsetY) return false;
            if (db_reference.ReferenceType != ReferenceType) return false;
            if (db_reference.MaterialUniq != MaterialUniq) return false;
            if (db_reference.ReferenceUniq != ReferenceUniq) return false;
            return true;
        }
    }
}