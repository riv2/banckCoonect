using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using AtlasServer.External;

namespace AtlasServer.Cache
{
    public class CacheLib
    {
        public static string getValue(string url, string value = null)
        {

            Uri objectURI = new Uri(url);
            string[] pathsegments = objectURI.Segments;

            Dictionary<string, string> values = new Dictionary<string, string>();
            String cacheFileName = "main.data";
            Boolean useFromCashe = false;

            for (int i = 0; i < pathsegments.Length; i++)
            {
                pathsegments[i] = pathsegments[i].Replace("/", "").ToUpper();
            }

            var hash = new System.Collections.Generic.HashSet<string>(pathsegments);
            string name = ExternalLibs.CreateMD5(url);

            switch (hash) // что кешируем
            {
                case var route when hash.Contains("NODE"):
                    useFromCashe = true;
                    break;
            }

            if (useFromCashe)
            {
                if (System.IO.File.Exists(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location) + "\\" + cacheFileName))
                {
                    values = File.ReadLines(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location) + "\\" + cacheFileName)
                    .Where(line => (!String.IsNullOrWhiteSpace(line)))
                    .Select(line => line.Split(new char[] { '=' }, 2, 0))
                    .ToDictionary(parts => parts[0].Trim(), parts => parts.Length > 1 ? parts[1].Trim() : null);
                }

                if (values != null && values.ContainsKey(name))
                {
                    Logger.WriteLog("Cache:from useFromCashe|ContainsKey:url=" + url + "|name=" + name);
                    return values[name];
                }
                else
                {
                    StreamWriter f = new StreamWriter(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location) + "\\" + cacheFileName, true);
                    f.WriteLine(name + "=" + value);
                    f.Close();
                    Logger.WriteLog("Cache:from useFromCashe|not ContainsKey:url=" + url + "|name=" + name);
                    return value;
                }
            }

            Logger.WriteLog("Cache:not from useFromCashe:url=" + url + "|name=" + name);
            return value;

        }      

    }
}

