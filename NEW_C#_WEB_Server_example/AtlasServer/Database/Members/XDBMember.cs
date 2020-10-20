
using System;
using System.IO;
using System.Text;
using System.Linq;
using System.Collections.Generic;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Пользователя системы,
    /// содержит базовую информацию о данных авторизации
    /// </summary>
    [Serializable]

    public class XDBMember
    {
        /// <summary>
        /// Значение группы пдоступа по-умолчнию (5 - Частное лицо)
        /// </summary>
        public const int DEFAULT_GROUP = 5;

        [JsonProperty("uniq")]
        public string Uniq { set; get; }

        [JsonProperty("email")]
        public string Email { set; get; }

        [JsonProperty("password_salt")]
        public string PasswordSalt { set; get; }

        [JsonProperty("password")]
        public string Password { set; get; }

        [JsonProperty("access_group")]
        public int AccessGroup { set; get; }

        [JsonProperty("activation_key")]
        public string ActivationKey { set; get; }

        [JsonProperty("date_activate")]
        public long DateActivated { set; get; }

        public XDBMember()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBMember FromBytes(byte[] bt_data)
        {
            XDBMember db_member = new XDBMember();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                db_member.Uniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_member.Email = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_member.PasswordSalt = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_member.Password = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_member.AccessGroup = br.ReadInt32();

                len = br.ReadInt32();
                db_member.ActivationKey = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_member.DateActivated = br.ReadInt64();
            }
            catch (Exception ex)
            {
                
                db_member = null;
            }

            br.Close();
            ms.Close();
            return db_member;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_member"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBMember db_member)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_member.Uniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_member.Uniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_member.Email)));
            bw.Write(Encoding.UTF8.GetBytes(db_member.Email));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_member.PasswordSalt)));
            bw.Write(Encoding.UTF8.GetBytes(db_member.PasswordSalt));


            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_member.Password)));
            bw.Write(Encoding.UTF8.GetBytes(db_member.Password));

            bw.Write(BitConverter.GetBytes((int)db_member.AccessGroup));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_member.PasswordSalt)));
            bw.Write(Encoding.UTF8.GetBytes(db_member.PasswordSalt));

            bw.Write(BitConverter.GetBytes((long)db_member.DateActivated));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_member"></param>
        /// <returns></returns>
        public bool CompareTo(XDBMember db_member)
        {
            if (db_member == null) return false;
            if (db_member.Uniq != Uniq) return false;
            return true;
        }
    }

    /*

    public class XDBMemberManager
    {
        public static XDBMember FindMember(XDatabaseAdapter connection, string uniq_or_email)
        {
            List<XDBMember> memberList = connection.Database.Query<XDBMember>(
                "SELECT * FROM _members WHERE email='{0}' OR uniq='{0}' LIMIT 1",
                uniq_or_email
            ).ToList();
            return memberList.Count > 0 ? memberList[0] : null;
        }

        public static XDBMember Create(XDatabaseAdapter connection, string email, string password, int access_group)
        {
            XDBMember member = FindMember(connection, email);
            if (member == null)
            {
                member = new XDBMember();
                try
                {
                    member.Uniq = GenerateMemberUniq(connection);
                    member.Email = email;
                    member.PasswordSalt = GeneratePasswordSalt();
                    member.Password = Hash.Sha256(member.PasswordSalt + member.Uniq + password);
                    member.AccessGroup = access_group;
                    connection.Insert(member);
                }
                catch (Exception ex)
                {
                    
                    return null;
                }
            }
            return member;
        }

        public static bool IsPasswordCorrect(XDBMember member, string password)
        {
            return member.Password.Equals(Hash.Sha256(member.PasswordSalt + member.Uniq + password));
        }

        public static bool GenerateActivationKey(XDatabaseAdapter context, XDBMember member)
        {
            member.ActivationKey = Hash.Md5(member.PasswordSalt + member.Email + XUtils.GetUnixTimestamp());
            member.DateActivated = 0;
            try
            {
                context.Update(member);
            }
            catch (Exception ex)
            {
                
                return false;
            }
            return true;
        }

        public static bool GenerateRestorationKey(XDatabaseAdapter context, XDBMember member)
        {
            member.ActivationKey = Hash.Md5(member.PasswordSalt + member.Email + XUtils.GetUnixTimestamp());
            try
            {
                context.Update(member);
            }
            catch (Exception ex)
            {
                
                return false;
            }
            return true;
        }

        public static string GenerateMemberUniq(XDatabaseAdapter connection)
        {
            int tries = 100;
            string uniq = XU.GetRandomUniq(3).ToUpper();
            XDBMember member = connection.Database.Table<XDBMember>().Where(x => x.Uniq == uniq).FirstOrDefault();
            while (member != null && tries > 0)
            {
                uniq = XU.GetRandomUniq(3).ToUpper();
                member = connection.Database.Table<XDBMember>().Where(x => x.Uniq == uniq).FirstOrDefault();
                tries--;
            }

            if (tries == 0)
            {
                throw new Exception("Limit of tries exceed!");
            }
            return uniq;
        }

        public static string GeneratePasswordSalt()
        {
            return Hash.Sha256(XU.GetRandomString(64));
        }
    }

    */
}
