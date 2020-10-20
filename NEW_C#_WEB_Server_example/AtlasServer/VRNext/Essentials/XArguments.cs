using System.Collections.Generic;

namespace VRNext
{
    /// <summary>
    /// Параметр аргумента
    /// </summary>
    public class XParam
    {
        /// <summary>
        /// Имя параметра
        /// </summary>
        public string Name { private set; get; }

        /// <summary>
        /// Значение параметра
        /// </summary>
        public string Value { private set; get; }

        public XParam(string name, string value)
        {
            Name = name;
            Value = value;
        }

        /// <summary>
        /// Устанавливает значение параметра
        /// </summary>
        /// <param name="value"></param>
        public void SetValue(string value)
        {
            Value = value;
        }
    }

    /// <summary>
    /// Класс по обслуживанию именованых аргументов
    /// </summary>
    public class XArguments
    {
        /// <summary>
        /// Список параметров
        /// </summary>
        public List<XParam> Params { private set; get; }

        public XArguments()
        {
            Params = new List<XParam>();
        }

        public XArguments(string args)
        {
            ConstrcutParams(args.Replace("\r\n", ";").Replace("=", ";").Split(';'));
        }

        public XArguments(string[] argsList)
        {
            ConstrcutParams(argsList);
        }

        private void ConstrcutParams(string[] argsList)
        {
            Params = new List<XParam>();
            for (int i = 0; i < argsList.Length; i++)
            {
                if (i % 2 == 0 && i + 1 < argsList.Length)
                {
                    Params.Add(new XParam(argsList[i], argsList[i + 1]));
                }
            }
            Autoreplace();
        }

        /// <summary>
        /// Добавляет аргумент
        /// </summary>
        /// <param name="name"></param>
        /// <param name="value"></param>
        public void Add(string name, string value)
        {
            Params.Add(new XParam(name, value));
        }

        /// <summary>
        /// Устанавливает новое значние аргумента
        /// </summary>
        /// <param name="name"></param>
        /// <param name="value"></param>
        public void Set(string name, string value)
        {
            XParam param = Params.Find(x => x.Name == name);
            if (param != null)
            {
                param.SetValue(value);
            }
            else
            {
                Params.Add(new XParam(name, value));
            }
        }

        /// <summary>
        /// Возвращает строковое значение аргумента
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public string Get(string name)
        {
            return Params.Find(x => x.Name == name)?.Value;
        }

        /// <summary>
        /// Возвращаает целочисленное значение аргумента
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public int GetInt32(string name)
        {
            string val = Get(name);
            return string.IsNullOrEmpty(val) ? 0 : int.Parse(val);
        }

        /// <summary>
        /// Возвращаает целочисленное значение аргумента
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public long GetInt64(string name)
        {
            string val = Get(name);
            return string.IsNullOrEmpty(val) ? 0 : long.Parse(val);
        }

        /// <summary>
        /// Возвращаает вещественное значение аргумента
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public float GetSingle(string name)
        {
            string val = Get(name);
            return string.IsNullOrEmpty(val) ? 0 : float.Parse(val);
        }

        /// <summary>
        /// Возвращаает вещественное значение аргумента
        /// </summary>
        /// <param name="name"></param>
        /// <returns></returns>
        public double GetDouble(string name)
        {
            string val = Get(name);
            return string.IsNullOrEmpty(val) ? 0 : double.Parse(val);
        }

        /// <summary>
        /// Удаляет список аргументов по именам
        /// </summary>
        /// <param name="nameList"></param>
        public void RemoveAll(params string[] nameList)
        {
            foreach (string name in nameList)
            {
                Params.RemoveAll(x => x.Name == name);
            }
        }

        public string[] ToArray()
        {
            List<string> elements = new List<string>();
            foreach (XParam param in Params)
            {
                if (param.Name != null)
                {
                    elements.Add(param.Name);
                }
                if (param.Value != null)
                {
                    elements.Add(param.Value);
                }
            }
            return elements.ToArray();
        }

        override public string ToString()
        {
            List<string> elements = new List<string>();
            foreach (XParam param in Params)
            {
                if (param.Name != null && param.Value != null)
                {
                    elements.Add(param.Name + "=" + param.Value);
                }
            }
            return string.Join("\r\n", elements);
        }

        /// <summary>
        /// Автозамена значений аргументов
        /// </summary>
        private void Autoreplace()
        {
            foreach (XParam param in Params)
            {
                if (param.Value.Contains("%"))
                {
                    foreach (XParam replace in Params)
                    {
                        param.SetValue(param.Value.Replace("%" + replace.Name + "%", replace.Value));
                    }
                }
            }
        }
    }
}