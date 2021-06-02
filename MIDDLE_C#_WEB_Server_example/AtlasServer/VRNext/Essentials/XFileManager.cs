using System.IO;

namespace VRNext
{
    public enum XFileStatus
    {
        FILE_NOT_EXISTS,
        FILE_EXISTS_AND_LOCKED,
        FILE_EXISTS_AND_AVAILABLE
    };

    public class XFileManager
    {
        /// <summary>
        /// Возвращает статус файла
        /// </summary>
        /// <param name="filename"></param>
        /// <returns></returns>
        static public XFileStatus GetFileStatus(string filename)
        {
            if (File.Exists(filename))
            {
                try
                {
                    FileStream fs = File.Open(filename, FileMode.OpenOrCreate, FileAccess.ReadWrite, FileShare.None);
                    fs.Close();

                    return XFileStatus.FILE_EXISTS_AND_AVAILABLE;
                }
                catch (IOException) {
                }

                return XFileStatus.FILE_EXISTS_AND_LOCKED;
            }
            return XFileStatus.FILE_NOT_EXISTS;
        }

        /// <summary>
        /// Возвращает True если файл существует и доступен для записи
        /// </summary>
        /// <param name="filename"></param>
        /// <returns></returns>
        static public bool IsFileAwailable(string filename)
        {
            if (File.Exists(filename))
            {
                bool awailable = true;
                try
                {
                    FileStream fs = File.Open(filename, FileMode.OpenOrCreate, FileAccess.ReadWrite, FileShare.None);
                    fs.Close();
                }
                catch (IOException)
                {
                    awailable = false;
                }

                return awailable;
            }
            else return false;
        }

        /// <summary>
        /// Перемещает файл в новую локацию.
        /// Если файл в новой локации иуже существет, то он будет удален и заменен перемещаемым.
        /// </summary>
        /// <param name="srcPath"></param>
        /// <param name="destPath"></param>
        /// <returns></returns>
        public static bool MoveAndReplace(string srcPath, string destPath)
        {
            if (File.Exists(destPath))
            {
                try
                {
                    File.Delete(destPath);
                    File.Move(srcPath, destPath);
                }
                catch (System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return false;
                }
            }
            else
            {
                File.Move(srcPath, destPath);
            }
            return true;
        }

        /// <summary>
        /// Копирует файл в новую локацию.
        /// Если файл в новой локации иуже существет, то он будет удален и заменен копируемым.
        /// </summary>
        /// <param name="srcPath"></param>
        /// <param name="destPath"></param>
        /// <returns></returns>
        public static bool CopyAndReplace(string srcPath, string destPath)
        {
            if(File.Exists(destPath))
            {
                try
                {
                    File.Delete(destPath);
                    File.Copy(srcPath, destPath);
                }
                catch(System.Exception ex)
                {
                    XLogger.LogException(ex);
                    return false;
                }
            }
            else
            {
                File.Copy(srcPath, destPath);
            }
            return true;
        }

        /// <summary>
        /// Проверяет наличие директории.
        /// Если он не существет - то будет создана пустая
        /// </summary>
        /// <param name="pathToDir"></param>
        public static void CheckDirectory(string pathToDir)
        {
            if (!Directory.Exists(pathToDir))
            {
                Directory.CreateDirectory(pathToDir);
            }
        }

        /// <summary>
        /// Очищает директорию от всего содержимого.
        /// Если директория не существует - будет создана пустая.
        /// </summary>
        /// <param name="pathToDir"></param>
        public static void ClearDirectory(string pathToDir)
        {
            if (Directory.Exists(pathToDir))
            {
                string[] dirslist = Directory.GetDirectories(pathToDir, "*");
                foreach (string dirname in dirslist)
                {
                    try
                    {
                        ClearDirectory(dirname);
                        Directory.Delete(dirname, true);
                    }
                    catch
                    {
                        XLogger.Log("Can't Delete the file " + dirname);
                    }
                }

                string[] fileslist = Directory.GetFiles(pathToDir, "*");
                foreach (string filename in fileslist)
                {
                    try
                    {
                        File.Delete(filename);
                    }
                    catch
                    {
                        XLogger.Log("Can't Delete the file " + filename);
                    }
                }
            }
            else
            {
                Directory.CreateDirectory(pathToDir);
            }
        }

        /// <summary>
        /// Удаляет файл, если он существует
        /// </summary>
        /// <param name="pathToFile"></param>
        public static void DeleteFileIfExists(string pathToFile)
        {
            if (File.Exists(pathToFile))
                File.Delete(pathToFile);
        }
    }
}