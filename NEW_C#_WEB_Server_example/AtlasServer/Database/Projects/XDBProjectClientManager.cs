/*
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using UnityEngine;

using Atlas.Database;

namespace Atlas
{
    /// <summary>
    /// Модуль для работы с базой данных проектов на клиентской части.
    /// Предоставляет удобный интерфейс для считывания и записи данных проектов.
    /// </summary>
    public class XDBProjectClientManager
    {
        public static void SynchronizeProjectsData(XDatabaseAdapter connection, List<XDBProjectBundle> projects_list)
        {
            foreach (XDBProjectBundle project_wrap in projects_list)
            {
                SynchronizeProjectData(connection, project_wrap);
            }
        }

        /// <summary>
        /// Осуществвляет иинхронизацию проекта, полученного от сервера, с проектами из локальной базы
        /// </summary>
        /// <param name="project_wrap"></param>
        public static void SynchronizeProjectData(XDatabaseAdapter connection, XDBProjectBundle project_wrap)
        {
            XDBProject local_project = connection.Database.Table<XDBProject>()
                .Where(x => x.GlobalUniq == project_wrap.project.GlobalUniq)
                .FirstOrDefault();

            XDBProjectInfo local_info = null;
            local_info = connection.Database.Table<XDBProjectInfo>()
                .Where(x => x.ProjectUniq == project_wrap.info.ProjectUniq)
                .FirstOrDefault();

            XDBProjectVersion local_version = null;
            if (project_wrap.version_latest != null)
            {
                local_version = connection.Database.Table<XDBProjectVersion>()
                    .Where(x => x.GlobalUniq == project_wrap.version_latest.GlobalUniq)
                    .FirstOrDefault();
            }


            if (local_project == null)
            {
                lock (connection)
                {
                    connection.BeginTransaction();
                    try
                    {
                        connection.Insert(project_wrap.project);
                        connection.Insert(project_wrap.info);

                        if (local_version != null)
                            connection.Update(project_wrap.version_latest);
                        else if (project_wrap.version_latest != null)
                            connection.Insert(project_wrap.version_latest);

                        //if (project_wrap.version_latest != null)
                        //connection.Insert(project_wrap.version_latest);

                        connection.Commit();
                    }
                    catch (Exception ex)
                    {
                        connection.Rollback();

                    }
                }
            }
            else
            {
                lock (connection)
                {
                    connection.BeginTransaction();
                    try
                    {
                        connection.Update(project_wrap.project);

                        if (local_info != null)
                            connection.Update(project_wrap.info);
                        else 
                            connection.Insert(project_wrap.info);

                        if (local_version != null)
                            connection.Update(project_wrap.version_latest);
                        else if (project_wrap.version_latest != null)
                            connection.Insert(project_wrap.version_latest);

                        connection.Commit();
                    }
                    catch (Exception ex)
                    {
                        connection.Rollback();

                    }
                }
            }
        }

        /// <summary>
        /// Осуществвляет иинхронизацию проекта, полученного от сервера, с проектами из локальной базы
        /// </summary>
        /// <param name="project_handle"></param>
        public static void SynchronizeProjectHandle(XDatabaseAdapter connection, XDBProjectHandle project_handle)
        {
            XDBProject local_project = connection.Database.Table<XDBProject>()
                .Where(x => x.GlobalUniq == project_handle.GlobalUniq)
                .FirstOrDefault();

            if (local_project == null)
            {
                lock (connection)
                {
                    connection.BeginTransaction();
                    try
                    {
                        connection.Insert(CreateUnsyncronizedProject(project_handle));
                        connection.Commit();
                    }
                    catch (Exception ex)
                    {
                        connection.Rollback();

                    }
                }
            }
            else if(local_project.DateModified < project_handle.DateModified)
            {
                lock (connection)
                {
                    connection.BeginTransaction();
                    try
                    {
                        // Пустая версия означает что версия утратила акутальность и требуется загрузка с сервера
                        local_project.ProjectVersion = "";
                        connection.Update(local_project);
                        connection.Commit();
                    }
                    catch (Exception ex)
                    {
                        connection.Rollback();

                    }
                }
            }
        }

