using System;
using System.Collections.Concurrent;
using System.Text;
using System.Threading.Tasks;

namespace AtlasServer.External
{
    internal static class Logger
    {
        private static BlockingCollection<string> _blockingCollectionError;
        private static BlockingCollection<string> _blockingCollectionInfo;
        private static BlockingCollection<string> _blockingCollectionNotice;
        private static BlockingCollection<string> _blockingCollectionDefault;
        private static BlockingCollection<string> _blockingCollectionCache;        

        private static Task _taskError;
        private static Task _taskInfo;
        private static Task _taskNotice;
        private static Task _taskDefault;
        private static Task _taskCache;

        static Logger()
        {
            _blockingCollectionError = new BlockingCollection<string>();
            _blockingCollectionInfo  = new BlockingCollection<string>();
            _blockingCollectionNotice = new BlockingCollection<string>();
            _blockingCollectionDefault = new BlockingCollection<string>();
            _blockingCollectionCache = new BlockingCollection<string>();

            _taskError = Task.Factory.StartNew(() =>
            {
                using (var streamWriterError = new System.IO.StreamWriter("error.log", true, Encoding.UTF8))
                {
                    streamWriterError.AutoFlush = true;

                    foreach (var s in _blockingCollectionError.GetConsumingEnumerable())
                        streamWriterError.WriteLine(s);
                } 
            },
            TaskCreationOptions.LongRunning);

            _taskInfo = Task.Factory.StartNew(() =>
            {
                using (var streamWriterInfo = new System.IO.StreamWriter("info.log", true, Encoding.UTF8))
                {
                    streamWriterInfo.AutoFlush = true;

                    foreach (var s in _blockingCollectionInfo.GetConsumingEnumerable())
                        streamWriterInfo.WriteLine(s);
                }
            },
            TaskCreationOptions.LongRunning);

            _taskNotice = Task.Factory.StartNew(() =>
            {
                using (var streamWriterNotice = new System.IO.StreamWriter("notice.log", true, Encoding.UTF8))
                {
                    streamWriterNotice.AutoFlush = true;

                    foreach (var s in _blockingCollectionNotice.GetConsumingEnumerable())
                        streamWriterNotice.WriteLine(s);
                }
            },
           TaskCreationOptions.LongRunning);

            _taskDefault = Task.Factory.StartNew(() =>
            {
                using (var streamWriterDefault = new System.IO.StreamWriter("default.log", true, Encoding.UTF8))
                {
                    streamWriterDefault.AutoFlush = true;

                    foreach (var s in _blockingCollectionDefault.GetConsumingEnumerable())
                        streamWriterDefault.WriteLine(s);
                }
            },
            TaskCreationOptions.LongRunning);

            _taskCache = Task.Factory.StartNew(() =>
            {
                using (var streamWriterCache = new System.IO.StreamWriter("cache.log", true, Encoding.UTF8))
                {
                    streamWriterCache.AutoFlush = true;

                    foreach (var s in _blockingCollectionCache.GetConsumingEnumerable())
                        streamWriterCache.WriteLine(s);
                }
            },
            TaskCreationOptions.LongRunning);


        }

        public static void WriteLog(string log)
        {         

            if (log.ToUpper().IndexOf("INFO") > -1)
            {
                _blockingCollectionInfo.Add($"{DateTime.Now.ToString("dd.MM.yyyy HH:mm:ss.fff")} : {log}");               

            } else if (log.ToUpper().IndexOf("ERROR") > -1)
            {
                _blockingCollectionError.Add($"{DateTime.Now.ToString("dd.MM.yyyy HH:mm:ss.fff")} : {log}");

            } else if (log.ToUpper().IndexOf("NOTICE") > -1)
            {
                _blockingCollectionNotice.Add($"{DateTime.Now.ToString("dd.MM.yyyy HH:mm:ss.fff")} : {log}");
            } else if (log.ToUpper().IndexOf("CACHE") > -1)
            {
                _blockingCollectionCache.Add($"{DateTime.Now.ToString("dd.MM.yyyy HH:mm:ss.fff")} : {log}");
            } else
            {
                _blockingCollectionDefault.Add($"{DateTime.Now.ToString("dd.MM.yyyy HH:mm:ss.fff")} : {log}");
            }
            
        }

        public static void Flush()
        {
            _blockingCollectionError.CompleteAdding();
            _blockingCollectionInfo.CompleteAdding();
            _blockingCollectionNotice.CompleteAdding();
            _blockingCollectionDefault.CompleteAdding();
            _blockingCollectionCache.CompleteAdding();
            _taskError.Wait();
            _taskInfo.Wait();
            _taskNotice.Wait();
            _taskDefault.Wait();
            _taskCache.Wait();
        }
    }

}

