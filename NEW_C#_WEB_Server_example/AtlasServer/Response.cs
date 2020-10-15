using Newtonsoft.Json;

namespace AtlasServer
{
    public class ResponseObj
    {
        public bool error { get; set; }
        public string msg { get; set; }
        public object data_array { get; set; }
    }
    class ResponseObj_catalogues_products_by_catalogue_uid
    {
        public object _catalogues_products_groups { get; set; }
        public object _catalogues_groups { get; set; }
        public object _catalogues_hierarchy { get; set; }
    }

    class ResponseObj_catalogues_resources
    {
        [JsonProperty("uniq")]
        public string uniq { get; set; }

        [JsonProperty("path_source")]
        public string path_source { get; set; }

        [JsonProperty("checksum")]
        public string checksum { get; set; }

        [JsonProperty("date_modified")]
        public int date_modified { get; set; }

        [JsonProperty("size_factor")]
        public int size_factor { get; set; }
    }

    class responseObj_catalogues_products
    {
        [JsonProperty("uniq")]
        public string uniq { get; set; }

        [JsonProperty("member_uniq")]
        public string member_uniq { get; set; }

        [JsonProperty("manufactorer")]
        public string manufactorer { get; set; }

        [JsonProperty("dim_x")]
        public int dim_x { get; set; }

        [JsonProperty("dim_y")]
        public int dim_y { get; set; }

        [JsonProperty("dim_z")]
        public int dim_z { get; set; }

        [JsonProperty("flags")]
        public int flags { get; set; }

        [JsonProperty("status")]
        public int status { get; set; }

        [JsonProperty("date_modified")]
        public int date_modified { get; set; }

        [JsonProperty("date_deleted")]
        public int date_deleted { get; set; }

        [JsonProperty("catalogue_uid")]
        public int catalogue_uid { get; set; }

        [JsonProperty("name")]
        public string name { get; set; }

    }
}