        /// <summary>
        /// Определяет статус несинхронизированного проекта и его версии.
        /// Если проект отсутсвует в локальной базе данных, автоматически добавляет его в базу.
        /// </summary>
        /// <param name="company_uid"></param>
        /// <param name="project_dir"></param>
        /// <returns></returns>
        public static bool CheckUnsynchronizedProject(XDatabaseAdapter connection, long company_uid, DirectoryInfo project_dir)
        {
            XDBProject atlas_project = connection.Database.Table<XDBProject>()
                .Where(x => x.CompanyUid == company_uid && x.ProjectUniq == project_dir.Name)
                .FirstOrDefault();

            List<DirectoryInfo> versions_dir_list = new List<DirectoryInfo>(project_dir.GetDirectories());
            versions_dir_list = versions_dir_list.OrderByDescending(x => x.LastWriteTime).ToList();
            for (int i = 0; i < versions_dir_list.Count; i++)
            {
                DirectoryInfo version_dir = versions_dir_list[i];
                if (version_dir.Name.StartsWith("version_"))
                {
                    if (FileManager.IsFileAwailable(version_dir.FullName + @"\proj.atl") && FileManager.IsFileAwailable(version_dir.FullName + @"\proj.info"))
                    {
                        string version_uniq = version_dir.Name.Substring(8);
                        if (atlas_project != null)
                        {
                            XDBProjectVersion atlas_project_version = connection.Database.Table<XDBProjectVersion>()
                                .Where(x => x.ProjectUniq == atlas_project.GlobalUniq && x.VersionUniq == version_uniq)
                                .FirstOrDefault();

                            if (atlas_project_version == null)
                            {
                                XDBProjectBundle proj_wrap = CreateUnsynchronizedVersion(connection, atlas_project, project_dir.Name, version_uniq);
                                proj_wrap.project.ProjectVersion = proj_wrap.version_latest.VersionUniq;
                                proj_wrap.project.DateModified = Math.Max(proj_wrap.version_latest.DateCreated, atlas_project.DateModified);
                                Debug.Log("[SYNC] Added unsynchronizred version: " + version_dir.FullName);

                                lock (connection)
                                {
                                    connection.BeginTransaction();
                                    try
                                    {
                                        connection.Update(proj_wrap.project);
                                        connection.Update(proj_wrap.info);
                                        connection.Insert(proj_wrap.version_latest);
                                        connection.Commit();
                                    }
                                    catch (Exception ex)
                                    {
                                        connection.Rollback();
                                        Debug.LogException(ex);
                                    }
                                }
                                return true;
                            }
                            else
                            {
                                return false;
                            }
                        }
                        else
                        {
                            XDBProjectBundle proj_wrap = CreateUnsynchronizedProject(company_uid, project_dir.Name, version_uniq);

                            lock (connection)
                            {
                                connection.BeginTransaction();
                                try
                                {
                                    connection.Insert(proj_wrap.project);
                                    connection.Insert(proj_wrap.info);
                                    connection.Insert(proj_wrap.version_latest);
                                    connection.Commit();
                                }
                                catch (Exception ex)
                                {
                                    connection.Rollback();
                     
                                }
                            }
                            return true;
                        }
                    }
                    else
                    {
            
                    }
                }
            }
            return false;
        }

        /// <summary>
        /// Возвращает количество проектов в базе
        /// </summary>
        /// <param name="organiztion_uniq"></param>
        /// <param name="timestamp_since"></param>
        /// <returns></returns>
        public static int GetProjectsCount(XDatabaseAdapter connection, int organiztion_uniq)
        {
            return (int)connection.Database.Table<XDBProject>().Where(x => x.CompanyUid == organiztion_uniq).Count();
        }

        /// <summary>
        /// Возвращает количество проектов в базе
        /// </summary>
        /// <param name="creator_uniq"></param>
        /// <param name="timestamp_since"></param>
        /// <returns></returns>
        public static int GetProjectsCount(XDatabaseAdapter connection, string creator_uniq)
        {
            return (int)connection.Database.Table<XDBProject>().Where(x => x.CreatorUniq == creator_uniq).Count();
        }

