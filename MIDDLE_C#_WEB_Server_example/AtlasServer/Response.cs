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
}
