#if !ATLAS_CLIENT
using System;
using System.Collections.Generic;
using VRNext;

namespace VRNext.XService
{
    public class XServiceManager
    {
        private static XServiceManager instance = null;
        public static XServiceManager GetInstance()
        {
            if(instance == null)
            {
                instance = new XServiceManager();
            }
            return instance;
        }

        private List<XServiceController> Pool = new List<XServiceController>();

        public void Update()
        {
            foreach(XServiceController entry in Pool)
            {
                if (entry.Thread == null && entry.Alive)
                {
                    try
                    {
                        entry.Service.Update();
                    }
                    catch(Exception ex)
                    {
                        //XLogger.LogException(ex);
                    }
                }
            }
        }

        void OnApplicationQuit()
        {
            StopAllServices();
        }

        public XServiceController AddService(IXService service, string name, bool threaded = false, int period = 0)
        {
            XServiceController controller = new XServiceController(service, name, threaded, period);
            Pool.Add(controller);
            return controller;
        }

        /// <summary>
        /// Запускает работу всех незапущенных сервисов
        /// </summary>
        public void StartAllServices()
        {
            foreach (XServiceController service in Pool)
            {
                if(!service.Alive)
                    service.Start();
            }
        }

        /// <summary>
        /// Прекращает работу всех сервисов
        /// </summary>
        public void StopAllServices()
        {
            foreach (XServiceController service in Pool)
            {
                if (service.Alive)
                    service.Stop();
            }
        }
    }
}
#endif