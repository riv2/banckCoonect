using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class ButtonCustomize : RestApiHandler
    {
        override public string Get(string URI)
        {
            string testJsonCustomize = "{\"button_customize\":\"I from REST calc -you uniq = " + URI + "\"}";

            dynamic responseObj_Config = JsonConvert.DeserializeObject(testJsonCustomize);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_Config;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
