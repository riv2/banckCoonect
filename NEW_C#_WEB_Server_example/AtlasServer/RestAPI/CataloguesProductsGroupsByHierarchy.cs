using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    class CataloguesProductsGroupsByHierarchy : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            String response_catalogues_groups = this.doRequest("catalogues_groups", query);

            dynamic responseObj_catalogues_groups = JsonConvert.DeserializeObject(response_catalogues_groups);

            string queryCataloguesProductsGroups = "";

            if (responseObj_catalogues_groups._catalogues_groups != null)
            {
                foreach (var item in responseObj_catalogues_groups._catalogues_groups)
                {
                    queryCataloguesProductsGroups = queryCataloguesProductsGroups + "&group_uniq[]=" + item.uniq;
                }
            }       

            String response_catalogues_products_groups = this.doRequest("catalogues_products_groups", queryCataloguesProductsGroups);
            dynamic responseObj_catalogues_products_groups = JsonConvert.DeserializeObject(response_catalogues_products_groups);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_catalogues_products_groups._catalogues_products_groups; 

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
