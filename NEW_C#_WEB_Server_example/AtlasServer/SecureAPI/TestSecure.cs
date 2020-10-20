using System;
using Newtonsoft.Json;

namespace AtlasServer.SeсureAPI
{
    class TestSecure : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "Success Secure";
            responseObj.data_array = new string[0];

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }

}
