
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]

    public class XDBSubscribeOption
    {
        /// <summary>
        /// Остаток "монет" на визаулизацию
        /// </summary>
        public const string PROPERTY_COINS_AMOUNT = "PROPERTY_COINS_AMOUNT";

        /// <summary>
        /// Доступ к калькулятору
        /// </summary>
        public const string FEATURE_CALCULATOR = "FEATURE_CALCULATOR";

        /// <summary>
        /// Доступ к раскладчику плитки
        /// </summary>
        public const string FEATURE_PATTERNS = "FEATURE_PATTERNS";

        /// <summary>
        /// Доступ к функции печати раскладки
        /// </summary>
        public const string FEATURE_PRINTING = "FEATURE_PRINTING";

        /// <summary>
        /// Доступ к визуализации для VR очков
        /// </summary>
        public const string FEATURE_RENDER_VR = "FEATURE_RENDER_VR";

        /// <summary>
        /// Доступ к визуализации панорам-туров
        /// </summary>
        public const string FEATURE_RENDER_TOURS = "FEATURE_RENDER_TOURS";

        /// <summary>
        /// Доступ к отправке панорам по email
        /// </summary>
        public const string FEATURE_RENDER_MAILING = "FEATURE_RENDER_MAILING";

        /// <summary>
        /// Доступ к онлайн редактору
        /// </summary>
        public const string FEATURE_ONLINE_EDITOR = "FEATURE_ONLINE_EDITOR";

        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("subscribe_uid")]
        public long SubscribeUid { set; get; }

        [JsonProperty("code")]
        public string Code { set; get; }

        [JsonProperty("name")]
        public string Name { set; get; }

        [JsonProperty("limitation")]
        public double Limitation { set; get; }

        [JsonProperty("refresh_period")]
        public long RefreshPeriod { set; get; }

        public XDBSubscribeOption()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_option"></param>
        /// <returns></returns>
        public static XDBSubscribeOption FromBytes(byte[] bt_option)
        {
            XDBSubscribeOption db_group = new XDBSubscribeOption();
            MemoryStream ms = new MemoryStream(bt_option);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_group.Uid = br.ReadInt64();
                db_group.SubscribeUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_group.Code = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_group.Name = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_group.Limitation = br.ReadDouble();
                db_group.RefreshPeriod = br.ReadInt64();
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
        /// <param name="db_option"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBSubscribeOption db_option)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_option.Uid));
            bw.Write(BitConverter.GetBytes((long)db_option.SubscribeUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_option.Code)));
            bw.Write(Encoding.UTF8.GetBytes(db_option.Code));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_option.Name)));
            bw.Write(Encoding.UTF8.GetBytes(db_option.Name));

            bw.Write(BitConverter.GetBytes((double)db_option.Limitation));
            bw.Write(BitConverter.GetBytes((long)db_option.RefreshPeriod));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_option"></param>
        /// <returns></returns>
        public bool CompareTo(XDBSubscribeOption db_option)
        {
            if (db_option == null) return false;
            if (db_option.Uid != Uid) return false;
            return true;
        }
    }
}
