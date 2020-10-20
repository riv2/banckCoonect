using System;
using Newtonsoft.Json;

namespace Atlas.Database
{
    [Serializable]

    public class XDBAtlasWebglDataCatalogue
    {
        [JsonProperty("webgl_key")]
        public string WebglKey { set; get; }

        [JsonProperty("catalogue_uniq")]
        public string CatalogueUniq { set; get; }
    }
}
