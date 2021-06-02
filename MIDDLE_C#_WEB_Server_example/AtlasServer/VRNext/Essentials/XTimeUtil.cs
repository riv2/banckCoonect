using System;

namespace VRNext
{
    public class XTimeUtil
    {
        public static long GetAbsoluteTime()
        {
            return (DateTime.Now.Ticks / 10000L);
        }

        public static float GetTimePassed(long ts)
        {
            return (float)((GetAbsoluteTime() - ts) / 1000.0);
        }

        public static long GetUnixTimestamp()
        {
            return (long)(DateTime.UtcNow.Subtract(new DateTime(1970, 1, 1))).TotalSeconds;
        }

        public static long GetUnixTimestamp(DateTime date)
        {
            return (long)(date.Subtract(new DateTime(1970, 1, 1))).TotalSeconds;
        }
        public static string GetDateTime(DateTime date)
        {
            var culture = new System.Globalization.CultureInfo("ru-RU");
            return date.ToString(culture).Replace(":", "-");
        }
    }
}
