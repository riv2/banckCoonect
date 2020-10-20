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

    public class XDBProject
    {
        [JsonProperty("global_uniq")]
        public string GlobalUniq { set; get; }

        [JsonProperty("project_name")]
        public string ProjectName { set; get; }

        [JsonProperty("project_uniq")]
        public string ProjectUniq { set; get; }

        [JsonProperty("project_version")]
        public string ProjectVersion { set; get; }

        [JsonProperty("project_type")]
        public int ProjectType { set; get; }

        [JsonProperty("customer_uniq")]
        public string CustomerUniq { set; get; }

        [JsonProperty("creator_type")]
        public int CreatorType { set; get; }

        [JsonProperty("creator_uniq")]
        public string CreatorUniq { set; get; }

        [JsonProperty("creator_name")]
        public string CreatorName { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("branch_uid")]
        public long BranchUid { set; get; }

        [JsonProperty("destination_fields")]
        public string DestinationFields { set; get; }

        [JsonProperty("date_created")]
        public long DateCreated { set; get; }

        [JsonProperty("date_modified")]
        public long DateModified { set; get; }

        public XDBProject()
        {
        }

        /// <summary>
        /// Воазвращает статус синхронизации информации о проекте
        /// </summary>
        /// <returns></returns>
        public bool IsSynchronized()
        {
            return ProjectVersion != "";
        }

        /// <summary>
        /// Возвращает статус сихнронизации актуальной версии проекта
        /// </summary>
        /// <returns></returns>
        public bool IsVersionSyncronized()
        {
            return ProjectUniq != "" && ProjectVersion != "";
        }

        public static XDBProject FromBytes(byte[] bt_data)
        {
            XDBProject project = new XDBProject();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                project.GlobalUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project.ProjectName = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project.ProjectUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project.ProjectVersion = Encoding.UTF8.GetString(br.ReadBytes(len));

                project.ProjectType = br.ReadInt32();

                len = br.ReadInt32();
                project.CustomerUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                project.CreatorType = br.ReadInt32();

                len = br.ReadInt32();
                project.CreatorUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project.CreatorName = Encoding.UTF8.GetString(br.ReadBytes(len));

                project.CompanyUid = br.ReadInt32();
                project.BranchUid = br.ReadInt32();

                len = br.ReadInt32();
                project.DestinationFields = Encoding.UTF8.GetString(br.ReadBytes(len));

                project.DateCreated = br.ReadInt64();
                project.DateModified = br.ReadInt64();
            }
            catch (Exception ex)
            {
                project = null;
            }

            br.Close();
            ms.Close();
            return project;
        }

        public static byte[] ToBytes(XDBProject project)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.GlobalUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project.GlobalUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.ProjectName)));
            bw.Write(Encoding.UTF8.GetBytes(project.ProjectName));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.ProjectUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project.ProjectUniq));

            if (string.IsNullOrEmpty(project.ProjectVersion))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.ProjectVersion)));
                bw.Write(Encoding.UTF8.GetBytes(project.ProjectVersion));
            }

            bw.Write(BitConverter.GetBytes((int)project.ProjectType));

            if (string.IsNullOrEmpty(project.CustomerUniq))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.CustomerUniq)));
                bw.Write(Encoding.UTF8.GetBytes(project.CustomerUniq));
            }

            bw.Write(BitConverter.GetBytes((int)project.CreatorType));

            if (string.IsNullOrEmpty(project.CreatorUniq))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.CreatorUniq)));
                bw.Write(Encoding.UTF8.GetBytes(project.CreatorUniq));
            }

            if (string.IsNullOrEmpty(project.CreatorName))
            {
                bw.Write(BitConverter.GetBytes((int)0));
            }
            else
            {
                bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.CreatorName)));
                bw.Write(Encoding.UTF8.GetBytes(project.CreatorName));
            }

            bw.Write(BitConverter.GetBytes((int)project.CompanyUid));

            bw.Write(BitConverter.GetBytes((int)project.BranchUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.DestinationFields)));
            bw.Write(Encoding.UTF8.GetBytes(project.DestinationFields));

            bw.Write(BitConverter.GetBytes((long)project.DateCreated));
            bw.Write(BitConverter.GetBytes((long)project.DateModified));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}