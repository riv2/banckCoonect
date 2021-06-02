using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class ProductSearch : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            String response_product_search = this.doRequest("product_search", query);

            dynamic responseObj_product_search = JsonConvert.DeserializeObject(response_product_search);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_product_search._products;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}