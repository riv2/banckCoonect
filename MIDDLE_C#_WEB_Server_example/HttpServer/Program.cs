using System;
using System.IO;
using System.Net;
using AtlasServer;
using AtlasServer.Configs;
using VRNext;

namespace HttpServer
{
    class Program
    {
        static void Main(string[] args)
        {
            string currentDir = Directory.GetCurrentDirectory() + @"\";
            XArguments xArguments = ParseArgs(args);

            if(string.IsNullOrEmpty(xArguments.Get("ConfigPath")))
            {
                xArguments.Set("ConfigPath", currentDir + "config.ini");
            }

            XConfig.Init(xArguments.Get("ConfigPath"));

            // Create a new HTTP server
            var server = new HttpAtlasServer(IPAddress.Any, XConfig.PORT);

            // Start the server
            Console.Write("Server starting...");
            server.Start();
            Console.WriteLine("Done!");

            Console.WriteLine("Press Enter to stop the server or '!' to restart the server...");

            // Perform text input
            for (;;)
            {
                string line = Console.ReadLine();
                if (string.IsNullOrEmpty(line))
                    break;

                // Restart the server
                if (line == "!")
                {
                    Console.Write("Server restarting...");
                    server.Restart();
                    Console.WriteLine("Done!");
                }
            }

            // Stop the server
            Console.Write("Server stopping...");
            server.Stop();
            Console.WriteLine("Done!");
        }

        private static XArguments ParseArgs(string[] args)
        {
            Console.Out.WriteLine("Parsing arguments...");
            XArguments xArgsuments = new XArguments();
            for (int i = 0; i < args.Length; i++)
            {
                if (i + 1 < args.Length)
                {
                    if (args[i] == "-config")
                    {
                        xArgsuments.Set("ConfigPath", args[i + 1]);
                    }
                }
            }
            Console.Out.WriteLine(xArgsuments.ToString());
            return xArgsuments;
        }
    }
}
