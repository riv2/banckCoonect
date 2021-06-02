using System.Security.Cryptography;
using System.Text;

namespace VRNext
{
    public class XHash
    {
        public enum HashAlgorithm { MD5 = 32, SHA256 = 64, SHA512 = 128 };

        /// <summary>
        /// Возвращает хеш-сумму строки по заданному алгоритму
        /// </summary>
        /// <param name="algorithm"></param>
        /// <param name="text"></param>
        /// <returns></returns>
        public static string Get(HashAlgorithm algorithm, string text)
        {
            return Get(algorithm, Encoding.UTF8.GetBytes(text));
        }

        /// <summary>
        /// Возвращает хеш-сумму байт по заданному алгоритму
        /// </summary>
        /// <param name="algorithm"></param>
        /// <param name="bytes"></param>
        /// <param name="offset"></param>
        /// <param name="count"></param>
        /// <returns></returns>
        public static string Get(HashAlgorithm algorithm, byte[] bytes, int offset, int count)
        {
            if (0 == offset && bytes.Length == count)
            {
                return Get(algorithm, bytes);
            }
            else
            {
                byte[] subarray = new byte[count];
                System.Array.Copy(bytes, offset, subarray, 0, count);
                return Get(algorithm, subarray);
            }
        }

        /// <summary>
        /// Возвращает хеш-сумму байт по заданному алгоритму
        /// </summary>
        /// <param name="algorithm"></param>
        /// <param name="bytes"></param>
        /// <returns></returns>
        public static string Get(HashAlgorithm algorithm, byte[] bytes)
        {
            switch (algorithm)
            {
                case HashAlgorithm.MD5: return Md5(bytes);
                case HashAlgorithm.SHA256: return Sha256(bytes);
                case HashAlgorithm.SHA512: return Sha512(bytes);
                default: return "unsuported algorithm";
            }
        }

        /// <summary>
        /// Возвращает хеш-сумму строки по алгоритму Sha256
        /// </summary>
        /// <param name="text"></param>
        /// <returns></returns>
        public static string Sha256(string text)
        {
            return Sha256(Encoding.UTF8.GetBytes(text));
        }

        /// <summary>
        /// Возвращает хеш-сумму байт по алгоритму Sha256
        /// </summary>
        /// <param name="bytes"></param>
        /// <returns></returns>
        public static string Sha256(byte[] bytes)
        {
            SHA256Managed crypt = new SHA256Managed();
            StringBuilder hash = new StringBuilder();
            byte[] crypto = crypt.ComputeHash(bytes, 0, bytes.Length);
            foreach (byte theByte in crypto)
            {
                hash.Append(theByte.ToString("x2"));
            }
            return hash.ToString();
        }

        /// <summary>
        /// Возвращает хеш-сумму строки по алгоритму Sha512
        /// </summary>
        /// <param name="text"></param>
        /// <returns></returns>
        public static string Sha512(string text)
        {
            return Sha512(Encoding.UTF8.GetBytes(text));
        }

        /// <summary>
        /// Возвращает хеш-сумму байт строки по алгоритму Sha512
        /// </summary>
        /// <param name="bytes"></param>
        /// <returns></returns>
        public static string Sha512(byte[] bytes)
        {
            SHA512Managed crypt = new SHA512Managed();
            StringBuilder hash = new StringBuilder();
            byte[] crypto = crypt.ComputeHash(bytes, 0, bytes.Length);
            foreach (byte theByte in crypto)
            {
                hash.Append(theByte.ToString("x2"));
            }
            return hash.ToString();
        }

        /// <summary>
        /// Возвращает хеш-сумму строки по алгоритму Md5
        /// </summary>
        /// <param name="text"></param>
        /// <returns></returns>
        public static string Md5(string text)
        {
            return Md5(Encoding.UTF8.GetBytes(text));
        }

        /// <summary>
        /// Возвращает хеш-сумму байт по алгоритму Md5
        /// </summary>
        /// <param name="bytes"></param>
        /// <returns></returns>
        public static string Md5(byte[] bytes)
        {
            MD5 crypt = MD5.Create();
            StringBuilder hash = new StringBuilder();
            byte[] crypto = crypt.ComputeHash(bytes, 0, bytes.Length);
            foreach (byte theByte in crypto)
            {
                hash.Append(theByte.ToString("x2"));
            }
            return hash.ToString();
        }
    }
}