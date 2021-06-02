
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Связывает выбранный каталог с пользователем
    /// </summary>
    [Serializable]
    public class XDBMemberCatalogue
    {
        /// <summary>
        /// Уникальный идентификатор
        /// </summary>
        [JsonProperty("uid")]
        public long Uid { set; get; }

        /// <summary>
        /// Уникальный идентификатор пользователя, которому принадлежит выбор
        /// </summary>
        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

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

        public XDBMemberCatalogue()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBMemberCatalogue FromBytes(byte[] bt_data)
        {
            XDBMemberCatalogue db_selection = new XDBMemberCatalogue();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_selection.Uid = br.ReadInt64();

                int len = br.ReadInt32();
                db_selection.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_selection.CatalogueUid = br.ReadInt64();

                len = br.ReadInt32();
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
        public static byte[] ToBytes(XDBMemberCatalogue db_selection)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_selection.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_selection.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_selection.MemberUniq));

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
        public bool CompareTo(XDBMemberCatalogue db_selection)
        {
            if (db_selection == null) return false;
            if (db_selection.MemberUniq != MemberUniq) return false;
            if (db_selection.CatalogueUid != CatalogueUid) return false;
            if (db_selection.HierarchyUniq != HierarchyUniq) return false;
            if (db_selection.Path != Path) return false;
            return true;
        }
    }
}
