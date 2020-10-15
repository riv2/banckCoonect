using System;
using System.Linq;
using System.Collections.Generic;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace AtlasServer.RestAPI
{
    class CataloguesMaterialsReferencesByProductUniq : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);
            string query_companies_store_by_product_uniq = query;

            query = query + "&material_channel=0";
            query = query.Replace("product_uniq", "material_uniq");

            String response_catalogues_materials_references = this.doRequest("catalogues_materials_references", query);
            dynamic responseObj_catalogues_materials_references = JsonConvert.DeserializeObject(response_catalogues_materials_references);

            string queryResources = "";
            string queryProducts  = "";

            foreach (var item in responseObj_catalogues_materials_references._catalogues_materials_references)
            {
                queryResources = queryResources + "&uniq[]=" + item.reference_uniq;
                queryProducts = queryProducts + "&uniq[]=" + item.material_uniq;
            }

            String response_catalogues_resources     = this.doRequest("catalogues_resources", queryResources);
            JObject responseObj_catalogues_resources = JObject.Parse(response_catalogues_resources);
            JArray _catalogues_resources             = (JArray)responseObj_catalogues_resources["_catalogues_resources"];

            String response_catalogues_products     = this.doRequest("catalogues_products", queryProducts);
            JObject responseObj_catalogues_products = JObject.Parse(response_catalogues_products);
            JArray _catalogues_products             = (JArray)responseObj_catalogues_products["_catalogues_products"];

            String response_companies_store_by_product_uniq = this.doRequest("companies_store_by_product_uniq", query_companies_store_by_product_uniq);
            JObject responseObj_companies_store_by_product_uniq = JObject.Parse(response_companies_store_by_product_uniq);
            JArray _companies_store = (JArray)responseObj_companies_store_by_product_uniq["_companies_store"];

            int i = 0;
            JArray arrResponse = new JArray();
            IEnumerator<JToken> it = _catalogues_resources.GetEnumerator();

            while (it.MoveNext())
            {
                JObject prod = (JObject)_catalogues_products[i];
                JObject item = (JObject)it.Current;
                item.Add("catalogue_uid", prod["catalogue_uid"]);
                item.Add("manufactorer", prod["manufactorer"]);
                item.Add("product_uniq", prod["uniq"]);                
                item.Add("dim_x", prod["dim_x"]);
                item.Add("dim_y", prod["dim_y"]);
                item.Add("dim_z", prod["dim_z"]);
                item.Add("name", prod["name"]);
                item.Add("flags", prod["flags"]);

                if (_companies_store != null)
                {
                    for (int n = 0; n < _companies_store.Count; n++)
                    {
                        if ((string)prod["uniq"] == (string)_companies_store[n]["product_uniq"])
                        {
                            item.Add("article", _companies_store[n]["article"]);
                            item.Add("currency", _companies_store[n]["currency"]);
                            item.Add("calculation", _companies_store[n]["calculation"]);
                            item.Add("units", _companies_store[n]["units"]);
                            item.Add("price", _companies_store[n]["price"]);
                            item.Add("available", _companies_store[n]["available"]);
                            item.Add("company_uid", _companies_store[n]["company_uid"]);
                        }
                    }
                }

                arrResponse.Add(item);
                i++;
            }

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = arrResponse;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