        /// <summary>
        /// Возвращает количество проектов, по которым еще не загружены данные
        /// </summary>
        /// <param name="count"></param>
        /// <returns></returns>
        public static int GetEmptyProjectsCount(XDatabaseAdapter connection, long company_uid)
        {
            List<XDBProject> atlas_projects_list = new List<XDBProject>();
            List<XDBProject> atlas_projects_temp;

            try
            {
                int page = 0;
                int limit = 999;

                do
                {
                    atlas_projects_temp = connection.Database.Table<XDBProject>()
                        .Where(x => x.CompanyUid == company_uid && (x.ProjectUniq == null || x.ProjectUniq == ""))
                        .Skip(page * limit)
                        .Take(limit)
                        .ToList();

                    atlas_projects_list.AddRange(atlas_projects_temp);
                    page++;
                }
                while (atlas_projects_temp.Count > 0);
            }
            catch(Exception ex)
            {
                atlas_projects_list = new List<XDBProject>();
            }
            return atlas_projects_list.Count;
        }

        /// <summary>
        /// Возвращает список проектов, по которым еще не загружены данные
        /// </summary>
        /// <param name="count"></param>
        /// <returns></returns>
        public static List<XDBProject> GetEmptyProjects(XDatabaseAdapter connection, long company_uid, int count, List<string> except)
        {
            IEnumerator<XDBProject> atlas_project_it = connection.Database.Table<XDBProject>()
                .Where(x => x.CompanyUid == company_uid && (x.ProjectUniq == null || x.ProjectUniq == ""))
                .OrderByDescending(x => x.DateCreated)
                .GetEnumerator();


            List<XDBProject> atlas_projects_list = new List<XDBProject>();
            while(atlas_project_it.MoveNext() && atlas_projects_list.Count < count)
            {
                if (!except.Contains(atlas_project_it.Current.GlobalUniq))
                    atlas_projects_list.Add(atlas_project_it.Current);
            }
            return atlas_projects_list;
        }

        /// <summary>
        /// Возвращает наиболее актуальный проект, требующий отправки на сервер
        /// </summary>
        /// <param name="organization_uniq"></param>
        /// <returns></returns>
        public static XDBProjectBundle GetUnsynchronizedProject(XDatabaseAdapter connection)
        {
            XDBProjectVersion latest_unsync_version = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.SyncStatus < (int)XDBVersionStatus.SYNC)
                .OrderByDescending(x => x.DateCreated)
                .FirstOrDefault();

            if (latest_unsync_version != null)
            {
                XDBProject atlas_project = connection.Database.Table<XDBProject>()
                    .Where(x => x.GlobalUniq == latest_unsync_version.ProjectUniq)
                    .FirstOrDefault();

                if (atlas_project != null)
                {
                    XDBProjectInfo atlas_project_info = connection.Database.Table<XDBProjectInfo>()
                        .Where(x => x.ProjectUniq == atlas_project.GlobalUniq)
                        .FirstOrDefault();

                    return new XDBProjectBundle(atlas_project, atlas_project_info, latest_unsync_version);
                }
            }
            return null;
        }
        
        /// <summary>
        /// Помечает все версии проекта статусом ошибки, которая не позволит синхронизировать проект в будущем
        /// </summary>
        /// <param name="organization_uniq"></param>
        public static void SetProjectUnsynchronizable(XDatabaseAdapter connection, string global_uniq)
        {
            List<XDBProjectVersion> versionList = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.ProjectUniq == global_uniq && x.SyncStatus < (int)XDBVersionStatus.SYNC)
                .OrderByDescending(x => x.DateCreated)
                .ToList();

