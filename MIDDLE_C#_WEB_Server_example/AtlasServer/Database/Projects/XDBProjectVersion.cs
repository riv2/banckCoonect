using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Объект базы данных, описывающий информацию о версии проекта
    /// </summary>
    [Serializable]

    public class XDBProjectVersion
    {
        [JsonProperty("global_uniq")]
        public string GlobalUniq { set; get; }

        [JsonProperty("project_uniq")]
        public string ProjectUniq { set; get; }

        [JsonProperty("version_uniq")]
        public string VersionUniq { set; get; }

        [JsonProperty("software_version")]
        public int SoftwareVersion { set; get; }

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

        [JsonProperty("sync_status")]
        public int SyncStatus { set; get; }

        [JsonProperty("date_created")]
        public long DateCreated { set; get; }

        public XDBProjectVersion()
        {
        }

        public static XDBProjectVersion FromBytes(byte[] bt_data)
        {
            XDBProjectVersion project_version = new XDBProjectVersion();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                project_version.GlobalUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project_version.ProjectUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project_version.VersionUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                project_version.SoftwareVersion = br.ReadInt32();
                project_version.CreatorType = br.ReadInt32();

                len = br.ReadInt32();
                project_version.CreatorUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                len = br.ReadInt32();
                project_version.CreatorName = Encoding.UTF8.GetString(br.ReadBytes(len));

                project_version.SyncStatus = br.ReadInt32();
                project_version.CompanyUid = br.ReadInt64();
                project_version.BranchUid = br.ReadInt64();
                project_version.DateCreated = br.ReadInt64();
            }
            catch (Exception ex)
            {
                project_version = null;
            }

            br.Close();
            ms.Close();
            return project_version;
        }

        public static byte[] ToBytes(XDBProjectVersion project)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.GlobalUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project.GlobalUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.ProjectUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project.ProjectUniq));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project.VersionUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project.VersionUniq));

            bw.Write(BitConverter.GetBytes((int)project.SoftwareVersion));

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

            bw.Write(BitConverter.GetBytes((int)project.SyncStatus));
            bw.Write(BitConverter.GetBytes((long)project.CompanyUid));
            bw.Write(BitConverter.GetBytes((long)project.BranchUid));
            bw.Write(BitConverter.GetBytes((long)project.DateCreated));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}