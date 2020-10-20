using System;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Объект базы данных, описывающий информацию об использовании проекта
    /// </summary>
    [Serializable]

    public class XDBProjectUsage
    {
        [JsonProperty("global_uniq")]
        public string GlobalUniq { set; get; }

        [JsonProperty("company_uid")]
        public long CompanyUid { set; get; }

        [JsonProperty("branch_uid")]
        public long BranchUid { set; get; }

        [JsonProperty("project_uniq")]
        public string ProjectUniq { set; get; }

        [JsonProperty("usage_type")]
        public int UsageType { set; get; }

        [JsonProperty("usage_time")]
        public float UsageTime { set; get; }

        [JsonProperty("trigger_action")]
        public string TriggerAction { set; get; }

        [JsonProperty("creator_type")]
        public int CreatorType { set; get; }

        [JsonProperty("creator_uniq")]
        public string CreatorUniq { set; get; }

        [JsonProperty("creator_name")]
        public string CreatorName { set; get; }

        [JsonProperty("date_created")]
        public long DateCreated { set; get; }

        public XDBProjectUsage()
        {
        }
    }
}

