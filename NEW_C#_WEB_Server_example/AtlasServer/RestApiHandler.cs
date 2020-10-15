using System;
using Newtonsoft.Json;
using NetCoreServer;

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
            string querySegment = querySegments[1];
            return querySegment;
        }
    }
}
