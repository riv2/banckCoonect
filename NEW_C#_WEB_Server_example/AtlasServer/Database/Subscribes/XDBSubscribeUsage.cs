
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBSubscribeUsage
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("option_uid")]
        public long OptionUid { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("amount")]
        public double Amount { set; get; }

        [JsonProperty("date_refreshed")]
        public long DateRefreshed { set; get; }

        public XDBSubscribeUsage()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBSubscribeUsage FromBytes(byte[] bt_data)
        {
            XDBSubscribeUsage bt_usasge = new XDBSubscribeUsage();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                bt_usasge.Uid = br.ReadInt64();
                bt_usasge.OptionUid = br.ReadInt64();

                int len = br.ReadInt32();
                bt_usasge.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                bt_usasge.Amount = br.ReadDouble();
                bt_usasge.DateRefreshed = br.ReadInt64();
            }
            catch (Exception ex)
            {
                
                bt_usasge = null;
            }

            br.Close();
            ms.Close();
            return bt_usasge;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="bt_usage"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBSubscribeUsage bt_usage)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)bt_usage.Uid));
            bw.Write(BitConverter.GetBytes((long)bt_usage.OptionUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(bt_usage.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(bt_usage.MemberUniq));

            bw.Write(BitConverter.GetBytes((double)bt_usage.Amount));
            bw.Write(BitConverter.GetBytes((long)bt_usage.DateRefreshed));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="bt_usage"></param>
        /// <returns></returns>
        public bool CompareTo(XDBSubscribeUsage bt_usage)
        {
            if (bt_usage == null) return false;
            if (bt_usage.Uid != Uid) return false;
            return true;
        }
    }
}
