
using System;
using System.IO;
using System.Text;
using Newtonsoft.Json;

namespace Atlas.Database
{
    /// <summary>
    /// Объект базы данных, описывающий базовую информацию о проекте
    /// </summary>
    [Serializable]
    public class XDBRenderTour
    {
        [JsonProperty("uid")]
        public long Uid { set; get; }

        [JsonProperty("render_uid")]
        public long RenderUid { set; get; }

        [JsonProperty("tour_uniq")]
        public string TourUniq { set; get; }

        [JsonProperty("date_create")]
        public long DateCreate { set; get; }

        [JsonProperty("date_expire")]
        public long DateExpire { set; get; }

        public XDBRenderTour()
        {
        }

        public static XDBRenderTour FromBytes(byte[] bt_data)
        {
            XDBRenderTour db_tour = new XDBRenderTour();
            MemoryStream ms = new MemoryStream(bt_data);
            BinaryReader br = new BinaryReader(ms);

            try
            {
                db_tour.Uid = br.ReadInt64();
                db_tour.RenderUid = br.ReadInt64();

                int len = br.ReadInt32();
                db_tour.TourUniq = Encoding.UTF8.GetString(br.ReadBytes(len));

                db_tour.DateCreate = br.ReadInt64();
                db_tour.DateExpire = br.ReadInt64();
            }
            catch (Exception ex)
            {
                db_tour = null;
            }

            br.Close();
            ms.Close();
            return db_tour;
        }

        public static byte[] ToBytes(XDBRenderTour db_tour)
        {
            MemoryStream ms = new MemoryStream();
            BinaryWriter bw = new BinaryWriter(ms);

            bw.Write(BitConverter.GetBytes((long)db_tour.Uid));
            bw.Write(BitConverter.GetBytes((long)db_tour.RenderUid));

            bw.Write(BitConverter.GetBytes((int)Encoding.UTF8.GetByteCount(db_tour.TourUniq)));
            bw.Write(Encoding.UTF8.GetBytes(db_tour.TourUniq));

            bw.Write(BitConverter.GetBytes((long)db_tour.DateCreate));
            bw.Write(BitConverter.GetBytes((long)db_tour.DateExpire));

            byte[] bt_data = ms.ToArray();
            bw.Close();
            ms.Close();
            return bt_data;
        }
    }
}

