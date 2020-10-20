using System;
using System.IO;
using System.Collections.Generic;

namespace VRNext
{
    public class XLoggerWriter: IXLoggerOutput
    {
        private List<string> MainLog = new List<string>();
        private List<string> ErrorLog = new List<string>();
        private bool IsLoggerReady = false;

        public string PathFileMain { private set; get; }
        public string PathFileError { private set; get; }

        public static string PathOldMainFile { private set; get; }
        public static string PathOldErrorFile { private set; get; }

        public void SetPaths(string pathFileMain, string pathFileError)
        {
            this.PathFileMain = pathFileMain;
            this.PathFileError = pathFileError;

            PathOldMainFile = PrepareLogFile(pathFileMain);
            PathOldErrorFile = PrepareLogFile(pathFileError);
            IsLoggerReady = true;
        }

        private static string GetDate()
        {
            return XTimeUtil.GetDateTime(DateTime.Now);
        }

        private string PrepareLogFile(string path)
        {
            string dir_name = System.IO.Path.GetDirectoryName(path);
            XFileManager.CheckDirectory(dir_name);

            string old_file_path = "";
            if (File.Exists(path))
            {
                try
                {
                    old_file_path = path + GetDate();
                    File.Move(path, old_file_path);
                }
                catch(System.Exception ex)
                {
                    Console.Out.WriteLine(ex);
                }
            }
            return old_file_path;
        }

        private void DumpLogFile(string path, List<string> log)
        {
            if (log.Count > 0)
            {
                try
                {
                    File.AppendAllLines(path, log, System.Text.Encoding.UTF8);
                    log.Clear();
                }
                catch (System.Exception ex)
                {
                    Console.Out.WriteLine(ex);
                }
            }
        }

        virtual public void Dump()
        {
            if (IsLoggerReady)
            {
                lock (MainLog)
                {
                    DumpLogFile(PathFileMain, MainLog);
                }

                lock (ErrorLog)
                {
                    DumpLogFile(PathFileError, ErrorLog);
                }
            }
        }

        public void Log(string message)
        {
            lock (MainLog)
            {
                MainLog.Add(message);
            }
        }

        public void LogWarning(string message)
        {
            lock (MainLog)
            {
                MainLog.Add(message);
            }
        }

        public void LogError(string message)
        {
            lock (MainLog)
            {
                MainLog.Add(message);
            }

            lock (ErrorLog)
            {
                ErrorLog.Add(message);
            }
        }

        public void LogException(string message, string stackTrace)
        {
            lock (MainLog)
            {
                MainLog.Add(message);
                MainLog.Add(stackTrace);
            }

            lock (ErrorLog)
            {
                ErrorLog.Add(message);
                ErrorLog.Add(stackTrace);
            }
        }
    }
}