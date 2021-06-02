
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Связывает выбор каталога с ключем публичного доступа
    /// </summary>
    [Serializable]
    public class XDBTokenCatalogue
    {
        /// <summary>
        /// Уникальный идентификатор
        /// </summary>
        [JsonProperty("uid")]
        public long Uid { set; get; }

        /// <summary>
        /// Уникальный идентификатор ключа, которому принадлежит выбор
        /// </summary>
        [JsonProperty("token_uid")]
        public long TokenUid { set; get; }

        /// <summary>
        /// Уникальный идентификатор каталога
        /// </summary>
        [JsonProperty("catalogue_uid")]
        public long CatalogueUid { set; get; }

        /// <summary>
        /// Уникальный идентификатор уровня иерархии
        /// </summary>
        [JsonProperty("hierarchy_uniq")]
        public string HierarchyUniq { set; get; }

        /// <summary>
        /// Путь, который описывает уровень иерархии
        /// </summary>
        [JsonProperty("path")]
        public string Path { set; get; }

        public XDBTokenCatalogue()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBTokenCatalogue FromBytes(byte[] bt_data)
        {
            XDBTokenCatalogue db_selection = new XDBTokenCatalogue();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_selection.Uid = br.ReadInt64();
                db_selection.TokenUid = br.ReadInt64();
                db_selection.CatalogueUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_selection.HierarchyUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_selection.Path = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                
                db_selection = null;
            }

            br.Close();
            ms.Close();
            return db_selection;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_selection"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBTokenCatalogue db_selection)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_selection.Uid));
            bw.Write(BitConverter.GetBytes((long)db_selection.TokenUid));
            bw.Write(BitConverter.GetBytes((long)db_selection.CatalogueUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_selection.HierarchyUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_selection.HierarchyUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_selection.Path)));
            bw.Write(Encoding.UTF8.GetBytes(db_selection.Path));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_selection"></param>
        /// <returns></returns>
        public bool CompareTo(XDBTokenCatalogue db_selection)
        {
            if (db_selection == null) return false;
            if (db_selection.TokenUid != TokenUid) return false;
            if (db_selection.CatalogueUid != CatalogueUid) return false;
            if (db_selection.HierarchyUniq != HierarchyUniq) return false;
            if (db_selection.Path != Path) return false;
            return true;
        }
    }
}
