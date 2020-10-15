using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class ButtonHelp : RestApiHandler
    {
        override public string Get(string URI)
        {
            string testJsonHelp = "{\"button_help\":\"I from REST help\"}";

            dynamic responseObj_Config = JsonConvert.DeserializeObject(testJsonHelp);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_Config;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}