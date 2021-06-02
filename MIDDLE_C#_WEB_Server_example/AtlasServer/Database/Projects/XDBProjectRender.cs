using System;
using Newtonsoft.Json;

namespace Atlas.Database
{
    public enum RenderJobType { REGULAR, WEBGL };

    [Serializable]

    public class XDBProjectRender
    {
        [JsonProperty("global_uniq")]
        public string GlobalUniq { set; get; }

        [JsonProperty("render_job_uniq")]
        public string RenderJobUniq { set; get; }

        [JsonProperty("render_job_type")]
        public int RenderJobType { set; get; }

        [JsonProperty("render_quality")]
        public int RenderQuality { set; get; }

        [JsonProperty("project_uniq")]
        public string ProjectUniq { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("branch_uid")]
        public long BranchUid { set; get; }

        [JsonProperty("project_version")]
        public string ProjectVersion { set; get; }

        [JsonProperty("sender_type")]
        public int SenderType { set; get; }

        [JsonProperty("sender_uniq")]
        public string SenderUniq { set; get; }

        [JsonProperty("sender_name")]
        public string SenderName { set; get; }

        [JsonProperty("webgl_uniq")]
        public string WebglUniq { set; get; }

        [JsonProperty("webgl_shortlink")]
        public string WebglShorlink { set; get; }

        [JsonProperty("date_created")]
        public long DateCreated { set; get; }

        [JsonProperty("date_finished")]
        public long DateFinished { set; get; }

        [JsonProperty("date_canceled")]
        public long DateCanceled { set; get; }

        [JsonProperty("views_count")]
        public int ViewsCount { set; get; }

        [JsonProperty("date_viewed")]
        public long DateViewed { set; get; }

        public XDBProjectRender()
        {
        }
    }
}
