using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Объект базы данных, описывающий сопроводительную и техническую информацию о проекте
    /// </summary>
    [Serializable]

    public class XDBProjectInfo
    {
        [JsonProperty("project_uniq")]
        public string ProjectUniq { set; get; }

        [JsonProperty("area")]
        public double Area { set; get; }

        [JsonProperty("rnjb_uniq")]
        public string RnjbUniq { set; get; }

        [JsonProperty("rnjb_state")]
        public int RnjbState { set; get; }

        [JsonProperty("webgl_uniq")]
        public string WebglUniq { set; get; }

        [JsonProperty("webgl_shortlink")]
        public string WebglShortlink { set; get; }

        [JsonProperty("webgl_email")]
        public string WebglEmail { set; get; }

        [JsonProperty("time_elapsed")]
        public double TimeElapsed { set; get; }

        [JsonProperty("date_rendered")]
        public long DateRendered { set; get; }

        public XDBProjectInfo()
        {
        }

        public XDBProjectInfo(XDBProjectInfo reference)
        {
            Area = reference.Area;
            RnjbUniq = reference.RnjbUniq;
            RnjbState = reference.RnjbState;
            WebglUniq = reference.WebglUniq;
            WebglShortlink = reference.WebglShortlink;
            WebglEmail = reference.WebglEmail;
            TimeElapsed = reference.TimeElapsed;
            DateRendered = reference.DateRendered;
        }

        public static XDBProjectInfo FromBytes(byte[] bt_data)
        {
            XDBProjectInfo project_info = new XDBProjectInfo();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                project_info.ProjectUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                project_info.Area = br.ReadSingle();

                len = br.ReadInt32();
                project_info.RnjbUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                project_info.RnjbState = br.ReadInt32();

                len = br.ReadInt32();
                project_info.WebglUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project_info.WebglShortlink = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project_info.WebglEmail = Encoding.UTF8.GetString(br.ReadBytes(len));

                project_info.TimeElapsed = br.ReadSingle();
                project_info.DateRendered = br.ReadInt64();
            }
            catch (Exception ex)
            {
                project_info = null;
            }

            br.Close();
            ms.Close();
            return project_info;
        }

        public static byte[] ToBytes(XDBProjectInfo project)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.ProjectUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project.ProjectUniq));

            bw.Write(BitConverter.GetBytes((float)project.Area));

            if (string.IsNullOrEmpty(project.RnjbUniq))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.RnjbUniq)));
                bw.Write(Encoding.UTF8.GetBytes(project.RnjbUniq));
            }

            bw.Write(BitConverter.GetBytes((int)project.RnjbState));

            if (string.IsNullOrEmpty(project.WebglUniq))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.WebglUniq)));
                bw.Write(Encoding.UTF8.GetBytes(project.WebglUniq));
            }

            if (string.IsNullOrEmpty(project.WebglShortlink))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.WebglShortlink)));
                bw.Write(Encoding.UTF8.GetBytes(project.WebglShortlink));
            }

            if (string.IsNullOrEmpty(project.WebglEmail))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.WebglEmail)));
                bw.Write(Encoding.UTF8.GetBytes(project.WebglEmail));
            }

            bw.Write(BitConverter.GetBytes((float)project.TimeElapsed));

            bw.Write(BitConverter.GetBytes((long)project.DateRendered));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}