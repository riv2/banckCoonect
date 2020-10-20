
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    [Serializable]
    public class XDBSubscribeMember
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("subscribe_uid"),]
        public long SubscribeUid { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("grantor_uniq")]
        public string GrantorUniq { set; get; }

        [JsonProperty("date_started")]
        public long DateStarted { set; get; }

        [JsonProperty("date_expires"),]
        public long DateExpires { set; get; }

        public XDBSubscribeMember()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBSubscribeMember FromBytes(byte[] bt_data)
        {
            XDBSubscribeMember db_subscribe_member = new XDBSubscribeMember();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_subscribe_member.Uid = br.ReadInt64();
                db_subscribe_member.SubscribeUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_subscribe_member.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_subscribe_member.GrantorUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_subscribe_member.DateStarted = br.ReadInt64();
                db_subscribe_member.DateExpires = br.ReadInt64();
            }
            catch (Exception ex)
            {
                
                db_subscribe_member = null;
            }

            br.Close();
            ms.Close();
            return db_subscribe_member;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_subscribe_member"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBSubscribeMember db_subscribe_member)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_subscribe_member.Uid));
            bw.Write(BitConverter.GetBytes((long)db_subscribe_member.SubscribeUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_subscribe_member.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_subscribe_member.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_subscribe_member.GrantorUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_subscribe_member.GrantorUniq));

            bw.Write(BitConverter.GetBytes((long)db_subscribe_member.DateStarted));
            bw.Write(BitConverter.GetBytes((long)db_subscribe_member.DateExpires));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_subscribe_member"></param>
        /// <returns></returns>
        public bool CompareTo(XDBSubscribeMember db_subscribe_member)
        {
            if (db_subscribe_member == null) return false;
            if (db_subscribe_member.Uid != Uid) return false;
            return true;
        }
    }
}