            foreach (XDBProjectVersion version in versionList)
            {
                version.SyncStatus = (int)XDBVersionStatus.ERROR;
                connection.Database.Update(version);
            }
        }
        
        /// <summary>
        /// Проверяет наличие более новой, уже синхронизированной, версии проекта на компьютере
        /// </summary>
        /// <returns></returns>
        public static bool CheckForNewerVersion(XDatabaseAdapter connection, XDBProjectVersion version)
        {
            XDBProjectVersion latest_sync_version = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.ProjectUniq == version.ProjectUniq && x.SyncStatus == (int)XDBVersionStatus.SYNC)
                .OrderByDescending(x => x.DateCreated)
                .FirstOrDefault();

            if (latest_sync_version != null)
            {
                if (latest_sync_version.DateCreated > version.DateCreated)
                    return true;
            }
            return false;
        }

        /// <summary>
        /// Удаляет устаревшую несинхронизированую версию из базы
        /// </summary>
        /// <returns></returns>
        public static bool DeleteObsoleteVersion(XDatabaseAdapter connection, XDBProjectVersion version)
        {
            XDBProjectVersion obsolete_version = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.GlobalUniq == version.GlobalUniq)
                .FirstOrDefault();

            if (obsolete_version != null && obsolete_version.SyncStatus == (int)XDBVersionStatus.NONSYNC)
            {
                connection.Database.Delete(obsolete_version);
                return true;
            }
            return false;
        }

        /// <summary>
        /// Возвращает проект по глобальному идентификатору проекта
        /// </summary>
        /// <param name="project_global_uniq"></param>
        /// <returns></returns>
        public static XDBProjectBundle GetProject(XDatabaseAdapter connection, string project_global_uniq)
        {
            XDBProject project = connection.Database.Table<XDBProject>().Where(x => x.GlobalUniq == project_global_uniq).FirstOrDefault();
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

        /// <summary>
        /// Возвращает проект по идентификатору проекта
        /// </summary>
        /// <param name="company_uid"></param>
        /// <param name="project_uniq"></param>
        /// <returns></returns>
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

        /// <summary>
        /// Создает новый проект и сохраняет в базу данных
        /// </summary>
        /// <param name="prject_uniq"></param>
        /// <param name="project_name"></param>
        /// <param name="destination_fields"></param>
        /// <returns></returns>
        public static XDBProjectBundle CreateEmptyProject(XDatabaseAdapter connection, string prject_uniq, string project_name, string customer_uniq, string destination_fields)
        {
            XDBProject atlas_project = new XDBProject();
            atlas_project.CompanyUid = App.UserSession.CompanyDefault.Uid;
            atlas_project.BranchUid = App.UserSession.CompanyDefault.BranchUid;
            atlas_project.ProjectType = (int)XDBProjectType.CORPORATE;
            atlas_project.ProjectUniq = prject_uniq;
            atlas_project.ProjectName = project_name;
            atlas_project.CustomerUniq = customer_uniq;
            atlas_project.DestinationFields = destination_fields;

            atlas_project.CreatorType = (int)XDBCreatorType.INTERNAL;
            atlas_project.CreatorUniq = App.UserSession.UserUniq;
            atlas_project.CreatorName = App.UserSession.GetFullName();

            atlas_project.ProjectVersion = null;
            atlas_project.DateCreated = XUtils.GetUnixTimestamp();
            atlas_project.DateModified = XUtils.GetUnixTimestamp();
            atlas_project.GlobalUniq = Hash.Sha256(atlas_project.CompanyUid + "#" + atlas_project.ProjectUniq);

            XDBProjectInfo atlas_project_info = new XDBProjectInfo();
            atlas_project_info.ProjectUniq = atlas_project.GlobalUniq;
            atlas_project_info.Area = 0;
            atlas_project_info.RnjbUniq = "";
            atlas_project_info.RnjbState = (int)XDBRenderState.INCOMPLETE;
            atlas_project_info.WebglUniq = "";
            atlas_project_info.WebglShortlink = "";
            atlas_project_info.WebglEmail = ""; 
            atlas_project_info.TimeElapsed = 0;
            atlas_project_info.DateRendered = 0;

            lock (connection)
            {
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
                    Debug.LogException(ex);
                    return null;
                }
            }
        }

        /// <summary>
        /// Сохарняет проект в базе данных для дальнейшей синхронизации
        /// </summary>
        /// <param name="organiztion_uniq"></param>
        /// <param name="project_uniq"></param>
        /// <returns></returns>
        public static bool SaveProject(XDatabaseAdapter connection, XDBProjectBundle project_wrap)
        {
            XDBProjectVersion project_version = connection.Database.Table<XDBProjectVersion>()
                .Where(x => x.GlobalUniq == project_wrap.version_latest.GlobalUniq)
                .FirstOrDefault();

            lock (connection)
            {
                connection.BeginTransaction();
                try
                {
                    connection.Update(project_wrap.project);
                    connection.Update(project_wrap.info);

                    if (project_version != null)
                        connection.Update(project_wrap.version_latest);
                    else
                        connection.Insert(project_wrap.version_latest);

                    connection.Commit();
                    return true;
                }
                catch (Exception ex)
                {
                    connection.Rollback();
                    Debug.LogException(ex);
                    return false;
                }
            }
        }

        /// <summary>
        /// Возвращает список проектов из каталога организации
        /// </summary>
        /// <param name="cataloguie_id"></param>
        /// <returns></returns>
        public static List<XDBProjectBundle> GetProjectsList(XDatabaseAdapter connection, long company_uid, string member_uniq)
        {
            List<XDBProjectBundle> result_list = new List<XDBProjectBundle>();
            try
            {
                List<XDBProject> projects_list = new List<XDBProject>();
                if (company_uid == 0)
                {
                    projects_list.AddRange(connection.Database.Table<XDBProject>()
                        .Where(x => x.CompanyUid == company_uid && x.CreatorUniq == member_uniq)
                        .OrderByDescending(x => x.DateModified)
                        .Take(500)
                        .ToList()
                    );
                }
                else
                {
                    projects_list.AddRange(connection.Database.Table<XDBProject>()
                        .Where(x => x.CompanyUid == company_uid)
                        .OrderByDescending(x => x.DateModified)
                        .Take(500)
                        .ToList()
                    );
                }
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

                    XDBProjectBundle project_wrap = new XDBProjectBundle(project, project_info, version_latest);
                    project_wrap.versions_list = project_versions;
                    result_list.Add(project_wrap);
                }
            }
            catch (Exception ex)
            {
                Debug.LogException(ex);
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
        /// Создает с нуля и возвращает несинхронизированный проект, отсутсвующий в базе
        /// </summary>
        /// <param name="organization_uniq"></param>
        /// <param name="project_uniq"></param>
        /// <param name="version_uniq"></param>
        /// <returns></returns>
        private static XDBProject CreateUnsyncronizedProject(XDBProjectHandle project_handle)
        {
            XDBProject atlas_project = new XDBProject();
            atlas_project.GlobalUniq = project_handle.GlobalUniq;
            atlas_project.ProjectName = "";
            atlas_project.ProjectUniq = "";
            atlas_project.ProjectVersion = "";
            atlas_project.ProjectType = 0;
            atlas_project.CustomerUniq = "";
            atlas_project.CreatorType = 0;
            atlas_project.CreatorUniq = "";
            atlas_project.CreatorName = "";
            atlas_project.CompanyUid = project_handle.CompanyUid;
            atlas_project.BranchUid = project_handle.BranchUid;
            atlas_project.DestinationFields = "";
            atlas_project.DateCreated = project_handle.DateCreated;
            atlas_project.DateModified = project_handle.DateModified;
            return atlas_project;
        }

    /// <summary>
    /// Создает с нуля и возвращает несинхронизированный проект, отсутсвующий в базе
    /// </summary>
    /// <param name="company_uid"></param>
    /// <param name="project_uniq"></param>
    /// <param name="version_uniq"></param>
    /// <returns></returns>
    private static XDBProjectBundle CreateUnsynchronizedProject(long company_uid, string project_uniq, string version_uniq)
        {
            string project_path = GlobalResources.GetAccountDataPath() + company_uid.ToString() + @"\Projects\" + project_uniq + @"\";
            string version_path = project_path + "version_" + version_uniq + @"\";

            string global_uniq = Hash.Sha256(company_uid.ToString() + "#" + project_uniq);
            string proj_latest_info_path = version_path + @"proj.info";
            byte[] proj_info_data = File.ReadAllBytes(proj_latest_info_path);

            // Заполняем данные о проекте
            XDBProject atlas_project = new XDBProject();
            GetProjectFromBytes(atlas_project, proj_info_data);
            atlas_project.GlobalUniq = global_uniq;
            atlas_project.ProjectType = (int)XDBProjectType.CORPORATE;
            atlas_project.ProjectUniq = project_uniq;
            atlas_project.ProjectVersion = version_uniq;
            atlas_project.CustomerUniq = "";
            atlas_project.CompanyUid = company_uid;
            atlas_project.BranchUid = App.UserSession.CompanyList.Find(x => x.Uid == company_uid).BranchUid;

            // Заполняем информацию о проекте
            XDBProjectInfo atlas_project_info = new XDBProjectInfo();
            GetProjectInfoFromBytes(atlas_project_info, proj_info_data);
            atlas_project_info.ProjectUniq = global_uniq;
            atlas_project_info.WebglUniq = "";
            atlas_project_info.WebglShortlink = "";
            atlas_project_info.WebglEmail = "";
            atlas_project_info.RnjbState = (int)XDBRenderState.INCOMPLETE;
            atlas_project_info.DateRendered = 0;

            XDBProjectVersion project_version = new XDBProjectVersion();
            project_version.VersionUniq = version_uniq;
            project_version.ProjectUniq = global_uniq;
            project_version.GlobalUniq = Hash.Sha256(project_version.ProjectUniq + project_version.VersionUniq);
            GetVersionInfoFromBytes(project_version, proj_info_data);
            project_version.SyncStatus = (int)XDBVersionStatus.NONSYNC;
            project_version.BranchUid = atlas_project.BranchUid;

            return new XDBProjectBundle(atlas_project, atlas_project_info, project_version);
        }

        /// <summary>
        /// Создает с нуля и возвращает несинхронизированную версию проекта, отсутсвующую в базе
        /// </summary>
        /// <param name="atlas_project"></param>
        /// <param name="project_uniq"></param>
        /// <param name="version_uniq"></param>
        /// <returns></returns>
        private static XDBProjectBundle CreateUnsynchronizedVersion(XDatabaseAdapter connection, XDBProject atlas_project, string project_uniq, string version_uniq)
        {
            string project_path = GlobalResources.GetAccountDataPath() + atlas_project.CompanyUid.ToString() + @"\Projects\" + project_uniq + @"\";
            string version_path = project_path + "version_" + version_uniq + @"\";

            string proj_latest_info_path = version_path + @"proj.info";
            byte[] proj_info_data = File.ReadAllBytes(proj_latest_info_path);

            // Заполняем информацию о проекте
            XDBProjectVersion project_version = new XDBProjectVersion();
            project_version.VersionUniq = version_uniq;
            project_version.ProjectUniq = atlas_project.GlobalUniq;
            project_version.GlobalUniq = Hash.Sha256(project_version.ProjectUniq + project_version.VersionUniq);
            GetVersionInfoFromBytes(project_version, proj_info_data);
            project_version.SyncStatus = (int)XDBVersionStatus.NONSYNC;
            atlas_project.BranchUid = App.UserSession.CompanyList.Find(x => x.Uid == atlas_project.CompanyUid).BranchUid;

            XDat.BlockEntry blocks = XDat.BlocksFromData(proj_info_data);
            atlas_project.ProjectName = blocks.FindBlockString("PROJECT_NAME", true);
            atlas_project.DestinationFields = blocks.FindBlockString("INFO_DESTINATION", true);

            XDBProjectInfo atlas_project_info = connection.Database.Table<XDBProjectInfo>()
                .Where(x => x.ProjectUniq == atlas_project.GlobalUniq)
                .FirstOrDefault();

            if(atlas_project_info == null)
            {
                atlas_project_info = new XDBProjectInfo();
                atlas_project_info.ProjectUniq = atlas_project.GlobalUniq;
                atlas_project_info.WebglUniq = "";
                atlas_project_info.WebglShortlink = "";
                atlas_project_info.WebglEmail = "";
                atlas_project_info.RnjbState = (int)XDBRenderState.INCOMPLETE;
                atlas_project_info.DateRendered = 0;
            }
            GetProjectInfoFromBytes(atlas_project_info, proj_info_data);

            return new XDBProjectBundle(atlas_project, atlas_project_info, project_version);
        }

        private static void GetProjectFromBytes(XDBProject atlas_project, byte[] bt_data)
        {
            XDat.BlockEntry blocks = XDat.BlocksFromData(bt_data);
            atlas_project.ProjectName = blocks.FindBlockString("PROJECT_NAME", true);
            atlas_project.DestinationFields = blocks.FindBlockString("INFO_DESTINATION", true);
            atlas_project.CreatorType = (int)XDBCreatorType.INTERNAL;
            atlas_project.CreatorUniq = blocks.BlockExists("AUTH_CREATOR") ? blocks.FindBlockString("AUTH_CREATOR", true) : "";
            atlas_project.CreatorName = blocks.BlockExists("AUTH_CREATOR_NAME") ? blocks.FindBlockString("AUTH_CREATOR_NAME", true) : "";
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
        }
    }
}

*/