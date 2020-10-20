using System.Collections;
using System.Collections.Generic;

namespace VRNext.WebSocket
{
    public enum WSState
    {
        /// <summary>
        /// Ожидается подключение
        /// </summary>
        AWAIT,
        /// <summary>
        /// Подключение начато
        /// </summary>
        CONNECTING,
        /// <summary>
        /// Подключено
        /// </summary>
        ONLINE,
        /// <summary>
        /// Отключено
        /// </summary>
        OFFLINE,
        /// <summary>
        /// Произошла ошибка сети
        /// </summary>
        ERROR
    };

    /// <summary>
    /// Буффер входящих сообщений, для обработки в основном потоке
    /// </summary>
    public class WSMessageBuffer
    {
        public List<WSData> Messages { private set; get; }
        public bool IsLocked { private set; get; }

        public WSMessageBuffer()
        {
            Messages = new List<WSData>();
            IsLocked = false;
        }

        /// <summary>
        /// (потокобезопасно) Добавляет сообщение в очередь обработки
        /// </summary>
        /// <param name="message"></param>
        public void Add(WSData message)
        {
            lock (Messages)
            {
                IsLocked = true;
                Messages.Add(message);
                IsLocked = false;
            }
        }

        /// <summary>
        /// (потокобезопасно) Считывает все накопившиеся сообщения в очереди, и очищает ее
        /// </summary>
        /// <returns></returns>
        public List<WSData> GetBuffer()
        {
            List<WSData> result = new List<WSData>();
            if (!IsLocked)
            {
                lock (Messages)
                {
                    result.AddRange(Messages);
                    Messages.Clear();
                }
            }
            return result;
        }
    }
    
    /// <summary>
    /// Абстрактное WebSocket подключение.
    /// </summary>
    public class WSConnectionAbstract
    {
        public bool IsAlive { protected set; get; }
        public bool IsSecure { protected set; get; }
        public bool IsIpResolve { protected set; get; }
        public bool IsNetworkAvailable { protected set; get; }
        public bool DebugMode { protected set; get; }
        public string HostPort { protected set; get; }
        public string HostName { protected set; get; }
        public string InitToken { protected set; get; }
        public WSState State { protected set; get; }
        public WSMessageBuffer Messages { protected set; get; }

        public bool IsReconnectRequired = false;

        public WSConnectionAbstract(string host_name, string host_port, string init_token, bool secure, bool resolve_ip = true)
        {
            HostName = host_name;
            HostPort = host_port;
            InitToken = init_token;
            IsSecure = secure;
            IsIpResolve = resolve_ip;
            State = WSState.OFFLINE;
            DebugMode = false;
            IsAlive = false;
            IsNetworkAvailable = false;
        }

        /// <summary>
        /// Устанавливает флаг отладки модуля
        /// </summary>
        /// <param name="enabled"></param>
        public void SetDebugMode(bool enabled)
        {
            DebugMode = enabled;
        }

        /// <summary>
        /// Устанавливает буффер входящих сообщений, у каждого подключения должен быть свой
        /// </summary>
        /// <param name="buffer"></param>
        public void SetMessageBuffer(WSMessageBuffer buffer)
        {
            Messages = buffer;
        }

        /// <summary>
        /// Активирует подключение WebSocket к серверу
        /// </summary>
        virtual public void Connect()
        {
            State = WSState.AWAIT;
            IsAlive = true;
        }

        /// <summary>
        /// Отправляет данные через сокет
        /// </summary>
        virtual public void Send(byte[] buffer)
        {
        }

        /// <summary>
        /// Отключает WebSocket от сервера
        /// </summary>
        virtual public void Disconnect()
        {
            State = WSState.OFFLINE;
            IsAlive = false;
        }

        /// <summary>
        /// Обновляет состояние подключения
        /// </summary>
        virtual public void Update()
        {
        }
    }

}