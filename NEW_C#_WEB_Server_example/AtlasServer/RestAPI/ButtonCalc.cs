using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class ButtonCalc : RestApiHandler
    {
        override public string Get(string URI)
        {
            string testJsonCalc = "{\"button_calc\":\"I from REST calc -you uniq = " + URI + "\"}";

            dynamic responseObj_Config = JsonConvert.DeserializeObject(testJsonCalc);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_Config;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
