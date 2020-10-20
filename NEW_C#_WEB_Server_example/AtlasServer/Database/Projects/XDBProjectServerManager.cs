/*

using System;
using System.IO;
using System.Linq;
using System.Threading;
using System.Collections.Generic;
using Atlas.Database;


/// <summary>
/// Файл содержит реализацию работы с базой данных проектов серверной части.
/// Доступен только в серверном коде.
/// </summary>

namespace Atlas
{
    public class XDBProjectServerManager
    {
        private class ImportInfo
        {
            public string project_path;
            public string project_uniq;
            public string corporate_path;
            public int corporate_uniq;
        }

        private class ImportWebgl
        {
            public long date_created;
            public string webgl_uniq;
            public string webgl_shortlink;
        }

        private class ImportStatus
        {
            public string latest;
            public string webgl_uniq;
            public string rnjb_status;
        }

        private class ImportRender
        {
            public long date_complete;
            public string version;
        }

        private static string projects_path;
        private static int projects_total;
        private static int projects_done;
        private static int projects_imported;
        private static Thread thread;

        /* Obsolete
        public static void ImportProjectsDatabase(string projects_root_path)
        {
            if (Directory.Exists(projects_root_path))
            {
                projects_path = projects_root_path;
                thread = new Thread(CacheProjectsDataThread);
                thread.Start();
            }
        }

        public static bool GetImportStatus()
        {
            return thread != null;
        }

        public static int GetImportProgress()
        {
            if(thread != null)
            {
                if (projects_total > 0)
                    return (int)System.Math.Floor((projects_done / (float)projects_total) * 100);

                return 0;
            }
            return 1;
        }

        public static int GetImportTotal()
        {
            return projects_total;
        }

        public static int GetImportDone()
        {
            return projects_done;
        }

        /* Obsolete
        private static void CacheProjectsDataThread()
        {
            List<ImportInfo> import_list = new List<ImportInfo>();

            DirectoryInfo dir_root = new DirectoryInfo(projects_path);
            foreach (DirectoryInfo dir_corporate in dir_root.GetDirectories())
            {
                foreach (DirectoryInfo dir_corporate_data in dir_corporate.GetDirectories())
                {
                    if (dir_corporate_data.Name == "Projects")
                    {
                        foreach (DirectoryInfo dir_proj in dir_corporate_data.GetDirectories())
                        {
                            ImportInfo info = new ImportInfo();
                            info.project_uniq = dir_proj.Name;
                            info.project_path = dir_proj.FullName;
                            info.corporate_uniq = int.Parse(dir_corporate.Name, NumberStyles.Any, CultureInfo.InvariantCulture);
                            info.corporate_path = dir_proj.FullName;
                            import_list.Add(info);

                            if (import_list.Count > 0 && import_list.Count % 100 == 0)
                                Thread.Sleep(100);
                        }
                    }
                }
            }

            projects_total = import_list.Count;
            projects_imported = 0;
            foreach (ImportInfo info in import_list)
            {
                XDBProject atlas_project = connection.Database.Table<XDBProject>().Where(
                    x => x.OrganizationUniq == info.corporate_uniq && x.ProjectUniq == info.project_uniq
                ).FirstOrDefault();

                if (atlas_project == null)
                {
                    string global_uniq = Hash.Sha256(info.corporate_uniq + "#" + info.project_uniq.ToLower());
                    byte[] proj_info_data;

                    DirectoryInfo dir = new DirectoryInfo(info.project_path);
                    DirectoryInfo[] dir_versions_list = dir.GetDirectories();
                    ImportStatus project_status = null;
                    ImportRender project_render = null;
                    List<ImportWebgl> webgl_list = new List<ImportWebgl>();
                    List<XDBProjectUsage> project_usages_list = new List<XDBProjectUsage>();
                    List<XDBProjectVersion> project_versions_list = new List<XDBProjectVersion>();

                    foreach (DirectoryInfo dir_contents in dir_versions_list)
                    {
                        if (dir_contents.Name.ToLower().StartsWith("version_"))
                        {
                            if (FileManager.IsFileAwailable(dir_contents.FullName + @"\proj.info"))
                            {
                                XDBProjectVersion project_version = new XDBProjectVersion();
                                project_version.VersionUniq = dir_contents.Name.ToLower().Substring("version_".Length);
                                project_version.ProjectUniq = global_uniq;
                                project_version.GlobalUniq = Hash.Sha256(project_version.ProjectUniq + project_version.VersionUniq);

                                proj_info_data = File.ReadAllBytes(dir_contents.FullName + @"\proj.info");
                                GetVersionInfoFromBytes(project_version, proj_info_data);

                                project_versions_list.Add(project_version);
                            }

                            if (FileManager.IsFileAwailable(dir_contents.FullName + @"\webgl.info"))
                            {
                                ImportWebgl webgl_render = new ImportWebgl();
                                string[] webgl_info = File.ReadAllText(dir_contents.FullName + @"\webgl.info").Split(';');

                                webgl_render.date_created = long.Parse(webgl_info[0], NumberStyles.Any, CultureInfo.InvariantCulture);
                                webgl_render.webgl_uniq = webgl_info[1];
                                webgl_render.webgl_shortlink = webgl_info[2];
                                webgl_list.Add(webgl_render);
                            }
                        }

                        if (dir_contents.Name.ToLower().StartsWith("_logs"))
                        {
                            project_usages_list = GetProjectUsage(global_uniq, info.corporate_uniq, info.project_uniq, dir_contents.FullName);
                        }

                        if (dir_contents.Name.ToLower().StartsWith("data"))
                        {
                            project_render = GetProjectRender(dir_contents.FullName);
                        }
                    }

                    FileInfo latest_icon = null;
                    FileInfo[] project_files_list = dir.GetFiles();
                    foreach (FileInfo porj_file in project_files_list)
                    {
                        if (porj_file.Name.ToLower().EndsWith(".latest"))
                        {
                            project_status = GetProjectStatus(porj_file.FullName);
                        }

                        if (porj_file.Name.ToLower().EndsWith(".thumb"))
                        {
                            if(latest_icon == null || latest_icon.LastWriteTime < porj_file.LastWriteTime)
                                latest_icon = porj_file;
                        }
                    }

                    webgl_list = webgl_list.OrderByDescending(x => x.date_created).ToList();
                    ImportWebgl webgl_latest = (webgl_list.Count > 0) ? webgl_list[0] : null;

                    project_versions_list = project_versions_list.OrderByDescending(x => x.DateCreated).ToList();
                    XDBProjectVersion vesrion_latest = (project_versions_list.Count > 0) ? project_versions_list[0] : null;

                    if (project_status != null)
                    {
                        vesrion_latest = project_versions_list.Find(x => x.VersionUniq == project_status.latest);
                        if (vesrion_latest == null)
                            vesrion_latest = project_versions_list[0];
                    }

                    if (vesrion_latest != null)
                    {
                        if (latest_icon != null)
                        {
                            if (!File.Exists(info.project_path + @"\version_" + vesrion_latest.VersionUniq + @"\preview.thumb"))
                                File.Move(latest_icon.FullName, info.project_path + @"\version_" + vesrion_latest.VersionUniq + @"\preview.thumb");
                        }

                        string proj_latest_info_path = info.project_path + @"\version_" + vesrion_latest.VersionUniq + @"\proj.info";
                        byte[] proj_info_data_latest = File.ReadAllBytes(proj_latest_info_path);

                        // Заполняем данные о проекте
                        atlas_project = new XDBProject();
                        GetProjectFromBytes(atlas_project, proj_info_data_latest);
                        atlas_project.GlobalUniq = global_uniq;
                        atlas_project.ProjectType = (int)XDBProjectType.CORPORATE;
                        atlas_project.ProjectUniq = info.project_uniq.ToLower();
                        atlas_project.ProjectVersion = (vesrion_latest != null) ? vesrion_latest.VersionUniq : "";
                        atlas_project.CustomerUniq = "";
                        atlas_project.OrganizationUniq = info.corporate_uniq;
                        atlas_project.DateModified = vesrion_latest.DateCreated;

                        XDBMemberProfile profile = connection.Database.Table<UserProfile>().Where(x => x.Uniq == atlas_project.CreatorUniq).FirstOrDefault();
                        if (profile != null && profile.Uniq != "IMP-ISE" && profile.Uniq != "UST-UCH")
                            atlas_project.BranchUniq = profile.BranchUniq;
                        else
                            atlas_project.BranchUniq = 0;

                        // Заполняем информацию о проекте
                        XDBProjectInfo atlas_project_info = new XDBProjectInfo();
                        GetProjectInfoFromBytes(atlas_project_info, proj_info_data_latest);
                        atlas_project_info.ProjectUniq = global_uniq;
                        atlas_project_info.WebglUniq = (webgl_latest != null) ? webgl_latest.webgl_uniq : "";
                        atlas_project_info.WebglShortlink = (webgl_latest != null) ? webgl_latest.webgl_shortlink : "";
                        atlas_project_info.RnjbState = (project_status != null && project_status.rnjb_status == "COMPLETED") ? (int)XDBRenderState.COMPLETE : (int)XDBRenderState.INCOMPLETE;
                        atlas_project_info.DateRendered = (project_render == null) ? 0 : project_render.date_complete;

                        if (webgl_latest != null)
                        {
                            string[] fields = atlas_project.DestinationFields.Split(';');
                            atlas_project_info.WebglEmail = (fields.Length >= 2) ? fields[1] : "";
                        }
                        else
                        {
                            atlas_project_info.WebglEmail = "";
                        }

                        foreach (XDBProjectVersion version in project_versions_list)
                            version.BranchUniq = atlas_project.BranchUniq;

                        SQLiteConnection con = connection;
                        lock (con)
                        {
                            con.BeginTransaction();
                            try
                            {
                                con.Insert(atlas_project);
                                con.Insert(atlas_project_info);
                                con.InsertAll(project_versions_list);
                                con.InsertAll(project_usages_list);
                                con.Commit();
                            }
                            catch (Exception ex)
                            {
                                con.Rollback();
                                XLogger.Log(ex.ToString());
                            }
                        }
                        projects_imported++;
                    }
                    else
                    {
                        XLogger.Log("Is no any version of project exists " + info.project_uniq + ". Skip project.");
                    }
                }
                projects_done++;
                if (projects_done > 0 && projects_done % 100 == 0)
                    Thread.Sleep(50);
            }

            thread = null;
        }


        public static string GenProjectUniq(XRequestContext context, long company_uid, int start_size, int end_size, int tries_amount)
        {
            bool new_project_uniq_ready = false;
            string new_project_uniq = "";
            int uniq_size = start_size;

            // До тех пор пока не получилось и не вылезли за границы размерности uniq_id...
            while (!new_project_uniq_ready && uniq_size <= end_size)
            {
                int try_number = 0;
                // Пытаемся несколько раз сгенерировать неповторяющийся Uniq проекта заданной длины.
                while (!new_project_uniq_ready && try_number < tries_amount)
                {
                    try_number += 1;
                    new_project_uniq = XU.GetRandomUniq(uniq_size).ToLower();
                    new_project_uniq_ready = (XDBProjectServerManager.GetProject(context.Connection, company_uid, new_project_uniq) == null);
                }

                // Если не вышло, увеличиваем длину идентификатора
                uniq_size += 1;
            }

            // С вероятностью 99,9999% будет сгенерирован новый Uniq
            return new_project_uniq_ready ? new_project_uniq : null;
        }
        
        /// <summary>
        /// Осуществвляет иинхронизацию проекта, полученного от сервера, с проектами из локальной базы
        /// </summary>
        /// <param name="projectBundle"></param>
        public static void SynchronizeProjectData(XDatabaseAdapter connection, XDBProjectBundle projectBundle)
        {
            XDBProject local_project = connection.Database.Table<XDBProject>()
                .Where(x => x.GlobalUniq == projectBundle.project.GlobalUniq)
                .FirstOrDefault();

            if (local_project == null)
            {
                // Защищаемся от возможной ошибки, чтобы связи были в порядке
                projectBundle.info.ProjectUniq = projectBundle.project.GlobalUniq;
                projectBundle.version_latest.ProjectUniq = projectBundle.project.GlobalUniq;
                projectBundle.project.ProjectVersion = projectBundle.version_latest.VersionUniq;

                connection.BeginTransaction();
                try
                {
                    connection.Insert(projectBundle.project);
                    connection.Insert(projectBundle.info);
                    connection.Insert(projectBundle.version_latest);
                    connection.Commit();
                }
                catch (Exception ex)
                {
                    connection.Rollback();
                    XLogger.Log(ex.ToString());
                }
            }
            else
            {
                XDBProjectVersion local_version = connection.Database.Table<XDBProjectVersion>()
                    .Where(x => x.GlobalUniq == projectBundle.version_latest.GlobalUniq)
                    .FirstOrDefault();

                // Обязательно обновляем и сохраняем локальный проект, тк могут прислать всякий треш!
                local_project.ProjectName = projectBundle.project.ProjectName;
                local_project.DestinationFields = projectBundle.project.DestinationFields;
                local_project.DateModified = Math.Max(local_project.DateModified, projectBundle.project.DateModified);
                local_project.ProjectVersion = projectBundle.version_latest.VersionUniq;

                XDBProjectInfo local_info = connection.Database.Table<XDBProjectInfo>()
                    .Where(x => x.ProjectUniq == local_project.GlobalUniq)
                    .FirstOrDefault();

                local_info.Area = projectBundle.info.Area;
                local_info.TimeElapsed = projectBundle.info.TimeElapsed;

                projectBundle.version_latest.ProjectUniq = local_project.GlobalUniq;
                projectBundle.project = local_project;
                projectBundle.info = local_info;

                try
                {
                    connection.Update(local_project);
                    connection.Update(local_info);

                    if (local_version == null)
                        connection.Insert(projectBundle.version_latest);
                    else
                        connection.Update(projectBundle.version_latest);

                    connection.Commit();
                }
                catch (Exception ex)
                {
                    connection.Rollback();
                    XLogger.Log(ex.ToString());
                }
            }
        }

        /// <summary>
        /// Создает новый проект и сохраняет с базу данных
        /// </summary>
        /// <param name="prject_uniq"></param>
        /// <param name="project_name"></param>
        /// <param name="destination_fields"></param>
        /// <param name="reference"></param>
        /// <returns></returns>
        public static XDBProjectBundle CreateProject(
            XDatabaseAdapter connection,
            XRequestUserInfo user_info, 
            long company_uid,
            long branch_uid,
            string prject_uniq, 
            string project_name, 
            string customer_uniq, 
            string destination_fields, 
            XDBProjectBundle reference = null
            )
        {
            XDBProject atlas_project = new XDBProject();
            atlas_project.CompanyUid = company_uid;
            atlas_project.BranchUid = branch_uid;
            atlas_project.ProjectType = (int)XDBProjectType.CORPORATE;
            atlas_project.ProjectUniq = prject_uniq;
            atlas_project.ProjectName = project_name;
            atlas_project.CustomerUniq = customer_uniq;
            atlas_project.DestinationFields = destination_fields;

            atlas_project.CreatorType = (int)XDBCreatorType.INTERNAL;
            atlas_project.CreatorUniq = user_info.Profile.MemberUniq;
            atlas_project.CreatorName = user_info.Profile.FullName;

            atlas_project.ProjectVersion = null;
            atlas_project.DateCreated = XUtils.GetUnixTimestamp();
            atlas_project.DateModified = XUtils.GetUnixTimestamp();
            atlas_project.GlobalUniq = Hash.Sha256(atlas_project.CompanyUid + "#" + atlas_project.ProjectUniq);

            XDBProjectInfo atlas_project_info = null;
            if (reference != null && reference.info != null)
            {
                atlas_project_info = new XDBProjectInfo(reference.info);
                atlas_project_info.ProjectUniq = atlas_project.GlobalUniq;
                atlas_project_info.WebglUniq = "";
                atlas_project_info.WebglShortlink = "";
                atlas_project_info.WebglEmail = "";
                atlas_project_info.TimeElapsed = 0;
            }
            else
            {
                atlas_project_info = new XDBProjectInfo();
                atlas_project_info.ProjectUniq = atlas_project.GlobalUniq;
                atlas_project_info.Area = 0;
                atlas_project_info.RnjbUniq = "";
                atlas_project_info.RnjbState = (int)XDBRenderState.INCOMPLETE;
                atlas_project_info.WebglUniq = "";
                atlas_project_info.WebglShortlink = "";
                atlas_project_info.WebglEmail = "";
                atlas_project_info.TimeElapsed = 0;
                atlas_project_info.DateRendered = 0;
            }

            connection.BeginTransaction();
            try
            {
                connection.Insert(atlas_project);
                connection.Insert(atlas_project_info);
                connection.Commit();
                return new XDBProjectBundle(atlas_project, atlas_project_info, null);
            }
            catch (Exception ex)
            {
                connection.Rollback();
                XLogger.Log(ex.ToString());
                return null;
            }
        }

        /// <summary>
        /// Создает новый проект и сохраняет с базу данных
        /// </summary>
        /// <param name="prject_uniq"></param>
        /// <param name="project_name"></param>
        /// <param name="destination_fields"></param>
        /// <param name="reference"></param>
        /// <returns></returns>
        public static XDBProjectBundle CreateProject(
            XDatabaseAdapter connection,
            string customer_uniq,
            long company_uid,
            long branch_uid,
            string prject_uniq,
            string project_name,
            string destination_fields,
            XDBProjectBundle reference = null
            )
        {
            XDBProject atlas_project = new XDBProject();
            atlas_project.CompanyUid = company_uid;
            atlas_project.BranchUid = branch_uid;
            atlas_project.ProjectType = (int)XDBProjectType.CORPORATE;
            atlas_project.ProjectUniq = prject_uniq;
            atlas_project.ProjectName = project_name;
            atlas_project.CustomerUniq = customer_uniq;
            atlas_project.DestinationFields = destination_fields;

            atlas_project.CreatorType = (int)XDBCreatorType.EXTERNAL;
            atlas_project.CreatorUniq = customer_uniq;
            atlas_project.CreatorName = "";

            atlas_project.ProjectVersion = null;
            atlas_project.DateCreated = XUtils.GetUnixTimestamp();
            atlas_project.DateModified = XUtils.GetUnixTimestamp();
            atlas_project.GlobalUniq = Hash.Sha256(atlas_project.CompanyUid + "#" + atlas_project.ProjectUniq);

            XDBProjectInfo atlas_project_info = null;
            if (reference != null && reference.info != null)
            {
                atlas_project_info = new XDBProjectInfo(reference.info);
                atlas_project_info.ProjectUniq = atlas_project.GlobalUniq;
                atlas_project_info.WebglUniq = "";
                atlas_project_info.WebglShortlink = "";
                atlas_project_info.WebglEmail = "";
                atlas_project_info.TimeElapsed = 0;
            }
            else
            {
                atlas_project_info = new XDBProjectInfo();
                atlas_project_info.ProjectUniq = atlas_project.GlobalUniq;
                atlas_project_info.Area = 0;
                atlas_project_info.RnjbUniq = "";
                atlas_project_info.RnjbState = (int)XDBRenderState.INCOMPLETE;
                atlas_project_info.WebglUniq = "";
                atlas_project_info.WebglShortlink = "";
                atlas_project_info.WebglEmail = "";
                atlas_project_info.TimeElapsed = 0;
                atlas_project_info.DateRendered = 0;
            }

            connection.BeginTransaction();
            try
            {
                connection.Insert(atlas_project);
                connection.Insert(atlas_project_info);
                connection.Commit();
                return new XDBProjectBundle(atlas_project, atlas_project_info, null);
            }
            catch (Exception ex)
            {
                connection.Rollback();
                XLogger.Log(ex.ToString());
                return null;
            }
        }

        public static XDBProjectBundle GetProject(XDatabaseAdapter connection, string global_uniq)
        {
            XDBProject project = connection.Database.Table<XDBProject>().Where(x => x.GlobalUniq == global_uniq).FirstOrDefault();
            if (project == null)
                return null;

            XDBProjectInfo project_info = connection.Database.Table<XDBProjectInfo>()
                .Where(x => project.GlobalUniq == x.ProjectUniq)
                .FirstOrDefault();

            XDBProjectVersion version_latest = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.ProjectUniq == project.GlobalUniq && project.ProjectVersion == x.VersionUniq)
                .FirstOrDefault();

            return new XDBProjectBundle(project, project_info, version_latest);
        }

        public static XDBProjectBundle GetProject(XDatabaseAdapter connection, long company_uid, string project_uniq)
        {
            XDBProject project = connection.Database.Table<XDBProject>().Where(x => x.CompanyUid == company_uid && x.ProjectUniq == project_uniq).FirstOrDefault();
            if (project == null)
                return null;

            XDBProjectInfo project_info = connection.Database.Table<XDBProjectInfo>()
                .Where(x => project.GlobalUniq == x.ProjectUniq)
                .FirstOrDefault();

            XDBProjectVersion version_latest = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.ProjectUniq == project.GlobalUniq && project.ProjectVersion == x.VersionUniq)
                .FirstOrDefault();

            return new XDBProjectBundle(project, project_info, version_latest);
        }

        public static long GetProjectsCount(XDatabaseAdapter connection, long company_uid, long timestamp_since = 0)
        {
            return connection.Database.Table<XDBProject>().Where(x => x.CompanyUid == company_uid && x.DateModified > timestamp_since).Count();
        }

        public static long GetProjectsCount(XDatabaseAdapter connection, string creator_uniq, long timestamp_since = 0)
        {
            return connection.Database.Table<XDBProject>().Where(x => x.CreatorUniq == creator_uniq && x.DateModified > timestamp_since).Count();
        }

        public static List<XDBProjectHandle> GetProjectsHandles(XDatabaseAdapter connection, long company_uid, string member_uniq, int count)
        {
            List<XDBProjectHandle> handles_list = new List<XDBProjectHandle>();
            try
            {
                List<XDBProject> projectList = new List<XDBProject>();
                if (company_uid == 0)
                {
                    projectList.AddRange(connection.Database.Query<XDBProject>(
                        "SELECT prj.* FROM (SELECT * FROM _projects WHERE company_uid=0 AND creator_uniq='{0}') prj WHERE prj.date_modified>0 AND prj.project_version!='' ORDER BY prj.date_modified DESC LIMIT {1}",
                        member_uniq, count
                    ).ToList());
                }
                else
                {
                    projectList.AddRange(connection.Database.Query<XDBProject>(
                        "SELECT prj.* FROM (SELECT * FROM _projects WHERE company_uid={0}) prj WHERE prj.date_modified>0 AND prj.project_version!='' ORDER BY prj.date_modified DESC LIMIT {1}",
                        company_uid, count
                    ).ToList());
                }

                foreach (XDBProject project in projectList)
                {
                    if (project.ProjectVersion != null && project.ProjectVersion != "")
                        handles_list.Add(new XDBProjectHandle(project));
                }
            }
            catch (Exception ex)
            {
                XLogger.Log(ex.ToString());
                handles_list = new List<XDBProjectHandle>();
            }
            return handles_list;
        }

        public static List<XDBProjectHandle> GetProjectsHandlesWeb(XDatabaseAdapter connection, long company_uid, string customer_uniq, int count)
        {
            List<XDBProjectHandle> handles_list = new List<XDBProjectHandle>();
            try
            {
                List<XDBProject> projectList = connection.Database.Query<XDBProject>(
                    "SELECT prj.* FROM (SELECT * FROM _projects WHERE customer_uniq='{0}' AND company_uid={1}) prj WHERE prj.date_modified>0 AND prj.project_version!='' ORDER BY prj.date_modified DESC LIMIT {2}",
                    customer_uniq, company_uid, count
                ).ToList();

                foreach(XDBProject project in projectList)
                    handles_list.Add(new XDBProjectHandle(project));
            }
            catch (Exception ex)
            {
                XLogger.Log(ex.ToString());
                handles_list = new List<XDBProjectHandle>();
            }
            return handles_list;
        }

        public static List<XDBProjectBundle> GetProjectsList(XDatabaseAdapter connection, int company_uid, int count)
        {
            List<XDBProjectBundle> result_list = new List<XDBProjectBundle>();
            try
            {
                IEnumerator<XDBProject> projects_it = connection.Database.Table<XDBProject>()
                    .Where(x => x.CompanyUid == company_uid && x.ProjectVersion != null && x.ProjectVersion != "")
                    .OrderByDescending(x => x.DateModified)
                    .GetEnumerator();

                List<XDBProject> projects_list = new List<XDBProject>();
                while (projects_it.MoveNext() && projects_list.Count < count)
                    projects_list.Add(projects_it.Current);

                List<string> uniq_list = new List<string>();
                foreach (XDBProject proj in projects_list)
                    uniq_list.Add(proj.GlobalUniq);

                List<XDBProjectInfo> info_list = GetProjectsInfoList(connection, uniq_list);
                List<XDBProjectVersion> versions_list = GetProjectsVersionsList(connection, uniq_list);

                foreach (string project_uniq in uniq_list)
                {
                    XDBProject project = projects_list.Find(x => x.GlobalUniq == project_uniq);
                    XDBProjectInfo project_info = info_list.Find(x => x.ProjectUniq == project_uniq);

                    List<XDBProjectVersion> project_versions = versions_list.FindAll(x => x.ProjectUniq == project_uniq).OrderByDescending(x => x.DateCreated).ToList();
                    XDBProjectVersion version_latest = project_versions.Find(x => x.VersionUniq == project.ProjectVersion);

                    if (version_latest != null)
                    {
                        XDBProjectBundle project_wrap = new XDBProjectBundle(project, project_info, version_latest);
                        project_wrap.versions_list = project_versions;
                        result_list.Add(project_wrap);
                    }
                    else
                    {
                        XLogger.Log("Skip project: " + project.ProjectUniq);
                    }
                }
            }
            catch (Exception ex)
            {
                XLogger.Log(ex.ToString());
                result_list = new List<XDBProjectBundle>();
            }
            return result_list;
        }
        /*
        public static List<XDBProjectBundle> GetProjectsList(string customer_uniq, long timestamp_since, int count, int offset)
        {
            List<XDBProjectBundle> result_list = new List<XDBProjectBundle>();

            try
            {
                offset = Math.Max(0, offset);
                int page_size = Math.Min(count, 999);
                List<XDBProject> projects_list = new List<XDBProject>();

                do
                {
                    projects_list = connection.Database.Table<XDBProject>()
                        .Where(x => x.CustomerUniq == customer_uniq && x.ProjectVersion != null && x.ProjectVersion != "" && x.DateModified > timestamp_since)
                        .OrderByDescending(x => x.DateModified)
                        .Skip(offset)
                        .Take(page_size)
                        .ToList();

                    List<string> uniq_list = new List<string>();
                    foreach (XDBProject proj in projects_list)
                        uniq_list.Add(proj.GlobalUniq);

                    List<XDBProjectInfo> info_list = GetProjectsInfoList(uniq_list);
                    List<XDBProjectVersion> versions_list = GetProjectsVersionsList(uniq_list);

                    foreach (string project_uniq in uniq_list)
                    {
                        XDBProject project = projects_list.Find(x => x.GlobalUniq == project_uniq);
                        XDBProjectInfo project_info = info_list.Find(x => x.ProjectUniq == project_uniq);

                        List<XDBProjectVersion> project_versions = versions_list.FindAll(x => x.ProjectUniq == project_uniq).OrderByDescending(x => x.DateCreated).ToList();
                        XDBProjectVersion version_latest = project_versions.Find(x => x.VersionUniq == project.ProjectVersion);

                        if (version_latest != null)
                        {
                            XDBProjectBundle project_wrap = new XDBProjectBundle(project, project_info, version_latest);
                            project_wrap.versions_list = project_versions;
                            result_list.Add(project_wrap);
                        }
                        else
                        {
                            XLogger.Log("Skip project: " + project.ProjectUniq);
                        }
                    }

                    offset += page_size;
                }
                while (result_list.Count < count && projects_list != null && projects_list.Count > 0);
            }
            catch (Exception ex)
            {
                XLogger.Log(ex.ToString());
                result_list = new List<XDBProjectBundle>();
            }
            return result_list;
        }
  
        private static List<XDBProjectInfo> GetProjectsInfoList(XDatabaseAdapter connection, List<string> projects_uniqs)
        {
            int page_num = 0;
            int page_size = 999;
            List<XDBProjectInfo> info_list = new List<XDBProjectInfo>();
            List<XDBProjectInfo> info_tmp;

            do
            {
                info_tmp = connection.Database.Table<XDBProjectInfo>()
                    .Where(x => projects_uniqs.Contains(x.ProjectUniq))
                    .Skip(page_num * page_size)
                    .Take(page_size)
                    .ToList();

                info_list.AddRange(info_tmp);
                page_num++;
            }
            while (info_tmp != null && info_tmp.Count > 0);

            return info_list;
        }

        private static List<XDBProjectVersion> GetProjectsVersionsList(XDatabaseAdapter connection, List<string> projects_uniqs)
        {
            int page_num = 0;
            int page_size = 999;
            List<XDBProjectVersion> version_list = new List<XDBProjectVersion>();
            List<XDBProjectVersion> versions_tmp;

            do
            {
                versions_tmp = connection.Database.Table<XDBProjectVersion>()
                    .Where(x => projects_uniqs.Contains(x.ProjectUniq))
                    .Skip(page_num * page_size)
                    .Take(page_size)
                    .ToList();

                version_list.AddRange(versions_tmp);
                page_num++;
            }
            while (versions_tmp != null && versions_tmp.Count > 0);

            return version_list;
        }

        /// <summary>
        /// Производит поиск проектов по фильтру среди проектов указанной организации.
        /// </summary>
        /// <param name="company_uid"></param>
        /// <param name="filter"></param>
        /// <returns></returns>
        public static List<XDBProjectHandle> SearchProjectsHandles(XDatabaseAdapter connection, long company_uid, string filter)
        {
            List<XDBProjectHandle> result_list = new List<XDBProjectHandle>();
            try
            {
                int limit = 50;
                bool online_only = "online".IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0;
                List<XDBProject> search_result = new List<XDBProject>();

                // Поиск по идентификатору проекта
                IEnumerator<XDBProject> proj_it = connection.Database.Table<XDBProject>()
                    .Where(x => x.CompanyUid == company_uid)
                    .OrderByDescending(x => x.DateModified)
                    .GetEnumerator();

                while(proj_it.MoveNext())
                {
                    XDBProject atlas_project = proj_it.Current;
                    if (atlas_project.ProjectVersion == null || atlas_project.ProjectVersion == "")
                        continue;

                    if (atlas_project.ProjectUniq != null && atlas_project.ProjectUniq.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.ProjectName != null && atlas_project.ProjectName.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.DestinationFields != null && atlas_project.DestinationFields.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.CreatorName != null && atlas_project.CreatorName.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.CustomerUniq != null && atlas_project.CustomerUniq.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (online_only)
                    {
                        if (atlas_project.CreatorType == (int)XDBCreatorType.EXTERNAL)
                        {
                            search_result.Add(atlas_project);
                            continue;
                        }
                    }
                }

                search_result.Reverse();

                List<XDBProject> return_result = new List<XDBProject>();
                for (int i = 0; i < search_result.Count && return_result.Count < limit; i++)
                {
                    if (!return_result.Contains(search_result[i]))
                        return_result.Add(search_result[i]);
                }

                List<string> uniq_list = new List<string>();
                foreach (XDBProject proj in return_result)
                    uniq_list.Add(proj.GlobalUniq);

                foreach (string project_uniq in uniq_list)
                {
                    XDBProject project = return_result.Find(x => x.GlobalUniq == project_uniq);
                    if (project != null && project.DateModified > 0)
                    {
                        result_list.Add(new XDBProjectHandle(project));
                    }
                }
            }
            catch (Exception ex)
            {
                XLogger.Log(ex.ToString());
                result_list = new List<XDBProjectHandle>();
            }
            return result_list;
        }

        /// <summary>
        /// Производит поиск проектов по фильтру среди проектов указанной организации с проверкой доступности для пользователя
        /// </summary>
        /// <param name="company_uid"></param>
        /// <param name="filter"></param>
        /// <returns></returns>
        public static List<XDBProjectHandle> SearchProjectsHandles(XDatabaseAdapter connection, long company_uid, string filter, string customer_uniq)
        {
            List<XDBProjectHandle> result_list = new List<XDBProjectHandle>();
            try
            {
                int limit = 50;
                bool online_only = "online".IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0;
                List<XDBProject> search_result = new List<XDBProject>();

                // Поиск по идентификатору проекта
                IEnumerator<XDBProject> proj_it = connection.Database.Table<XDBProject>()
                    .Where(x => x.CompanyUid == company_uid && x.CustomerUniq == customer_uniq)
                    .OrderByDescending(x => x.DateModified)
                    .GetEnumerator();

                while (proj_it.MoveNext())
                {
                    XDBProject atlas_project = proj_it.Current;
                    if (atlas_project.ProjectVersion == null || atlas_project.ProjectVersion == "")
                        continue;

                    if (atlas_project.ProjectUniq.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.ProjectName.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.DestinationFields.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.CreatorName.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (atlas_project.CustomerUniq.IndexOf(filter, StringComparison.OrdinalIgnoreCase) >= 0)
                    {
                        search_result.Add(atlas_project);
                        continue;
                    }

                    if (online_only)
                    {
                        if (atlas_project.CreatorType == (int)XDBCreatorType.EXTERNAL)
                        {
                            search_result.Add(atlas_project);
                            continue;
                        }
                    }
                }

                search_result.Reverse();

                List<XDBProject> return_result = new List<XDBProject>();
                for (int i = 0; i < search_result.Count && return_result.Count < limit; i++)
                {
                    if (!return_result.Contains(search_result[i]))
                        return_result.Add(search_result[i]);
                }

                List<string> uniq_list = new List<string>();
                foreach (XDBProject proj in return_result)
                    uniq_list.Add(proj.GlobalUniq);

                foreach (string project_uniq in uniq_list)
                {
                    XDBProject project = return_result.Find(x => x.GlobalUniq == project_uniq);
                    if (project != null && project.DateModified > 0)
                    {
                        result_list.Add(new XDBProjectHandle(project));
                    }
                }
            }
            catch (Exception ex)
            {
                XLogger.Log(ex.ToString());
                result_list = new List<XDBProjectHandle>();
            }
            return result_list;
        }

        private static void GetProjectFromBytes(XDBProject atlas_project, byte[] bt_data)
        {
            XDat.BlockEntry blocks = XDat.BlocksFromData(bt_data);
            atlas_project.CreatorUniq = "";
            atlas_project.CreatorName = "";
            string creator_iniq = blocks.BlockExists("AUTH_CREATOR") ? blocks.FindBlockString("AUTH_CREATOR", true) : "";
            string creator_name = blocks.BlockExists("AUTH_CREATOR_NAME") ? blocks.FindBlockString("AUTH_CREATOR_NAME", true) : "";

            atlas_project.ProjectName = blocks.FindBlockString("PROJECT_NAME", true);
            atlas_project.DestinationFields = blocks.FindBlockString("INFO_DESTINATION", true);
            atlas_project.CreatorType = (int)XDBCreatorType.INTERNAL;
            atlas_project.CreatorUniq = string.IsNullOrEmpty(creator_iniq) ? atlas_project.CreatorUniq : creator_iniq;
            atlas_project.CreatorName = string.IsNullOrEmpty(creator_name) ? atlas_project.CreatorName : creator_name;
            atlas_project.DateCreated = (long)blocks.FindBlockDouble("TS_CREATION");
            atlas_project.DateModified = (long)blocks.FindBlockDouble("TS");

            if (atlas_project.DateCreated == 0 && atlas_project.DateModified != 0)
                atlas_project.DateCreated = atlas_project.DateModified;

            if (atlas_project.DateModified == 0 && atlas_project.DateCreated != 0)
                atlas_project.DateModified = atlas_project.DateCreated;
        }

        private static void GetProjectInfoFromBytes(XDBProjectInfo atlas_project_info, byte[] bt_data)
        {
            XDat.BlockEntry blocks = XDat.BlocksFromData(bt_data);
            atlas_project_info.TimeElapsed = blocks.FindBlockFloat("TIME_ELAPSED");
            atlas_project_info.Area = blocks.FindBlockFloat("INFO_M2");
            atlas_project_info.RnjbUniq = blocks.FindBlockString("INFO_RNJB_ID", true);
        }

        private static void GetVersionInfoFromBytes(XDBProjectVersion atlas_project_version, byte[] bt_data) 
        {
            XDat.BlockEntry blocks = XDat.BlocksFromData(bt_data);
            atlas_project_version.SoftwareVersion = blocks.FindBlockInt("SOFT_VERSION");
            atlas_project_version.CreatorType = (int)XDBCreatorType.INTERNAL;
            atlas_project_version.CreatorUniq = blocks.FindBlockString("AUTH", true);
            atlas_project_version.CreatorName = blocks.FindBlockString("AUTH_NAME", true);
            atlas_project_version.DateCreated = (long)blocks.FindBlockDouble("TS");
            atlas_project_version.SyncStatus = (int)XDBVersionStatus.SYNC;
        }
        /* Obsolete
        private static List<XDBProjectUsage> GetProjectUsage(string global_uniq, int organization_uniq, string project_uniq, string data_path)
        {
            List<XDBProjectUsage> usage_list = new List<XDBProjectUsage>();

            string[] files_list = Directory.GetFiles(data_path, "*.csv", SearchOption.TopDirectoryOnly);
            foreach(string file_name in files_list)
            {
                XDBProjectUsage project_usage = null;
                int substr_from = file_name.LastIndexOf(@"_") + 1;
                int substr_to = file_name.LastIndexOf(@".");
                string timestamp = file_name.Substring(substr_from, substr_to - substr_from);

                string[] values = File.ReadAllLines(file_name);
                if (values[1].Contains("HMD_USAGE"))
                {
                    project_usage = new XDBProjectUsage();
                    project_usage.UsageType = (int)XDBProjectUsageType.HMD;
                    project_usage.UsageTime = float.Parse(values[2].Split(';')[1], NumberStyles.Any, CultureInfo.InvariantCulture);
                    project_usage.TriggerAction = "";
                }

                if (values[1].Contains("PROJECT_SAVE"))
                {
                    project_usage = new XDBProjectUsage();
                    project_usage.UsageType = (int)XDBProjectUsageType.EDIT;
                    project_usage.UsageTime = float.Parse(values[3].Split(';')[1], NumberStyles.Any, CultureInfo.InvariantCulture);
                    project_usage.TriggerAction = values[2].Split(';')[1];
                }

                if (project_usage != null)
                {
                    string profile_uniq = values[0].Split(';')[1];
                    UserProfile profile = XDBServerManager.GetUserProfile(profile_uniq);

                    project_usage.GlobalUniq = global_uniq;
                    project_usage.OrganizationUniq = (profile != null) ? profile.OrganizationUniq : organization_uniq;
                    project_usage.BranchUniq = (profile != null) ? profile.BranchUniq : 0;
                    project_usage.ProjectUniq = project_uniq;
                    project_usage.CreatorType = (int)XDBCreatorType.INTERNAL;
                    project_usage.CreatorUniq = (profile != null) ? profile.Uniq : profile_uniq;
                    project_usage.CreatorName = (profile != null) ? profile.GetFullName() : "";
                    project_usage.DateCreated = long.Parse(timestamp, NumberStyles.Any, CultureInfo.InvariantCulture);
                    usage_list.Add(project_usage);
                }
            }

            return usage_list;
        }

        private static ImportStatus GetProjectStatus(string file_path)
        {
            string[] values = File.ReadAllLines(file_path);
            ImportStatus import_status = new ImportStatus();
            import_status.rnjb_status = values[2];
            import_status.webgl_uniq = values[3];
            import_status.latest = values[0];
            return import_status;
        }

        private static ImportRender GetProjectRender(string dir_path)
        {
            ImportRender import_render = new ImportRender();
            import_render.date_complete = 0;

            DirectoryInfo dir = new DirectoryInfo(dir_path);
            FileInfo[] file_list = dir.GetFiles();
            foreach(FileInfo file in file_list)
            {
                if(file.Extension.Contains("dat"))
                    import_render.date_complete = Math.Max(import_render.date_complete, (long)XUtils.GetUnixTimestamp(file.LastWriteTime));
            }

            return import_render;
        }
    }
}

*/
