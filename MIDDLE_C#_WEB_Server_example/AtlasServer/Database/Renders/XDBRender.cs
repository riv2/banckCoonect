
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

    public class XDBRender
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("job_uid")]
        public long JobUid { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("project_uniq")]
        public string ProjectUniq { set; get; }

        [JsonProperty("version_uniq")]
        public string VersionUniq { set; get; }

        [JsonProperty("render_type")]
        public string RenderType { set; get; }

        [JsonProperty("render_quality")]
        public int RenderQuality { set; get; }

        [JsonProperty("render_value")]
        public double RenderValue { set; get; }

        [JsonProperty("member_uniq")]
        public string MemberUniq { set; get; }

        [JsonProperty("member_name")]
        public string MemberName { set; get; }

        [JsonProperty("member_type")]
        public int MemberType { set; get; }

        public XDBRender()
        {
        }

        public static XDBRender FromBytes(byte[] bt_data)
        {
            XDBRender db_render = new XDBRender();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_render.Uid = br.ReadInt64();
                db_render.JobUid = br.ReadInt64();
                db_render.CompanyUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_render.ProjectUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_render.VersionUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_render.RenderType = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_render.RenderQuality = br.ReadInt32();
                db_render.RenderValue = br.ReadDouble();

                len = br.ReadInt32();
                db_render.MemberUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                db_render.MemberName = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_render.MemberType = br.ReadInt32();
            }
            catch (Exception ex)
            {
                db_render = null;
            }

            br.Close();
            ms.Close();
            return db_render;
        }

        public static byte[] ToBytes(XDBRender db_render)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_render.Uid));
            bw.Write(BitConverter.GetBytes((long)db_render.JobUid));
            bw.Write(BitConverter.GetBytes((long)db_render.CompanyUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_render.ProjectUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_render.ProjectUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_render.VersionUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_render.VersionUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_render.RenderType)));
            bw.Write(Encoding.UTF8.GetBytes(db_render.RenderType));

            bw.Write(BitConverter.GetBytes((int)db_render.RenderQuality));
            bw.Write(BitConverter.GetBytes((double)db_render.RenderValue));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_render.MemberUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_render.MemberUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_render.MemberName)));
            bw.Write(Encoding.UTF8.GetBytes(db_render.MemberName));

            bw.Write(BitConverter.GetBytes((int)db_render.MemberType));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}

