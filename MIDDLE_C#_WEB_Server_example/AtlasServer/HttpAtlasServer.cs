using System;
using System.Net;
using System.Net.Sockets;
using NetCoreServer;
using AtlasServer.Cache;
using AtlasServer.External;

namespace AtlasServer
{
    public class HttpAtlasServer : HttpServer
    {
        public HttpAtlasServer(IPAddress address, int port) : base(address, port) { }

        protected override TcpSession CreateSession() { return new HttpAtlasSession(this); }

        protected override void OnError(SocketError error)
        {
            Console.WriteLine($"HTTP session caught an error: {error}");
        }
    }

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
                String ResponseFromMethod = CacheLib.getValue(url, restAPIhandler?.Get(url));

                Logger.WriteLog("Info:AtlasServer.HttpAtlasSession.OnReceivedRequest:request=" + url + "|Response=" + ResponseFromMethod);

                SendResponseAsync(Response.MakeGetResponse(ResponseFromMethod, "application/json; charset=UTF-8"));
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
