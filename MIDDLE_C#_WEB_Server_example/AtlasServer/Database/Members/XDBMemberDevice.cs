
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;


namespace Atlas.Database
{
    /// <summary>
    /// Устройство запуска программного комплекса.
    /// Связывает устрйоство с определенной учетной записью пользователя
    /// </summary>
    [Serializable]
    public class XDBMemberDevice
    {
        [JsonProperty("uid")]
        public int Uid { set; get; }

        [JsonProperty("device_uniq")]
        public string DeviceUniq { set; get; }

        [JsonProperty("device_name")]
        public string DeviceName { set; get; }

        [JsonProperty("ip_address")]
        public string IpAddress { set; get; }

        [JsonProperty("version")]
        public string Version { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("date_registered")]
        public long DateRegistered { set; get; }

        [JsonProperty("date_updated")]
        public long DateUpdated { set; get; }

        public XDBMemberDevice()
        {
        }

        /// <summary>
        /// Десериализует данные из массива байт.
        /// </summary>
        /// <param name="bt_data"></param>
        /// <returns></returns>
        public static XDBMemberDevice FromBytes(byte[] bt_data)
        {
            XDBMemberDevice db_device = new XDBMemberDevice();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_device.Uid = br.ReadInt32();

                int len = br.ReadInt32();
                db_device.DeviceUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_device.DeviceName = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_device.IpAddress = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_device.Version = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_device.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_device.DateRegistered = br.ReadInt64();
                db_device.DateUpdated = br.ReadInt64();
            }
            catch (Exception ex)
            {
                
                db_device = null;
            }

            br.Close();
            ms.Close();
            return db_device;
        }

        /// <summary>
        /// Сериализует данные в массив байт.
        /// </summary>
        /// <param name="db_device"></param>
        /// <returns></returns>
        public static byte[] ToBytes(XDBMemberDevice db_device)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)db_device.Uid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_device.DeviceUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_device.DeviceUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_device.DeviceName)));
            bw.Write(Encoding.UTF8.GetBytes(db_device.DeviceName));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_device.IpAddress)));
            bw.Write(Encoding.UTF8.GetBytes(db_device.IpAddress));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_device.Version)));
            bw.Write(Encoding.UTF8.GetBytes(db_device.Version));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_device.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_device.MemberUniq));

            bw.Write(BitConverter.GetBytes((long)db_device.DateRegistered));
            bw.Write(BitConverter.GetBytes((long)db_device.DateUpdated));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }

        /// <summary>
        /// Сравнивает два экземпляра класса
        /// </summary>
        /// <param name="db_device"></param>
        /// <returns></returns>
        public bool CompareTo(XDBMemberDevice db_device)
        {
            if (db_device == null) return false;
            if (db_device.DeviceUniq != DeviceUniq) return false;
            if (db_device.MemberUniq != MemberUniq) return false;
            return true;
        }
    }
}
