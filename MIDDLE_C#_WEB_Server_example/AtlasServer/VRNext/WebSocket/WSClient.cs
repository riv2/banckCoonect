using System;
using System.Collections.Generic;
using System.Security.Authentication;

namespace VRNext.WebSocket
{
    public enum WSContentType { TEXT, BINARY };
    public class WSData
    {
        public WSContentType type;
        public string data;
        public byte[] rawData;
    }

    public class WSClient
    {
        private Uri mUrl;

        public WSClient(Uri url)
        {
            mUrl = url;

            string protocol = mUrl.Scheme;
            if (!protocol.Equals("ws") && !protocol.Equals("wss"))
                throw new ArgumentException("Unsupported protocol: " + protocol);
        }

        private WebSocketSharp.WebSocket m_Socket;
        private System.Threading.Thread m_Thread;
        private Queue<WSData> m_Messages = new Queue<WSData>();
        private bool m_IsConnected = false;
        private bool m_IsClosed = false;
        private string m_Error = null;
        private int AwaitTime;

        public void Connect(int awaitTime, bool secure = false)
        {
            if (m_IsConnected)
                Close();

            AwaitTime = awaitTime;
            m_IsClosed = false;
            m_Socket = new WebSocketSharp.WebSocket(mUrl.ToString());

            if (secure)
            {
                // Set full TLS protocols
                m_Socket.SslConfiguration.EnabledSslProtocols = SslProtocols.Default | SslProtocols.Tls | SslProtocols.Tls11 | SslProtocols.Tls12;

                m_Socket.SslConfiguration.ServerCertificateValidationCallback = (sender, certificate, chain, sslPolicyErrors) =>
                {
                    return true; // If the server certificate is valid.
                };
            }

            m_Socket.OnMessage += (sender, e) =>
            {
                lock (m_Messages)
                {
                    if (e.Opcode == WebSocketSharp.Opcode.Text)
                    {
                        m_Messages.Enqueue(new WSData() { type = WSContentType.TEXT, data = e.Data });
                    }
                    else if (e.Opcode == WebSocketSharp.Opcode.Binary)
                    {
                        m_Messages.Enqueue(new WSData() { type = WSContentType.BINARY, rawData = e.RawData });
                    }
                }
            };

            m_Socket.OnOpen += (sender, e) => m_IsConnected = true;
            m_Socket.OnError += (sender, e) => m_Error = e.Message;
            m_Socket.OnClose += (sender, e) =>
            {
                m_IsClosed = true;
                m_Error = "Connection has ben closed.";
            };

            // Подключаемся в отдельном потоке, чтобы не стопорить работу всей программы.
            m_Thread = new System.Threading.Thread(ConnectThread);
            m_Thread.Start();
        }

        private void ConnectThread()
        {
            m_Socket.Connect();

            // Ограничиваем лимит времени на подключение к серверу в милисекундах.
            int waitTime = AwaitTime * 1000;
            while (!m_IsConnected && !m_IsClosed && m_Error == null)
            {
                if (waitTime > 0)
                {
                    System.Threading.Thread.Sleep(100);
                    waitTime -= 100;
                }
                else
                {
                    // Время ожидания иссякло, информируем об ошибке.
                    m_Error = "Connection time out.";
                }
            }

            if(m_IsClosed)
            {
                // Подключение было закрыто в момент подключения - это свидетельствует
                // об отсутсвии доступа к сети интернет.
                m_Error = "Network unavailable.";
            }
            m_Thread = null;
        }

        public void Send(byte[] buffer)
        {
            try
            {
                m_Socket.Send(buffer);
            }
            catch (InvalidOperationException ex)
            {
                Debug.LogException(ex);
                m_Error = ex.Message;
            }
        }

        public void Send(string buffer)
        {
            try
            {
                m_Socket.Send(buffer);
            }
            catch (InvalidOperationException ex)
            {
                Debug.LogException(ex);
                m_Error = ex.Message;
            }
        }

        public WSData Recv()
        {
            lock (m_Messages)
            {
                if (m_Messages.Count == 0)
                    return null;

                return m_Messages.Dequeue();
            }
        }

        public void Close()
        {
            m_Socket?.Close();
            m_Error = null;
            m_IsConnected = false;
            m_IsClosed = true;
        }

        public bool IsConnected()
        {
            return m_IsConnected && !m_IsClosed;
        }

        public string error
        {
            get
            {
                return m_Error;
            }
        }
    }
}