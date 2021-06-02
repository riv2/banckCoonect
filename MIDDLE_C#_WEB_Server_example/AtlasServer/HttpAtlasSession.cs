using System;
using System.Net.Sockets;
using NetCoreServer;

namespace AtlasServer
{
    public class HttpAtlasSession : HttpSession
    {
        public HttpAtlasSession(HttpServer server) : base(server) { }

        protected override void OnReceivedRequest(HttpRequest request)
        {

            RestAPIMain restAPIMain = new RestAPIMain();
            RestApiHandler restAPIhandler = null;

            if (request.Method == "HEAD")
            {
                SendResponseAsync(Response.MakeHeadResponse());
            }
            else if (request.Method == "GET")
            {
                string url = "http://localhost/" + request.Url;
                restAPIhandler = restAPIMain.routeByURI(url);
                SendResponseAsync(Response.MakeGetResponse(restAPIhandler?.Get(url), "application/json; charset=UTF-8"));
            }
            else if ((request.Method == "POST") || (request.Method == "PUT"))
            {
                // TODO 
            }
            else if (request.Method == "DELETE")
            {
                // TODO
            }
            else
            {
                SendResponseAsync(Response.MakeErrorResponse("Unsupported HTTP method: " + request.Method));
            }
        }

        protected override void OnReceivedRequestError(HttpRequest request, string error)
        {
            Console.WriteLine($"Request error: {error}");
        }

        protected override void OnError(SocketError error)
        {
            Console.WriteLine($"HTTP session caught an error: {error}");
        }
    }
}
