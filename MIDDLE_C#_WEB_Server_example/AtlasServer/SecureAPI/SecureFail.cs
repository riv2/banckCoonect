using System;
using Newtonsoft.Json;

namespace AtlasServer.SeсureAPI
{
    class SecureFail : RestApiHandler
    {
        string MSG = "Unknown secure error";

        public override string Get(string URL)
        {
            ResponseObj responseObj = new ResponseObj();

            responseObj.error = true;
            responseObj.msg = this.MSG;
            responseObj.data_array = new string[0];

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }

        public SecureFail(string MSG)
        {
            this.MSG = MSG;
        }
    }

}
