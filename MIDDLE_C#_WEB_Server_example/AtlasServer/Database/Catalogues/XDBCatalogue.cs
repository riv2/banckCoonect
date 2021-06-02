using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Единицы подсчета
    /// </summary>
    public class XDBCatalogueUnits
    {
        /// <summary>
        /// Поштучно (PCS сокращено от pieces)
        /// </summary>
        public const int PCS = 1;

        /// <summary>
        /// Метры квадратные
        /// </summary>
        public const int M2 = 2;

        /// <summary>
        /// Литры 
        /// </summary>
        public const int LT = 3;

        /// <summary>
        /// Погонные метры
        /// </summary>
        public const int M = 4;
    }

    /// <summary>
    /// Тип подсчета
    /// </summary>
    public class XDBCatalogueCalculationType
    {
        /// <summary>
        /// Подсчет с подрезкой по заводскому углу
        /// </summary>
        public const int CORNER = 0;

        /// <summary>
        /// Подсчет с подрезкой ориенторованного рисунка
        /// </summary>
        public const int DECOR = 1;

        /// <summary>
        /// Подсчет по площади
        /// </summary>
        public const int MOSAIC = 2;

        /// <summary>
        /// Подсчет по рулонам 
        /// </summary>
        public const int ROLLS = 3;

        /// <summary>
        /// Подсчет по расходу красок
        /// </summary>
        public const int PAINTS = 4;

        /// <summary>
        /// Подсчет по штучно
        /// </summary>
        public const int BODIES = 5;
    }

    /// <summary>
    /// Тип валюты
    /// </summary>
    public class XDBCatalogueCurrency
    {
        /// <summary>
        /// Системный материал, обозначает что не для продажи
        /// </summary>
        public const string SYSTEM = "---";

        /// <summary>
        /// Рубли
        /// </summary>
        public const string RUB = "RUB";

        /// <summary>
        /// Доллары
        /// </summary>
        public const string USD = "USD";

        /// <summary>
        /// Евро
        /// </summary>
        public const string EUR = "EUR";
    }

    [Serializable]
    public class XDBCatalogue
    {
        public const int ONLY_ME = 0;
	    public const int MY_COMPANY = 1;
	    public const int MY_PARTNERS = 2;
	    public const int EVERYONE = 3;
	    public const int DEFAULT = 4;


        [JsonProperty("uid")]

        public long Uid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("owner")]
        public string Owner { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("type")]
        public string Type { set; get; }

        [JsonProperty("access"),]
        public int Access { set; get; }

        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        [JsonProperty("date_created")]
        public long DateCreated { set; get; }

        public XDBCatalogue()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBCatalogue FromBytes(byte[] bt_data)
        {
            XDBCatalogue db_catalogue = new XDBCatalogue();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_catalogue.Uid = br.ReadInt64();
                //db_catalogue.CompanyUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_catalogue.Owner = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_catalogue.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_catalogue.Type = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_catalogue.Access = br.ReadInt32();
                db_catalogue.DateModified = br.ReadInt64();
                db_catalogue.DateCreated = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_catalogue = null;
            }

            br.Close();
            ms.Close();
            return db_catalogue;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_catalogue"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBCatalogue db_catalogue)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_catalogue.Uid));
            //bw.Write(BitConverter.GetBytes((long)db_catalogue.CompanyUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_catalogue.Owner)));
            bw.Write(Encoding.UTF8.GetBytes(db_catalogue.Owner));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_catalogue.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_catalogue.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_catalogue.Type)));
            bw.Write(Encoding.UTF8.GetBytes(db_catalogue.Type));

            bw.Write(BitConverter.GetBytes((int)db_catalogue.Access));
            bw.Write(BitConverter.GetBytes((long)db_catalogue.DateModified));
            bw.Write(BitConverter.GetBytes((long)db_catalogue.DateCreated));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
        
        /// <summary>
                 /// Десериализует данные из массива байт.
                 /// </summary>
                 /// <param name="bt_data"></param>
                 /// <returns></returns>
        public static XDBCatalogue FromBytesLTS(byte[] bt_data)
        {
            XDBCatalogue db_catalogue = new XDBCatalogue();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_catalogue.Uid = br.ReadInt64();
                //db_catalogue.CompanyUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_catalogue.Owner = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_catalogue.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_catalogue.Type = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_catalogue.Access = br.ReadInt32();
                db_catalogue.DateCreated = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_catalogue = null;
            }

            br.Close();
            ms.Close();
            return db_catalogue;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_catalogue"></param>
        /// <returns></returns>
        public static byte[] ToBytesLTS(XDBCatalogue db_catalogue)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_catalogue.Uid));
            //bw.Write(BitConverter.GetBytes((long)db_catalogue.CompanyUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_catalogue.Owner)));
            bw.Write(Encoding.UTF8.GetBytes(db_catalogue.Owner));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_catalogue.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_catalogue.Name));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_catalogue.Type)));
            bw.Write(Encoding.UTF8.GetBytes(db_catalogue.Type));

            bw.Write(BitConverter.GetBytes((int)db_catalogue.Access));
            bw.Write(BitConverter.GetBytes((long)db_catalogue.DateCreated));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_catalogue"></param>
        /// <returns></returns>
        public bool CompareTo(XDBCatalogue db_catalogue)
        {
            if (db_catalogue == null) return false;
            if (db_catalogue.Uid != Uid) return false;
            return true;
        }
    }
}


        
