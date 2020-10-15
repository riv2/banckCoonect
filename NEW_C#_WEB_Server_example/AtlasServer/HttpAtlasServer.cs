using System;
using System.Net;
using System.Net.Sockets;
using NetCoreServer;

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
}
