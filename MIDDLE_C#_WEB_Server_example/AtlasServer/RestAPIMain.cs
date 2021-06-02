using System;
using System.Collections.Specialized;
using AtlasServer.RestAPI;
using AtlasServer.SeсureAPI;
using AtlasServer.NodeAPI;
using AtlasServer.External;

namespace AtlasServer
{
    public class RestAPIMain
    {
        private NameValueCollection args;

        public RestApiHandler routeByURI(String URI)
        {
            Uri objectURI = new Uri(URI);
            string[] pathsegments = objectURI.Segments;

            Logger.WriteLog("Info:AtlasServer.RestAPIMain.RestApiHandler:URI=" + URI);

            for (int i = 0; i < pathsegments.Length; i++)
            {
                pathsegments[i] = pathsegments[i].Replace("/", "").ToUpper();
            }

            var hash = new System.Collections.Generic.HashSet<string>(pathsegments);

            switch (hash)
            {
                case var firstLevel when hash.Contains("SECURE"):
                  
                    if (!RestApiHandler.checkSuccessSecure(objectURI))
                    {                     
                        return new SecureFail("Incorrect secure");
                    } 

                    switch (hash)
                    {
                        case var secondtLevel when hash.Contains("TEST_SECURE"):
                            return new TestSecure();
                        default:
                            Logger.WriteLog("Error:Secure route not found:AtlasServer.RestAPIMain.RestApiHandler:URI=" + URI);
                            return new SecureFail("Secure route not found.");
                    }

                case var firstLevel when hash.Contains("NODE"):
                    switch (hash)
                    {
                        case var secondtLevel when hash.Contains("TEST_NODE"):
                            return new TestNode();                     
                    }
                break;

                case var firstLevel when hash.Contains("API"):
                    switch (hash)
                    {
                        case var secondtLevel when hash.Contains("COMPANIES_LIST_WITH_CATALOGUES"):
                            return new CompaniesListWithCatalogues();

                        case var secondtLevel when hash.Contains("CATALOGUES_BY_COMPANY_UID"):
                            return new СataloguesByCompanyUid();

                        case var secondtLevel when hash.Contains("CATALOGUES_PRODUCTS_BY_CATALOGUE_UID"):
                            return new СataloguesProductsByCatalogueUid();

                        case var secondtLevel when hash.Contains("CATALOGUES_PRODUCTS"):
                            return new CataloguesProducts();

                        case var secondtLevel when hash.Contains("CATALOGUES_MATERIALS_REFERENCES_BY_PRODUCT_UNIQ"):
                            return new CataloguesMaterialsReferencesByProductUniq();

                        case var secondtLevel when hash.Contains("PLAYER_CONFIG"):
                            return new PlayerConfig();

                        case var secondtLevel when hash.Contains("BUTTON_HELP"):
                            return new ButtonHelp();

                        case var secondtLevel when hash.Contains("BUTTON_CALC"):
                            return new ButtonCalc();

                        case var secondtLevel when hash.Contains("BUTTON_CUSTOMIZE"):
                            return new ButtonCustomize();

                        case var secondtLevel when hash.Contains("CATALOGUES_HIERARCHY"):
                            return new CataloguesHierarchy();

                        case var secondtLevel when hash.Contains("CATALOGUES_PRODUCTS_GROUPS_BY_HIERARCHY"):
                            return new CataloguesProductsGroupsByHierarchy();

                        case var secondtLevel when hash.Contains("PRODUCT_SEARCH"):
                            return new ProductSearch();
                    }
                break;
            }

            Logger.WriteLog("Error:Route not found:AtlasServer.RestAPIMain.RestApiHandler:URI=" + URI);

            return new RestApiHandler();

         //   Console.WriteLine(String.Join(",", pathsegments));

          
        }
    }
}
