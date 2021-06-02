
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Профиль пользователя системы,
    /// содержит информацию, характеризующую пользователя
    /// </summary>
    [Serializable]
    public class XDBMemberProfile
    {
        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("first_name")]
        public string FirstName { set; get; }

        [JsonProperty("last_name")]
        public string LastName { set; get; }

        [JsonProperty("last_logged_in")]
        public long LastLoggedIn { set; get; }

        [JsonProperty("registered")]
        public long Registered { set; get; }

        /// <summary>
        /// Возвращает полное имя пользователя. 
        /// Если не указано, вернут его Uniq
        /// </summary>
        public string FullName { get { return GetFullName(); } }

        public XDBMemberProfile()
        {
        }

        private string GetFullName()
        {
            if (string.IsNullOrEmpty(FirstName))
            {
                return string.IsNullOrEmpty(LastName) ? MemberUniq : LastName;
            }
            else
            {
                return string.IsNullOrEmpty(LastName) ? FirstName : FirstName + " " + LastName;
            }
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBMemberProfile FromBytes(byte[] bt_data)
        {
            XDBMemberProfile db_profile = new XDBMemberProfile();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_profile.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_profile.FirstName = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_profile.LastName = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_profile.LastLoggedIn = br.ReadInt64();
                db_profile.Registered = br.ReadInt64();
            }
            catch (Exception ex)
            {
                
                db_profile = null;
            }

            br.Close();
            ms.Close();
            return db_profile;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_profile"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBMemberProfile db_profile)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_profile.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_profile.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_profile.FirstName)));
            bw.Write(Encoding.UTF8.GetBytes(db_profile.FirstName));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_profile.LastName)));
            bw.Write(Encoding.UTF8.GetBytes(db_profile.LastName));

            bw.Write(BitConverter.GetBytes((long)db_profile.LastLoggedIn));
            bw.Write(BitConverter.GetBytes((long)db_profile.Registered));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_profile"></param>
        /// <returns></returns>
        public bool CompareTo(XDBMemberProfile db_profile)
        {
            if (db_profile == null) return false;
            if (db_profile.MemberUniq != MemberUniq) return false;
            return true;
        }
    }
}
