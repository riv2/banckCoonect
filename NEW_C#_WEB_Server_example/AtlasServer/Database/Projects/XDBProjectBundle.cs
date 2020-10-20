using System;
using System.IO;
using System.Text;
using System.Collections.Generic;

namespace Atlas.Database
{
    public enum XDBProjectType { CORPORATE, CUSTOM };
    public enum XDBCreatorType { INTERNAL, EXTERNAL };
    public enum XDBVersionStatus { NODATA, NONSYNC, SYNC, ERROR };
    public enum XDBRenderState { INCOMPLETE, COMPLETE };
    public enum XDBProjectUsageType { EDIT, HMD };

    /// <summary>
    /// Контейнер данных о проекте
    /// </summary>
    [Serializable]
    public class XDBProjectBundle
    {
        public XDBProject project;
        public XDBProjectInfo info;
        public XDBProjectVersion version_latest;
        public List<XDBProjectVersion> versions_list = null;

        public byte[] preview = null;

        public XDBProjectBundle(XDBProject project, XDBProjectInfo info, XDBProjectVersion version)
        {
            this.project = project;
            this.info = info;
            this.version_latest = version;
        }

        public XDBProjectBundle(XDBProject project, XDBProjectInfo info, XDBProjectVersion version, byte[] preview)
        {
            this.project = project;
            this.info = info;
            this.version_latest = version;
            this.preview = preview;
        }
    }

    /// <summary>
    /// Контейнер заголовка проекта
    /// </summary>
    [Serializable]
    public class XDBProjectHandle
    {
        public string GlobalUniq;
        public long CompanyUid;
        public long BranchUid;
        public long DateCreated;
        public long DateModified;

        public XDBProjectHandle()
        {
        }

        public XDBProjectHandle(XDBProject project)
        {
            GlobalUniq = project.GlobalUniq;
            CompanyUid = project.CompanyUid;
            BranchUid = project.BranchUid;
            DateCreated = project.DateCreated;
            DateModified = project.DateModified;
        }

        public static XDBProjectHandle FromBytes(byte[] bt_data)
        {
            XDBProjectHandle project_handle = new XDBProjectHandle();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                int len = br.ReadInt32();
                project_handle.GlobalUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                project_handle.CompanyUid = br.ReadInt32();
                project_handle.BranchUid = br.ReadInt32();
                project_handle.DateCreated = br.ReadInt64();
                project_handle.DateModified = br.ReadInt64();
            }
            catch (Exception ex)
            {
                project_handle = null;
            }

            br.Close();
            ms.Close();
            return project_handle;
        }

        public static byte[] ToBytes(XDBProjectHandle project_handle)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(project_handle.GlobalUniq)));
            bw.Write(Encoding.UTF8.GetBytes(project_handle.GlobalUniq));
            bw.Write(BitConverter.GetBytes((int)project_handle.CompanyUid));
            bw.Write(BitConverter.GetBytes((int)project_handle.BranchUid));
            bw.Write(BitConverter.GetBytes((long)project_handle.DateCreated));
            bw.Write(BitConverter.GetBytes((long)project_handle.DateModified));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}