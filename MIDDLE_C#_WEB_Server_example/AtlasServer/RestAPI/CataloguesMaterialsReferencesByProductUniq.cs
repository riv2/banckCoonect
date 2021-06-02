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

            JArray arrResponse = new JArray();

            foreach (var item in responseObj_catalogues_materials_references._catalogues_materials_references)
            {
                JObject itemProd = new JObject();

                for (int i = 0; i < _catalogues_resources.Count; i++)
                {
                    if ((string)item["reference_uniq"] == (string)_catalogues_resources[i]["uniq"])
                    {
                        itemProd.Add("checksum", _catalogues_resources[i]["checksum"]);
                    }
                }

                for (int i = 0; i < _catalogues_products.Count; i++)
                {
                    if ((string)item["material_uniq"] == (string)_catalogues_products[i]["uniq"])
                    {
                        itemProd.Add("catalogue_uid", _catalogues_products[i]["catalogue_uid"]);
                        itemProd.Add("manufactorer", _catalogues_products[i]["manufactorer"]);
                        itemProd.Add("product_uniq", _catalogues_products[i]["uniq"]);
                        itemProd.Add("dim_x", _catalogues_products[i]["dim_x"]);
                        itemProd.Add("dim_y", _catalogues_products[i]["dim_y"]);
                        itemProd.Add("dim_z", _catalogues_products[i]["dim_z"]);
                        itemProd.Add("name", _catalogues_products[i]["name"]);
                        itemProd.Add("flags", _catalogues_products[i]["flags"]);

                        if (_companies_store != null)
                        {
                            for (int n = 0; n < _companies_store.Count; n++)
                            {
                                if ((string)_catalogues_products[i]["uniq"] == (string)_companies_store[n]["product_uniq"])
                                {
                                    itemProd.Add("article", _companies_store[n]["article"]);
                                    itemProd.Add("currency", _companies_store[n]["currency"]);
                                    itemProd.Add("calculation", _companies_store[n]["calculation"]);
                                    itemProd.Add("units", _companies_store[n]["units"]);
                                    itemProd.Add("price", _companies_store[n]["price"]);
                                    itemProd.Add("available", _companies_store[n]["available"]);
                                    itemProd.Add("company_uid", _companies_store[n]["company_uid"]);
                                }
                            }
                        }

                    }
                }

                arrResponse.Add(itemProd);
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
