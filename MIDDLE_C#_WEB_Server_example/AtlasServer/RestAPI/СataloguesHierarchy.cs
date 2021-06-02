using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class CataloguesHierarchy : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            String response_catalogues_hierarchy = this.doRequest("catalogues_hierarchy", query);

            dynamic responseObj_catalogues_hierarchy = JsonConvert.DeserializeObject(response_catalogues_hierarchy);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_catalogues_hierarchy._catalogues_hierarchy;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
