using System.IO;
using System.Text;
using System.Collections.Generic;

namespace VRNext.WebSocket
{
    public enum WSOperation
    {
        PING = 0x01,
        PONG = 0x02,

        JOIN_SESSION_ID = 0x10,
        RENAME_SESSION_ID = 0x11,

        ENTER_SESSION_ID = 0x30,
        LEAVE_SESSION_ID = 0x31,
        CHANGE_SESSION_ID = 0x32,
        ACTIVE_SESSION_ID = 0x33,

        REQUEST_CATALOGUE_FILE = 0x50,
        RESPONSE_CATALOGUE_FILE = 0x51,
        REQUEST_RENDER_FILE = 0x52,
        RESPONSE_RENDER_FILE = 0x53,

        REQUEST_DATA_INFO = 0x60,
        RESPONSE_DATA_INFO = 0x61,
        REQUEST_DATA_PART = 0x62,
        RESPONSE_DATA_PART = 0x63,

        RECIPIENT_OFFLINE = 0xE1,
    }

    public enum WSArgumentType
    {
        BINARY = 0,
        INTGER = 1,
        LONG = 2,
        SINGLE = 3,
        DOUBLE = 4,
        BOOLEAN = 5,
        STRING = 6
    };

    public class WSArgument
    {
        public WSArgumentType type;
        public object value;

        public WSArgument(WSArgumentType type, object value)
        {
            this.type = type;
            this.value = value;
        }

        public byte[] GetBytes()
        {
            return (byte[])value;
        }

        public int GetInt()
        {
            return (int)value;
        }

        public long GetLong()
        {
            return (long)value;
        }

        public float GetSingle()
        {
            return (float)value;
        }

        public double GetDouble()
        {
            return (double)value;
        }

        public bool GetBool()
        {
            return (bool)value;
        }

        public string GetString()
        {
            return (string)value;
        }
    }

    public class WSCommand
    {
        public WSOperation operation;
        public List<WSArgument> arguments;

        public WSCommand(WSOperation operation, List<WSArgument> arguments)
        {
            this.operation = operation;
            this.arguments = arguments;
        }

        public static byte[] Serialize(WSCommand command)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bwr = new BinaryWriter(ms);
            bwr.Write(System.BitConverter.GetBytes((int)command.operation));
            bwr.Write(System.BitConverter.GetBytes(command.arguments.Count));
            for (int i = 0; i < command.arguments.Count; i++)
            {
                bwr.Write(System.BitConverter.GetBytes((int)command.arguments[i].type));

                switch (command.arguments[i].type)
                {
                    case WSArgumentType.INTGER:
                        bwr.Write(System.BitConverter.GetBytes((int)command.arguments[i].value));
                        break;
                    case WSArgumentType.LONG:
                        bwr.Write(System.BitConverter.GetBytes((long)command.arguments[i].value));
                        break;
                    case WSArgumentType.SINGLE:
                        bwr.Write(System.BitConverter.GetBytes((float)command.arguments[i].value));
                        break;
                    case WSArgumentType.DOUBLE:
                        bwr.Write(System.BitConverter.GetBytes((double)command.arguments[i].value));
                        break;
                    case WSArgumentType.BOOLEAN:
                        bwr.Write(System.BitConverter.GetBytes((bool)command.arguments[i].value));
                        break;
                    case WSArgumentType.STRING:
                        byte[] bytes = Encoding.UTF8.GetBytes((string)command.arguments[i].value);
                        bwr.Write(System.BitConverter.GetBytes(bytes.Length));
                        bwr.Write(bytes);
                        break;
                    case WSArgumentType.BINARY:
                        byte[] raw = (byte[])command.arguments[i].value;
                        bwr.Write(System.BitConverter.GetBytes(raw.Length));
                        bwr.Write(raw);
                        break;
                }
            }
            return ms.ToArray();
        }

        public static WSCommand Deserialize(byte[] command_data)
        {
            MemoryStream ms = new MemoryStream(command_data);
            BinaryReader brd = new BinaryReader(ms);

            WSOperation operation = (WSOperation)brd.ReadInt32();
            List<WSArgument> arguments = new List<WSArgument>();

            int arguments_acount = brd.ReadInt32();
            for (int i = 0; i < arguments_acount; i++)
            {
                WSArgumentType argument_type = (WSArgumentType)brd.ReadInt32();
                object argument_value = null;
                switch (argument_type)
                {
                    case WSArgumentType.INTGER:
                        argument_value = brd.ReadInt32();
                        break;
                    case WSArgumentType.LONG:
                        argument_value = brd.ReadInt64();
                        break;
                    case WSArgumentType.SINGLE:
                        argument_value = brd.ReadSingle();
                        break;
                    case WSArgumentType.DOUBLE:
                        argument_value = brd.ReadDouble();
                        break;
                    case WSArgumentType.BOOLEAN:
                        argument_value = brd.ReadBoolean();
                        break;
                    case WSArgumentType.STRING:
                        int str_len = brd.ReadInt32();
                        byte[] bytes = brd.ReadBytes(str_len);
                        argument_value = Encoding.UTF8.GetString(bytes);
                        break;
                    case WSArgumentType.BINARY:
                        int raw_len = brd.ReadInt32();
                        argument_value = brd.ReadBytes(raw_len);
                        break;
                }

                arguments.Add(new WSArgument(argument_type, argument_value));
            }

            return new WSCommand(operation, arguments);
        }
    }
}