using System;
using System.Collections.Generic;

namespace VRNext
{
    public interface IXLoggerOutput
    {
        void Dump();
        void Log(string message);
        void LogWarning(string message);
        void LogError(string message);
        void LogException(string message, string stackTrace);
    };

    public class XLogger 
    {
        private static XLogger instance = null;
        private List<IXLoggerOutput> logger_output = new List<IXLoggerOutput>();

        void Update()
        {
            foreach (IXLoggerOutput output in logger_output)
            {
                output.Dump();
            }
        }

        public static XLogger GetInstance()
        {
            if(instance == null)
            {
                instance = new XLogger();
            }
            return instance;
        }

        private static string GetDate()
        {
            // System.DateTime localDate = System.DateTime.Now;
            // var culture = new System.Globalization.CultureInfo("ru-RU");
            // return "["+ localDate.ToString(culture).Replace(":", "-") + "]";

            return "[" + System.DateTime.Now.ToString("hh:mm:ss:fff") + "]" ;
        }

        public static void AddLoggerOutput(IXLoggerOutput output)
        {
            GetInstance().logger_output.Add(output);
        }

        public static void Log(string message, bool console = false)
        {
            message = GetDate() + ": " + message;
            Console.Out.WriteLine(message);

            if (!console)
            {
                foreach (IXLoggerOutput output in GetInstance().logger_output)
                {
                    output.Log(message);
                }
            }
        }

        public static void LogWarning(string message, bool console = false)
        {
            message = GetDate() + ": " + message;
            Console.Out.WriteLine(message);

            if (!console)
            {
                foreach (IXLoggerOutput output in GetInstance().logger_output)
                {
                    output.LogWarning(message);
                }
            }
        }

        public static void LogError(string message, bool console = false)
        {
            message = GetDate() + ": " + message;
            Console.Out.WriteLine(message);

            if (!console)
            {
                foreach (IXLoggerOutput output in GetInstance().logger_output)
                {
                    output.LogError(message);
                }
            }
        }
    
        public static void LogException(System.Exception exception, bool console = false)
        {
            string message = GetDate() + ": " + exception.Message;
            string stackTrace = GetDate() + ": " + exception.StackTrace;
            Console.Out.WriteLine(message);
            Console.Out.WriteLine(stackTrace);

            if (!console)
            {
                foreach (IXLoggerOutput output in GetInstance().logger_output)
                {
                    output.LogException(message, stackTrace);
                }
            }
        }
    }
}