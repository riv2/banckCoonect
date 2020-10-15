using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class PlayerConfig : RestApiHandler
    {
        override public string Get(string URI)
        {
            string testJsonConfig = "{\"button_help\":\"main_control_child_block_left\",\"button_calc\":\"main_control_child_block_right\",\"screen_shot\":\"main_control_child_block_right\",\"button_customize\":\"main_control_child_block_left\", \"full_screen\":\"main_control_child_block_right\"}";

            dynamic responseObj_Config = JsonConvert.DeserializeObject(testJsonConfig);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_Config;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
