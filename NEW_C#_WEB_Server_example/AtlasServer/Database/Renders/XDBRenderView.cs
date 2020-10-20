
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
    public class XDBRenderView
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("render_uid")]
        public long RenderUid { set; get; }

        [JsonProperty("view_duration")]
        public long ViewDuration { set; get; }

        [JsonProperty("date_start")]
        public long DateStart { set; get; }

        [JsonProperty("viewer_signature")]
        public string ViewerSignature { set; get; }

        public XDBRenderView()
        {
        }
    
        public static XDBRenderView FromBytes(byte[] bt_data)
        {
            XDBRenderView project = new XDBRenderView();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                project.Uid = br.ReadInt64();
                project.RenderUid = br.ReadInt64();
                project.ViewDuration = br.ReadInt64();
                project.DateStart = br.ReadInt64();

                int len = br.ReadInt32();
                project.ViewerSignature = Encoding.UTF8.GetString(br.ReadBytes(len));
            }
            catch (Exception ex)
            {
                project = null;
            }

            br.Close();
            ms.Close();
            return project;
        }

        public static byte[] ToBytes(XDBRenderView project)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)project.Uid));
            bw.Write(BitConverter.GetBytes((long)project.RenderUid));
            bw.Write(BitConverter.GetBytes((long)project.ViewDuration));
            bw.Write(BitConverter.GetBytes((long)project.DateStart));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.ViewerSignature)));
            bw.Write(Encoding.UTF8.GetBytes(project.ViewerSignature));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}

