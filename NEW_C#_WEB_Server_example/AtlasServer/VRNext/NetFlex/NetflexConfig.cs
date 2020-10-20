using AtlasServer.ThirdParty;

namespace VRNext.Network
{
    public class NetflexConfig
    {
        private static NetflexConfig config = null;

        private static string config_path = "";

        public const string SECTION_NETWORK = "Network";

        private INIParser iniFile = null;

        static public NetflexConfig Get()
        {
            if (config == null)
            {
                config = new NetflexConfig(config_path);
            }
            return config;
        }

        public NetflexConfig(string path)
        {
            iniFile = new INIParser();
            iniFile.Open(path, true);
            config = this;
        }

        public string MACHINE_ID { get { return iniFile.ReadValue(SECTION_NETWORK, "machine_id", "NULL"); } }
        public bool IP_RESOLVE { get { return iniFile.ReadValue(SECTION_NETWORK, "ip_resolve", true); } }

        public string SERVER_ID { get { return iniFile.ReadValue(SECTION_NETWORK, "SERVER_ID", "NULL"); } }
        public string SERVER_URL { get { return iniFile.ReadValue(SECTION_NETWORK, "SERVER_URL", "NULL"); } }

        public int PORT_WS { get { return int.Parse(iniFile.ReadValue(SECTION_NETWORK, "PORT_WS", "0")); } }
        public int PORT_WS_SSL { get { return int.Parse(iniFile.ReadValue(SECTION_NETWORK, "PORT_WS_SSL", "0")); } }

        public int PORT_WSFS { get { return int.Parse(iniFile.ReadValue(SECTION_NETWORK, "PORT_WSFS", "0")); } }
        public int PORT_WSFS_SSL { get { return int.Parse(iniFile.ReadValue(SECTION_NETWORK, "PORT_WSFS_SSL", "0")); } }

    }
}