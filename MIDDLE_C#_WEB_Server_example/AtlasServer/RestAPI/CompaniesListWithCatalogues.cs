using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class CompaniesListWithCatalogues : RestApiHandler
    {
        override public string Get(string URI)
        {
            String response_catalogues = this.doRequest("catalogues", "!company_uid=0");
            dynamic responseObj_catalogues = JsonConvert.DeserializeObject(response_catalogues);

            String request_companies = "";

            foreach (var item in responseObj_catalogues._catalogues)
            {
                request_companies = request_companies + "&uid[]=" + item.company_uid;
            }

            request_companies = request_companies.Substring(1);

            String response_companies = this.doRequest("companies", request_companies);

            dynamic responseObj_companies = JsonConvert.DeserializeObject(response_companies);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_companies._companies;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
