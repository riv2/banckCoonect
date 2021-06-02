/*
using Atlas.Database;

/// <summary>
/// Файл содержит реализацию работы с базой данных визуалзаций серверной части.
/// Доступен только в серверном коде.
/// </summary>
namespace Atlas.Server
{
    public class XDBProjectRenderManager
    {
        public static XDBProjectRender CreateRenderJob(XRequestContext context, XDBProject project, JOB_REQUEST job_request, bool auto_commit)
        {
            if (long.TryParse(job_request.corporate_id, out long company_uid))
            {
                RenderJobType job_type = string.IsNullOrEmpty(job_request.webgl_uniq) ? RenderJobType.REGULAR : RenderJobType.WEBGL;

                string global_uniq = Hash.Sha256(job_request.project_settings.RNJB_uniq + "@" + project.CompanyUid + "#" + project.ProjectUniq);
                XDBProjectRender project_render = context.Connection.Database.Table<XDBProjectRender>()
                    .Where(x => x.GlobalUniq == global_uniq)
                    .FirstOrDefault();

                if (project_render == null)
                {
                    project_render = new XDBProjectRender();
                    project_render.GlobalUniq = global_uniq;
                    project_render.RenderJobUniq = job_request.RNJB_uniq;
                    project_render.RenderJobType = (int)job_type;
                    project_render.RenderQuality = job_request.project_settings.render_quality;
                    project_render.ProjectUniq = job_request.project_settings.project_uniq_id;
                    project_render.CompanyUid = context.UserInfo.Company.Default.CompanyUid;
                    project_render.BranchUid = context.UserInfo.Company.Default.BranchUid;
                    project_render.ProjectVersion = job_request.webgl_targetversion;
                    project_render.SenderType = (int)XDBCreatorType.INTERNAL;
                    project_render.SenderUniq = context.UserInfo.Profile.MemberUniq;
                    project_render.SenderName = context.UserInfo.Profile.FullName;
                    project_render.WebglUniq = job_request.webgl_uniq;
                    project_render.WebglShorlink = "";
                    project_render.DateCreated = XUtils.GetUnixTimestamp();
                    project_render.DateFinished = 0;
                    project_render.ViewsCount = 0;
                    project_render.DateViewed = 0;

                    if (auto_commit)
                        context.Connection.Insert(project_render);
                }
                return project_render;
            }
            return null;
        }

        public static XDBProjectRender CompleteRenderJob(XDatabaseAdapter connection, XDBProject project, JOB_REQUEST job_request, bool auto_commit)
        {
            RenderJobType job_type = string.IsNullOrEmpty(job_request.webgl_uniq) ? RenderJobType.REGULAR : RenderJobType.WEBGL;

            string global_uniq = Hash.Sha256(job_request.project_settings.RNJB_uniq + "@" + project.CompanyUid + "#" + project.ProjectUniq);
            XDBProjectRender project_render = connection.Database.Table<XDBProjectRender>()
                .Where(x => x.GlobalUniq == global_uniq)
                .FirstOrDefault();

            if (project_render != null)
            {
                project_render.WebglUniq = job_request.webgl_uniq;
                project_render.WebglShorlink = "";
                project_render.DateFinished = XUtils.GetUnixTimestamp();

                if (auto_commit)
                    connection.Update(project_render);

                return project_render;
            }
            else
            {
                XLogger.Log("[Database] Render not found");
            }
            return null;
        }

        public static XDBProjectRender CancelRenderJob(XDatabaseAdapter connection, XDBProject project, JOB_REQUEST job_request, bool auto_commit)
        {
            RenderJobType job_type = string.IsNullOrEmpty(job_request.webgl_uniq) ? RenderJobType.REGULAR : RenderJobType.WEBGL;

            string global_uniq = Hash.Sha256(job_request.project_settings.RNJB_uniq + "@" + project.CompanyUid + "#" + project.ProjectUniq);
            XDBProjectRender project_render = connection.Database.Table<XDBProjectRender>()
                .Where(x => x.GlobalUniq == global_uniq)
                .FirstOrDefault();

            if (project_render != null)
            {
                project_render.DateCanceled = XUtils.GetUnixTimestamp();

                if (auto_commit)
                    connection.Update(project_render);

                return project_render;
            }
            else
            {
                XLogger.Log("[Database] Render not found");
            }
            return null;
        }
    }
}
*/