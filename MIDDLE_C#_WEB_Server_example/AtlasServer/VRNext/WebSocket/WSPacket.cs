using System.IO;
using System.Text;

namespace VRNext.WebSocket
{
    public class WSPacket
    {
        public string sender_id;
        public string recipient_id;
        public byte[] data;

        public WSPacket(string sender_id, string recipient_id, byte[] data)
        {
            this.sender_id = sender_id;
            this.recipient_id = recipient_id;
            this.data = data;
        }

        public static byte[] Serialize(WSPacket packet)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bwr = new BinaryWriter(ms);
            WriteBytes(bwr, Encoding.UTF8.GetBytes(packet.sender_id));
            WriteBytes(bwr, Encoding.UTF8.GetBytes(packet.recipient_id));
            WriteBytes(bwr, packet.data);
            return ms.ToArray();
        }

        public static WSPacket Deserialize(byte[] packet_data)
        {
            try
            {
                MemoryStream ms = new MemoryStream(packet_data);
                BinaryReader brd = new BinaryReader(ms);
                string sender_id = Encoding.UTF8.GetString(ReadBytes(brd));
                string recipient_id = Encoding.UTF8.GetString(ReadBytes(brd));
                byte[] data = ReadBytes(brd);
                return new WSPacket(sender_id, recipient_id, data);
            }
            catch(System.Exception ex)
            {
                XLogger.LogException(ex);
                return null;
            }
        }

        private static void WriteBytes(BinaryWriter bwr, byte[] value)
        {
            bwr.Write(System.BitConverter.GetBytes(value.Length));
            bwr.Write(value);
        }

        private static byte[] ReadBytes(BinaryReader brd)
        {
            int length = brd.ReadInt32();
            return brd.ReadBytes(length);
        }
    }
}