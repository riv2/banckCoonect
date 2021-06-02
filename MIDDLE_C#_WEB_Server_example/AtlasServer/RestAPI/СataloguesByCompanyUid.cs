using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class СataloguesByCompanyUid : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            String response_catalogues = this.doRequest("catalogues", query);

            dynamic responseObj_catalogues = JsonConvert.DeserializeObject(response_catalogues);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_catalogues._catalogues;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
