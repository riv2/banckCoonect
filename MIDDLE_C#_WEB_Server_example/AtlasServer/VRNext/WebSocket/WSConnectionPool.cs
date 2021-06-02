#if !UNITY_WEBGL
using System.Threading;
#endif
using System.Collections;
using System.Collections.Generic;

namespace VRNext.WebSocket
{
    /// <summary>
    /// Пул WebSocket подключений (Синглтон).
    /// Создает и обновляет все WebSocket подключения в отдельном потоке.
    /// </summary>
    public class WSConnectionPool
    {
        private static WSConnectionPool instance = null;

        public static WSConnectionPool GetInstance()
        {
            if(instance == null)
            {
                instance = new WSConnectionPool();
            }
            return instance;
        }

        /// <summary>
        /// Пул WebSocket подключений
        /// </summary>
        private List<WSConnectionAbstract> ConnectionList;

#if !UNITY_WEBGL
        /// <summary>
        /// Поток обновления WebSocket подключений
        /// </summary>
        private Thread threadUpdate;
#endif

        /// <summary>
        /// Режим отладки, выводит в лог больше информации о работе модуля
        /// </summary>
        private bool DebugMode;

        /// <summary>
        /// Статус пула, пока True - поток будет работать
        /// </summary>
        private bool IsAlive;

        /// <summary>
        /// Заморозка пула, если True - поток будет на паузе
        /// </summary>
        public bool IsSuspended;

        private WSConnectionPool()
        {
            IsAlive = true;
            IsSuspended = false;
            DebugMode = false;
            ConnectionList = new List<WSConnectionAbstract>();
#if !UNITY_WEBGL
            threadUpdate = new Thread(UpdateThreaded);
            threadUpdate.Start(this);
#endif
        }

        /// <summary>
        /// Создает новое подключение по WebSocket с указанными настройками.
        /// </summary>
        /// <param name="host_name"></param>
        /// <param name="host_port"></param>
        /// <param name="init_token"></param>
        /// <param name="secure"></param>
        /// <param name="resolve_ip"></param>
        /// <returns></returns>
        public WSConnectionAbstract NewConnection(string host_name, string host_port, string init_token, bool secure, bool resolve_ip = true)
        {
#if UNITY_WEBGL && !UNITY_EDITOR
            WSConnectionAbstract connection = new WSConnectionBrowser(host_name, host_port, init_token, secure, resolve_ip);
#else
            WSConnectionAbstract connection = new WSConnectionDesktop(host_name, host_port, init_token, secure, resolve_ip);
#endif
            lock (ConnectionList)
            {
                ConnectionList.Add(connection);
                connection.SetDebugMode(DebugMode);
            }
            return connection;
        }

        /// <summary>
        /// Обязательно нужно вызвать при закрытии программы для корреткного завершения потока.
        /// </summary>
        public void OnApplicationQuit()
        {
            IsAlive = false;
#if !UNITY_WEBGL
            threadUpdate.Join();
#endif
        }

        /// <summary>
        /// Устанавливает флаг отладки модуля
        /// </summary>
        /// <param name="enabled"></param>
        public void SetDebugMode(bool enabled)
        {
            DebugMode = enabled;
            lock (ConnectionList)
            {
                for (int i = 0; i < ConnectionList.Count; i++)
                {
                    ConnectionList[i].SetDebugMode(enabled);
                }
            }
        }

        public void Update()
        {
            if (!IsSuspended && IsAlive)
            {
                for (int i = 0; i < ConnectionList.Count; i++)
                {
                    ConnectionList[i].Update();
                }
            }
        }

        /// <summary>
        /// Поток обновления подключений по WebSocket
        /// </summary>
        /// <param name="args"></param>
        private static void UpdateThreaded(object args)
        {
            WSConnectionPool pool = (WSConnectionPool)args;

            // Итератор обновления, не смысла обновлять все подключения, 
            // поэтому по 1 подключению за 1 итерацию цикла.
            int i = 0;
            while(pool.IsAlive)
            {
                if (!pool.IsSuspended)
                {
                    lock (pool.ConnectionList)
                    {
                        for (i = 0; i < pool.ConnectionList.Count; i++)
                        {
                            pool.ConnectionList[i].Update();
                        }
                        i = (i + 1) >= pool.ConnectionList.Count ? 0 : i + 1;
                    }
                }
            }

            // Если поток завершен, отключаем все вебсокеты от сети и очищаем пул.
            lock (pool.ConnectionList)
            {
                foreach (var connection in pool.ConnectionList)
                {
                    connection.Disconnect();
                }
                pool.ConnectionList.Clear();
            }
        }
    }
}