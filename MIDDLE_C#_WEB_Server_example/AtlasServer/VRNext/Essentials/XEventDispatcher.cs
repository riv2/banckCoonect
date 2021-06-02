using System.Linq;
using System.Collections;
using System.Collections.Generic;

namespace VRNext
{
    /// <summary>
    /// Триггер, срабатывающий на события в программном коде
    /// </summary>
    public partial class XEvent
    {
        /// <summary>
        /// Имя триггера, используемой для назначения слушателей
        /// </summary>
        protected string name;

        /// <summary>
        /// Цель триггера, указывает на объект, действие над которым сгенерировало триггер
        /// </summary>
        protected object target;

        /// <summary>
        /// Данные сопровождающие триггер, которые могут содержать дополнительную информацию о событии
        /// </summary>
        protected object data;

        /// <summary>
        /// Флаг прерывания исполнения триггера, нужно для отмены обработки, если один из обработчиков принял решение
        /// прервать дальнейшее исполнение триггера другими обработчиками
        /// </summary>
        protected bool isInterrupted;

        public XEvent(string name, object target = null, object data = null)
        {
            this.name = name;
            this.target = target;
            this.data = data;
            isInterrupted = false;
        }

        public string GetName()
        {
            return name;
        }

        public object GetTarget()
        {
            return target;
        }

        public object GetMeta()
        {
            return data;
        }

        public bool IsInterrupted()
        {
            return isInterrupted;
        }

        public void Interrupt()
        {
            isInterrupted = true;
        }
    }

    /// <summary>
    /// Контейнер обработчика триггера
    /// </summary>
    internal class XEventHandler
    {
        public string Name;
        public System.Action<XEvent> Listener;
        public int Priority;
        public bool IsSingleCall;
    }

    /// <summary>
    /// Интерфейс диспетчера триггеров
    /// </summary>
    public interface IXEventDispatcher
    {
        /// <summary>
        /// Добавляет обработчик срабатывания триггера, если триггер еще не установлен
        /// </summary>
        /// <param name="name"></param>
        /// <param name="listener"></param>
        /// <param name="priority"></param>
        /// <param name="single_call"></param>
        void AddTriggerListener(string name, System.Action<XEvent> listener, int priority = 0, bool single_call = false);

        /// <summary>
        /// Удаляет обработчик триггера, если он назначен
        /// </summary>
        /// <param name="name"></param>
        /// <param name="listener"></param>
        void RemoveTriggerListener(string name, System.Action<XEvent> listener);

        /// <summary>
        /// Очищает список слушателей треггера
        /// </summary>
        void ClearTriggerListeners();

        /// <summary>
        /// Вызывает срабатывание триггера
        /// </summary>
        /// <param name="trigger"></param>
        /// <returns></returns>
        bool DispatchTrigger(XEvent trigger);

        /// <summary>
        /// Возвращает статус наличия обработчиков триггера
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        bool HasTriggerListener(string name);
    }

    public class XEventDispatcher : IXEventDispatcher
    {
        private List<XEventHandler> handlers_list = new List<XEventHandler>();

        /// <summary>
        /// Добавляет обработчик срабатывания триггера, если триггер еще не установлен
        /// </summary>
        /// <param name="name"></param>
        /// <param name="listener"></param>
        /// <param name="priority"></param>
        /// <param name="single_call"></param>
        public void AddTriggerListener(string name, System.Action<XEvent> listener, int priority = 0, bool single_call = false)
        {
            if (string.IsNullOrEmpty(name) || listener == null)
            {
                XLogger.LogError("[TriggerDispatcher] Can not add event listener.");
                return;
            }

            XEventHandler handler = handlers_list.Find(x => x.Name == name && x.Listener == listener);
            if (handler == null)
            {
                handler = new XEventHandler() { Name = name, Listener = listener };
                handlers_list.Add(handler);
            }

            handler.Priority = priority;
            handler.IsSingleCall = single_call;
        }

        /// <summary>
        /// Удаляет обработчик триггера, если он назначен
        /// </summary>
        /// <param name="name"></param>
        /// <param name="listener"></param>
        public void RemoveTriggerListener(string name, System.Action<XEvent> listener)
        {
            if (string.IsNullOrEmpty(name) || listener == null)
            {
                XLogger.LogError("[TriggerDispatcher] Can not remove event listener.");
                return;
            }

            XEventHandler handler = handlers_list.Find(x => x.Name == name && x.Listener == listener);
            if (handler != null)
            {
                handlers_list.Remove(handler);
            }
        }

        /// <summary>
        /// Очищает список слушателей треггера
        /// </summary>
        public void ClearTriggerListeners()
        {
            handlers_list.Clear();
        }

        /// <summary>
        /// Вызывает срабатывание триггера
        /// </summary>
        /// <param name="trigger"></param>
        /// <returns></returns>
        public bool DispatchTrigger(XEvent trigger)
        {
            List<XEventHandler> handlers_triggered = handlers_list.FindAll(x => x.Name == trigger.GetName()).OrderByDescending(x => x.Priority).ToList();
            List<XEventHandler> handlers_removing = new List<XEventHandler>();
            if (handlers_triggered.Count > 0)
            {
                foreach (XEventHandler handler in handlers_triggered)
                {
                    try
                    {
                        handler.Listener?.Invoke(trigger);
                    }
                    catch (System.Exception ex)
                    {
                        if (handler.Listener != null)
                        {
                            XLogger.LogError("Error call of " + handler.Listener.Method.Name);
                        }
                        XLogger.LogException(ex);
                    }

                    if (handler.IsSingleCall)
                    {
                        handlers_removing.Add(handler);
                    }
                }

                handlers_list.RemoveAll(x => handlers_removing.Contains(x));
                return true;
            }
            else
            {
                return false;
            }
        }

        /// <summary>
        /// Возвращает статус наличия обработчиков триггера
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public bool HasTriggerListener(string name)
        {
            XEventHandler handler = handlers_list.Find(x => x.Name == name);
            return handler != null;
        }

        /// <summary>
        /// Возвращает статус наличия обработчиков триггера
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public bool HasTriggerListener(string name, System.Action<XEvent> listener)
        {
            XEventHandler handler = handlers_list.Find(x => x.Name == name && x.Listener == listener);
            return handler != null;
        }
    }
}