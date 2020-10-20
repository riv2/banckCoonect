using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Объект базы данных, описывающий базовую информацию о проекте
    /// </summary>
    [Serializable]
    public class XDBServerJob
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("job_type")]
        public string JobType { set; get; }

        [JsonProperty("job_uniq")]
        public string JobUniq { set; get; }

        [JsonProperty("parent_uniq")]
        public string ParentUniq { set; get; }

        [JsonProperty("date_accept")]
        public long DateAccept { set; get; }

        [JsonProperty("date_start")]
        public long DateStart { set; get; }

        [JsonProperty("date_abort")]
        public long DateAbort { set; get; }

        [JsonProperty("date_done")]
        public long DateDone { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("member_name")]
        public string MemberName { set; get; }

        [JsonProperty("member_type")]
        public int MemberType { set; get; }

        [JsonProperty("progress")]
        public double Progress { set; get; }

        [JsonProperty("description")]
        public string Description { set; get; }

        public XDBServerJob()
        {
        }

        public static XDBServerJob FromBytes(byte[] bt_data)
        {
            XDBServerJob db_job = new XDBServerJob();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_job.Uid = br.ReadInt64();
                db_job.CompanyUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_job.JobType = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_job.JobUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_job.ParentUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_job.DateAccept = br.ReadInt64();
                db_job.DateStart = br.ReadInt64();
                db_job.DateAbort = br.ReadInt64();
                db_job.DateDone = br.ReadInt64();

                len = br.ReadInt32();
                db_job.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_job.MemberName = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_job.MemberType = br.ReadInt32();
                db_job.Progress = br.ReadDouble();

                len = br.ReadInt32();
                db_job.Description = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                db_job = null;
            }

            br.Close();
            ms.Close();
            return db_job;
        }

        public static byte[] ToBytes(XDBServerJob db_job)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_job.Uid));
            bw.Write(BitConverter.GetBytes((long)db_job.CompanyUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_job.JobType)));
            bw.Write(Encoding.UTF8.GetBytes(db_job.JobType));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_job.JobUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_job.JobUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_job.ParentUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_job.ParentUniq));

            bw.Write(BitConverter.GetBytes((long)db_job.DateAccept));
            bw.Write(BitConverter.GetBytes((long)db_job.DateStart));
            bw.Write(BitConverter.GetBytes((long)db_job.DateAbort));
            bw.Write(BitConverter.GetBytes((long)db_job.DateDone));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_job.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_job.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_job.MemberName)));
            bw.Write(Encoding.UTF8.GetBytes(db_job.MemberName));

            bw.Write(BitConverter.GetBytes((int)db_job.MemberType));
            bw.Write(BitConverter.GetBytes((double)db_job.Progress));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_job.Description)));
            bw.Write(Encoding.UTF8.GetBytes(db_job.Description));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}