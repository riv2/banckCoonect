using Newtonsoft.Json;
using NetCoreServer;
using System.Web;
using System.Text;
using AtlasServer.Configs;
using System.IO;
using AtlasServer.External;

namespace AtlasServer
{
    public class RestApiHandler
    {
        virtual public string Get(string URI)
        {
            return Empty(URI);
        }
        virtual public string Post(string URI)
        {
            return Empty(URI);
        }
        virtual public string Put(string URI)
        {
            return Empty(URI);
        }
        virtual public string Delete(string URI)
        {
            return Empty(URI);
        }
        virtual public string Empty(string URI)
        {
            ResponseObj responseObj = new ResponseObj();

            responseObj.error = true;
            responseObj.msg = "Method not found";
            responseObj.data_array = null;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            Logger.WriteLog("Notice:AtlasServer.RestApiHandler.Empty:URI=" + URI);

            return responseJson;
        }

        public string doRequest(string method, string query)
        {
            string address = "127.0.0.1";
            int port = 8000;
            string responseType = "json";
            string requestString = "/" + responseType + "/" + method + "/?" + query;
            var client = new HttpClientEx(address, port);
            var response = client.SendGetRequest(requestString).Result;
            string responseString = response.Body;
            return responseString;
        }

        public string getQueryStringFromURI(string URI)
        {
            string[] querySegments = URI.Split('?');
            string querySegment    = querySegments[1];
            return querySegment;
        }


        public static bool checkSuccessSecure(System.Uri objectURI)
        {
            string secure = HttpUtility.ParseQueryString(objectURI.Query).Get("secure");
            string secretKey = XConfig.SECRET_KEY;

            string md5String   = "";
            string md5KeyValue = "";

            if (!ExternalLibs.IsNullOrEmpty(secure) && !ExternalLibs.IsNullOrEmpty(secretKey))
            {
                foreach (var item in objectURI.Query.TrimStart('?').Split('&'))
                {
                    var subStrings = item.Split('=');
                    var key        = subStrings[0];
                    var value      = subStrings[1];

                    if (key != "secure")
                    {
                        md5KeyValue = ExternalLibs.CreateMD5(key + value);
                        md5String   = md5String + md5KeyValue;
                    }
                  
                }

                md5String = ExternalLibs.CreateMD5(md5String.ToLower() + secretKey);

                if (secure.ToUpper() == md5String)
                {
                    return true;
                } else 
                {
                    Logger.WriteLog("Error:secure disagree:AtlasServer.RestApiHandler.checkSuccessSecure:secure=" + secure + "md5String=" + md5String);
                }
            } else
            {
                Logger.WriteLog("Error:secure value:AtlasServer.RestApiHandler.checkSuccessSecure:secure=" + secure + "secretKey=" + secretKey);
            }

            return false;

            // Console.WriteLine(String.Join(" | ", "5467"));

        }
    }
}
