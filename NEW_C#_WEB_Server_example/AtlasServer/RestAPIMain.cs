using System;
using System.Linq;
using System.Collections.Generic;
using NetCoreServer;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System.Collections.Specialized;
using AtlasServer.RestAPI;

namespace AtlasServer
{
    public class RestAPIMain
    {
        private NameValueCollection args;

        public RestApiHandler routeByURI(String URI)
        {
            Uri objectURI = new Uri(URI);
            string[] pathsegments = objectURI.Segments;
            var hash = new System.Collections.Generic.HashSet<string>(pathsegments);

            if (hash.Contains("companies_list_with_catalogues/"))
            {
                return new CompaniesListWithCatalogues();
            }

            if (hash.Contains("catalogues_by_company_uid/"))
            {
                return new СataloguesByCompanyUid();
            }

            if (hash.Contains("catalogues_products_by_catalogue_uid/"))
            {
                return new СataloguesProductsByCatalogueUid();
            }

            if (hash.Contains("catalogues_products_by_uniq/"))
            {
                return new CataloguesProductsByUniq();
            }

            if (hash.Contains("catalogues_materials_references_by_product_uniq/"))
            {
                return new CataloguesMaterialsReferencesByProductUniq();
            }

            if (hash.Contains("player_config/"))
            {
                return new PlayerConfig();
            }

            if (hash.Contains("button_help/"))
            {
                return new ButtonHelp();
            }

            if (hash.Contains("button_calc/"))
            {
                return new ButtonCalc();
            }

            if (hash.Contains("button_customize/"))
            {
                return new ButtonCustomize();
            }

            if (hash.Contains("catalogues_hierarchy/"))
            {
                return new CataloguesHierarchy();
            }

            if (hash.Contains("catalogues_products_groups_by_hierarchy/"))
            {
                return new CataloguesProductsGroupsByHierarchy();
            }

            if (hash.Contains("product_search/"))
            {
                return new ProductSearch();
            }

            return new RestApiHandler();
        }
    }
}
