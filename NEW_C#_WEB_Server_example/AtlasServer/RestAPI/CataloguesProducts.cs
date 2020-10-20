using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class CataloguesProducts : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            String response_catalogues_products = this.doRequest("catalogues_products", query);

            dynamic responseObj_catalogues_products = JsonConvert.DeserializeObject(response_catalogues_products);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_catalogues_products._catalogues_products;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
