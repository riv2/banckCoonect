using System;
using Newtonsoft.Json;

namespace AtlasServer.RestAPI
{
    public class PlayerConfig : RestApiHandler
    {
        public override string Get(string URI)
        {
            string query = this.getQueryStringFromURI(URI);

            String response_player_config = this.doRequest("player_config", query);

            dynamic responseObj_player_config = JsonConvert.DeserializeObject(response_player_config);

            ResponseObj responseObj = new ResponseObj();

            responseObj.error = false;
            responseObj.msg = "";
            responseObj.data_array = responseObj_player_config._player_config;

            string responseJson = JsonConvert.SerializeObject(responseObj);

            return responseJson;
        }
    }
}
