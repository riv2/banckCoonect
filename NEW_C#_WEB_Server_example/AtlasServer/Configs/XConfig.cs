using AtlasServer.ThirdParty;

namespace AtlasServer.Configs
{
    public class XConfig
    {
        private const string SECTION_SYSTEM = "system";

        private static XConfig instance = null;

        private INIParser IniFile = null;

        public static void Init(string configPath)
        {
            instance = new XConfig(configPath);
        }

        private XConfig(string path)
        {
            IniFile = new INIParser();
            IniFile.Open(path, true);
        }

        public static string SECRET_KEY { get { return instance.IniFile.ReadValue(SECTION_SYSTEM, "SecretKey", "") ; } }
        public static int PORT { get { return instance.IniFile.ReadValue(SECTION_SYSTEM, "Port", 6282); } }
    }
}

