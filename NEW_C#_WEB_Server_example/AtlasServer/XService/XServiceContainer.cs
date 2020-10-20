using System.Threading;

namespace VRNext.XService
{
    /// <summary>
    /// Интерфейс сревиса, может быть добавлен в менеджер сервисов для обработки
    /// </summary>
    public interface IXService
    {
        void Update();
    }

    /// <summary>
    /// Контроллер сервиса, служит для увправления сервсом
    /// </summary>
    public class XServiceController
    {
        public string Name { private set; get; }
        public int Period { private set; get; }
        public bool Alive { private set; get; }

        public IXService Service { private set; get; }
        public Thread Thread { private set; get; }

        public XServiceController(IXService service, string name, bool threaded, int period)
        {
            Service = service;
            Name = name;
            Period = period;

            if (threaded)
            {
                Thread = new Thread(ServiceThread);
            }
        }

        /// <summary>
        /// Запускает работу сервиса
        /// </summary>
        public void Start()
        {
            Alive = true;
            if (Thread != null)
            {
                Thread.Start(this);
            }
            else
            {

            }
        }

        /// <summary>
        /// Прекращает работу сервиса
        /// </summary>
        public void Stop()
        {
            Alive = false;
            if (Thread != null)
            {
                Thread.Join();
            }
        }

        /// <summary>
        /// Поток выполенения поточного сервиса
        /// </summary>
        /// <param name="arguments"></param>
        private void ServiceThread(object arguments)
        {
            XServiceController controller = (XServiceController)arguments;
            while (controller.Alive)
            {
                controller.Service.Update();
                Thread.Sleep(controller.Period);
            }
        }
    }
}
