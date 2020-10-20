
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBSubscribe
    {
        /// <summary>
        /// Подписка по умолчниаю, 2 - FREE
        /// </summary>
        public const long UID_DEFAULT = 2;

        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("price")]
        public double Price { set; get; }

        public XDBSubscribe()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBSubscribe FromBytes(byte[] bt_data)
        {
            XDBSubscribe db_group = new XDBSubscribe();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_group.Uid = br.ReadInt32();

                int len = br.ReadInt32();
                db_group.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_group.Price = br.ReadDouble();
            }
            catch (Exception ex)
            {
                
                db_group = null;
            }

            br.Close();
            ms.Close();
            return db_group;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_group"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBSubscribe db_group)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)db_group.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_group.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_group.Name));

            bw.Write(BitConverter.GetBytes((double)db_group.Price));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_group"></param>
        /// <returns></returns>
        public bool CompareTo(XDBSubscribe db_group)
        {
            if (db_group == null) return false;
            if (db_group.Uid != Uid) return false;
            return true;
        }
    }
}
