namespace VRNext
{
    public class XRandomUtil
    {
        private static System.Random instance = null;
        public static System.Random GetInstance()
        {
            if (instance == null)
            {
                instance = new System.Random((int)XTimeUtil.GetAbsoluteTime());
            }
            return instance;
        }

        public static int GetInt32(int max)
        {
            return GetInstance().Next(max);
        }

        public static int GetInt32(int min, int max)
        {
            return GetInstance().Next(min, max);
        }

        public static double GetDouble(int max)
        {
            return GetInstance().Next(max);
        }

        public static double GetDouble(int min, int max)
        {
            return GetInstance().Next(min, max);
        }

        public static string GetRandomString(int len)
        {
            string glyphs = "abcdefghijklmnopqrstuvwxyz";
            string result = "";
            for (int i = 0; i < len; i++)
            {
                result += glyphs[XRandomUtil.GetInt32(glyphs.Length)];
            }
            return result;
        }

        public static string GetRandomUniq(int len)
        {
            return GetRandomString(len) + "-" + GetRandomString(len);
        }
    }
}
