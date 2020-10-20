using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    class СataloguesProductsByCatalogueUid : RestApiHandler
    {
        public override string Get(string URI)
        {
            ResponseObj_catalogues_products_by_catalogue_uid responseObj_catalogues_products_by_catalogue_uid = new ResponseObj_catalogues_products_by_catalogue_uid();

            string query = this.getQueryStringFromURI(URI);

            query = query + "&date_deleted=0";

            String response_catalogues_hierarchy = this.doRequest("catalogues_hierarchy", query);
            dynamic responseObj_catalogues_hierarchy = JsonConvert.DeserializeObject(response_catalogues_hierarchy);
            responseObj_catalogues_products_by_catalogue_uid._catalogues_hierarchy = responseObj_catalogues_hierarchy._catalogues_hierarchy;

            String request_catalogues_groups = "";

            foreach (var item in responseObj_catalogues_hierarchy._catalogues_hierarchy)
            {
                request_catalogues_groups = request_catalogues_groups + "&hierarchy_uniq[]=" + item.uniq;
            }

       //     request_catalogues_groups = request_catalogues_groups.Substring(1);

            String response_catalogues_groups = this.doRequest("catalogues_groups", request_catalogues_groups);

            dynamic responseObj_catalogues_groups = JsonConvert.DeserializeObject(response_catalogues_groups);
            responseObj_catalogues_products_by_catalogue_uid._catalogues_groups = responseObj_catalogues_groups._catalogues_groups;

            String request_catalogues_products_groups = "";

            foreach (var item in responseObj_catalogues_groups._catalogues_groups)
            {
                request_catalogues_products_groups = request_catalogues_products_groups + "&group_uniq[]=" + item.uniq;
            }

            String response_catalogues_products_groups = this.doRequest("catalogues_products_groups", request_catalogues_products_groups);

            dynamic responseObj_catalogues_products_groups = JsonConvert.DeserializeObject(response_catalogues_products_groups);
            responseObj_catalogues_products_by_catalogue_uid._catalogues_products_groups = responseObj_catalogues_products_groups._catalogues_products_groups;

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_catalogues_products_by_catalogue_uid;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
