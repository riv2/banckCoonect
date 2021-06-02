using System.Text;
using System.Collections;
using System.Collections.Generic;

namespace VRNext.Network
{
    internal class NetFlexFileCompressor 
    {
        private List<NetFlexFile> compressQue = new List<NetFlexFile>();
        private List<NetFlexFile> extractQue = new List<NetFlexFile>();
        private bool isCompressing = false;
        private bool isExtracting = false;

#if !UNITY_WEBGL
        private System.Threading.Thread compressThread = null;
        private System.Threading.Thread extractThread = null;
#endif

        /// <summary>
        /// Добавляет файл в очередь на упаковку и последующую отправку
        /// </summary>
        /// <param name="netFile"></param>
        internal void Compress(NetFlexFile netFile)
        {
            lock(compressQue)
            {
                compressQue.Add(netFile);
            }
        }

        /// <summary>
        /// Добавляет файл в очередь на распаковку и последующую обрабоку
        /// </summary>
        /// <param name="netFile"></param>
        internal void Extract(NetFlexFile netFile)
        {
            lock (extractQue)
            {
                extractQue.Add(netFile);
            }
        }

        public void Update()
        {
#if !UNITY_WEBGL
            if (compressThread == null)
            {
                if (compressQue.Count > 0)
                {
                    compressThread = new System.Threading.Thread(CompressThread);
                    compressThread.Start(compressQue[0]);
                }
            }
            else if(!compressThread.IsAlive)
            {
                OnCompressThreadDone(compressQue[0]);
                compressThread = null;
            }

            if (extractThread == null)
            {
                if (extractQue.Count > 0)
                {
                    extractThread = new System.Threading.Thread(ExtractThread);
                    extractThread.Start(extractQue[0]);
                }
            }
            else if(!extractThread.IsAlive)
            {
                OnExtractThreadDone(extractQue[0]);
                extractThread = null;
            }
#else
            if(!isCompressing && compressQue.Count > 0)
            {
                isCompressing = true;
                StartCoroutine("CompressCoroutine", compressQue[0]);
                //CompressThread(compressQue[0]);
                //OnCompressThreadDone(compressQue[0]);
            }

            if(!isExtracting && extractQue.Count > 0)
            {
                isExtracting = true;
                StartCoroutine("ExtractCoroutine", extractQue[0]);
                //ExtractThread(extractQue[0]);
                //OnExtractThreadDone(extractQue[0]);
            }

#endif
        }

        private IEnumerator CompressCoroutine(object argument)
        {
            NetFlexFile netFile = (NetFlexFile)argument;
            try
            {
                if (netFile.compress)
                {
                    int uncompressed = netFile.data_solid.Length;
                    byte[] compressed = LZMA.Compress(new System.IO.MemoryStream(netFile.data_solid), null).ToArray();
                    netFile.data_solid = compressed;
                    XLogger.Log("[NetFlex] Outcome NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " to <" + netFile.targetId + "> uncompressed size " + uncompressed + " bytes -> compressed size: " + netFile.data_solid.Length + " bytes");

                    OnCompressThreadDone(netFile);
                    isCompressing = false;
                }
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
                isCompressing = false;
            }
            yield return null;
        }

        private void CompressThread(object argument)
        {
            NetFlexFile netFile = (NetFlexFile)argument;
            try
            {
                if (netFile.compress)
                {
                    int uncompressed = netFile.data_solid.Length;
                    byte[] compressed = LZMA.Compress(new System.IO.MemoryStream(netFile.data_solid), null).ToArray();
                    netFile.data_solid = compressed;
                    XLogger.Log("[NetFlex] Outcome NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " to <" + netFile.targetId + "> uncompressed size " + uncompressed + " bytes -> compressed size: " + netFile.data_solid.Length + " bytes");
                }
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
            }
        }

        private void OnCompressThreadDone(NetFlexFile netFile)
        {
            if (netFile.data_solid != null)
            {
                lock (compressQue)
                {
                    if (compressQue.Contains(netFile))
                    {
                        compressQue.Remove(netFile);
                    }
                }
            }

            NetFlex.Instance().Sender.SendCommand(netFile);
        }

        private void ExtractThread(object argument)
        {
            NetFlexFile netFile = (NetFlexFile)argument;
            try
            {
                if (netFile.compress)
                {
                    int compressed = netFile.data_solid.Length;
                    byte[] decompressed = LZMA.Decompress(netFile.data_solid, 0);
                    netFile.data_solid = decompressed;
                    netFile.compress = false;
                    XLogger.Log("[NetFlex] Income NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " from <" + netFile.senderId + "> compressed size " + compressed + " bytes -> uncompressed size: " + netFile.data_solid.Length + " bytes");
                }
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
            }
        }

        private IEnumerator ExtractCoroutine(object argument)
        {
            NetFlexFile netFile = (NetFlexFile)argument;
            try
            {
                if(netFile.compress)
                {
                    int compressed = netFile.data_solid.Length;
                    byte[] decompressed = LZMA.Decompress(netFile.data_solid, 0);
                    netFile.data_solid = decompressed;
                    netFile.compress = false;
                    XLogger.Log("[NetFlex] Income NetFlexFile <" + netFile.uniq + "> with command " + netFile.commandId + " " + netFile.message + " from <" + netFile.senderId + "> compressed size " + compressed + " bytes -> uncompressed size: " + netFile.data_solid.Length + " bytes");

                    OnExtractThreadDone(netFile);
                    isExtracting = false;
                }
            }
            catch (System.Exception ex)
            {
                XLogger.LogException(ex);
                isExtracting = false;
            }
            yield return null;
        }

        private void OnExtractThreadDone(NetFlexFile netFile)
        {
            if (netFile.data_solid != null)
            {
                lock (extractQue)
                {
                    if (extractQue.Contains(netFile))
                    {
                        extractQue.Remove(netFile);
                    }
                }
            }

            NetFlex.Instance().Receiver.OnNetFileReady(netFile);
        }
    }
}

