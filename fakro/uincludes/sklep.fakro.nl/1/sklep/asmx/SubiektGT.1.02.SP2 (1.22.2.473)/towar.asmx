<% @WebService Language="C#" Class="subiekt24" %>

using System;
using System.Web;
using System.Data;
using System.Data.SqlClient;
using System.Data.SqlTypes;
using System.Web.Services;
using System.Xml.Serialization;
using System.IO;

[WebService(Namespace="http://firma24.pl/webservices/")]
public class subiekt24 : WebService {

    [WebMethod]
    public Towar pokazTowar(string mag_Symbol, string tw_Symbol)
    {
	   Towar towar = new Towar();
	   towar = pobiezTowar(mag_Symbol,tw_Symbol);
	   return towar;
    }

    [WebMethod]
    public Towar[] listaTowarow(string mag_Symbol)
    {
			string connectionString = pobierzDb();

			SqlConnection conn = new SqlConnection(connectionString);

			conn.Open();

            Towar towar = new Towar();
            Int32 arraySize = 0;

            string queryString = "SELECT COUNT(*)";
            queryString+= "FROM";
            // BEGIN OF FROM SECTION
            queryString+= "[tw_Cena],";
            queryString+= "[sl_Magazyn],";
            queryString+= "[tw__Towar],";
            queryString+= "[tw_Stan],";
            queryString+= "[sl_StawkaVat]";
            // END OF FROM SECTION
            queryString+= "WHERE";
            // BEGIN OF WHERE SECTION
            queryString+= "(([tw_Id] = [st_TowId])";
            queryString+= "AND ([vat_Id]=[tw_IdVatSp])";
            queryString+= "AND ([tw_SklepInternet] = 1)";
            queryString+= "AND ([tw_Id] = [tc_IdTowar])";
            queryString+= "AND ([mag_Id] = [st_MagId])";
            queryString+= "AND ([mag_Symbol] = \'" + mag_Symbol + "\'))";
            // END OF WHERE SECTION

            SqlCommand command = new SqlCommand(queryString,conn);

            SqlDataReader reader = command.ExecuteReader();

            if ((bool)reader.Read() == true)
                arraySize = (Int32)reader.GetValue(0);
            Towar[] towary = new Towar[arraySize];
            reader.Close();

            queryString = "SELECT [tw_Symbol]";
            queryString+= "FROM";
            // BEGIN OF FROM SECTION
            queryString+= "[tw_Cena],";
            queryString+= "[sl_Magazyn],";
            queryString+= "[tw__Towar],";
            queryString+= "[tw_Stan],";
            queryString+= "[sl_StawkaVat]";
            // END OF FROM SECTION
            queryString+= "WHERE";
            // BEGIN OF WHERE SECTION
            queryString+= "(([tw_Id] = [st_TowId])";
            queryString+= "AND ([vat_Id]=[tw_IdVatSp])";
            queryString+= "AND ([tw_SklepInternet] = 1)";
            queryString+= "AND ([tw_Id] = [tc_IdTowar])";
            queryString+= "AND ([mag_Id] = [st_MagId])";
            queryString+= "AND ([mag_Symbol] = \'" + mag_Symbol + "\'))";
            // END OF WHERE SECTION

            command.CommandText = queryString;
            reader = command.ExecuteReader();

            string[] wyniki = new string[arraySize];

            int i = 0;

            while (reader.Read())
               wyniki[i++] = (string)reader.GetValue(0);


            reader.Close();
            conn.Close();

            for (i=0; i < wyniki.Length; i++)
                towary[i] = pobiezTowar(mag_Symbol,wyniki[i]);


            return towary;



    }

	public string pobierzDb()
	{
		string line;
	        string connectionString = "";
		try
		{
			System.IO.StreamReader file = new System.IO.StreamReader(Server.MapPath("")+"\\dbconnect.cfg");
			while((line = file.ReadLine()) != null)
			{
				connectionString = line;
			}
			file.Close();
			return connectionString;
		}
		catch (Exception e)
		{
			return e.Message;
		}
	}

    public Towar pobiezTowar(string mag, string tow)
    {
			string connectionString = pobierzDb();

			SqlConnection connection = new SqlConnection(connectionString);
			connection.Open();

            string queryString = "SELECT";
            // BEGIN OF SELECT SECTION
			queryString+= "[tc_CenaNetto1],";
            queryString+= "[tc_CenaNetto2],";
            queryString+= "[tc_CenaNetto3],";
            queryString+= "[tc_CenaNetto8],";
            queryString+= "[mag_Symbol],";
            queryString+= "[tw_Usuniety],";
            queryString+= "[st_StanRez],";
            queryString+= "[tw_JednMiary],";
            queryString+= "[tw_Symbol],";
            queryString+= "[tc_CenaNetto5],";
            queryString+= "[tc_CenaNetto7],";
            queryString+= "[tc_CenaNetto4],";
            queryString+= "[st_Stan],";
            queryString+= "[tc_CenaNetto6],";
            queryString+= "[tw_Id],";
            queryString+= "[mag_Id],";
            queryString+= "[tw_Nazwa],";
            queryString+= "[tw_Charakter],";
            queryString+= "[vat_Stawka],";
            queryString+= "[tw_IdGrupa],";
            queryString+= "[tw_Opis],";
            queryString+= "[tw_PodstKodKresk]";
            // END OF SELECT SECTION
            queryString+= "FROM";
            // BEGIN OF FROM SECTION
            queryString+= "[tw_Cena],";
            queryString+= "[sl_Magazyn],";
            queryString+= "[tw__Towar],";
            queryString+= "[tw_Stan],";
            queryString+= "[sl_StawkaVat]";
            // END OF FROM SECTION
            queryString+= "WHERE";
            // BEGIN OF WHERE SECTION
            queryString+= "(([tw_Id] = [st_TowId])";
            queryString+= "AND ([vat_Id]=[tw_IdVatSp])";
            queryString+= "AND ([tw_SklepInternet] = 1)";
            queryString+= "AND ([tw_Id] = [tc_IdTowar])";
            queryString+= "AND ([mag_Id] = [st_MagId])";
            queryString+= "AND ([mag_Symbol] = \'" + mag + "\')";
            queryString+= "AND ([tw_Symbol] = \'" + tow + "\'))";
            // END OF WHERE SECTION

//			queryString = "UPDATE [tw__Towar] SET tw_SklepInternet = 1";

            SqlCommand command = new SqlCommand(queryString,connection);
			
//			command.ExecuteScalar();
//			return null;

            SqlDataReader subreader = command.ExecuteReader();

            Towar towar = new Towar();

            if ((bool)subreader.Read() == true)
            {
		
                towar.tw_Nazwa=(string)subreader["tw_Nazwa"];
                towar.tw_Opis=(string)subreader["tw_Opis"];
                //towar.tw_Charakter=(string)reader["tw_Charakter"];
                towar.tw_Symbol=(string)subreader["tw_Symbol"];
                towar.mag_Symbol=(string)subreader["mag_Symbol"];
                towar.tw_Id=(int)subreader["tw_Id"];		

                towar.tw_GrupaId = subreader.GetValue(subreader.GetOrdinal("tw_IdGrupa")).ToString();

                towar.vat_Stawka = subreader.GetValue(subreader.GetOrdinal("vat_Stawka")).ToString();
                towar.tc_CenaNetto1 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto1")).ToString();
                towar.tc_CenaNetto2 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto2")).ToString();
                towar.tc_CenaNetto3 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto3")).ToString();
                towar.tc_CenaNetto4 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto4")).ToString();
                towar.tc_CenaNetto5 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto5")).ToString();
                towar.tc_CenaNetto6 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto6")).ToString();
                towar.tc_CenaNetto7 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto7")).ToString();
                towar.tc_CenaNetto8 = subreader.GetValue(subreader.GetOrdinal("tc_CenaNetto8")).ToString();
                towar.tw_Ean = subreader.GetValue(subreader.GetOrdinal("tw_PodstKodKresk")).ToString();

                if ((bool)subreader["tw_Usuniety"] == true)
                    towar.tw_Usuniety = "1";
                else
                    towar.tw_Usuniety = "0";

                towar.st_Stan = subreader.GetValue(subreader.GetOrdinal("st_Stan")).ToString();
                towar.st_StanRez = subreader.GetValue(subreader.GetOrdinal("st_StanRez")).ToString();
                towar.tw_JednMiary=(string)subreader["tw_JednMiary"];

                subreader.Close();

                command.CommandText = "SELECT [grt_Nazwa] FROM [sl_GrupaTw] WHERE ([grt_id] = "+towar.tw_GrupaId+")";
                towar.tw_Grupa = (string)command.ExecuteScalar();

		queryString = "SELECT * FROM [tw_JednMiary] WHERE ([jm_IdTowar] = "+towar.tw_Id+")";
		command.CommandText = queryString;
		subreader = command.ExecuteReader();
		if ((bool)subreader.Read() == true)
		{
			towar.tw_DodJednostkaMiary = (string)subreader["jm_IdJednMiary"];			
			towar.tw_Przelicznik = subreader.GetValue(subreader.GetOrdinal("jm_Przelicznik")).ToString();			
	                subreader.Close();
		}


                connection.Close();
                return towar;
            }

          subreader.Close();
          connection.Close();
          return null;
    }
}

public class Towar {
	public int tw_Id;
	public string tw_Symbol;
	public string mag_Symbol;
	public string tw_Nazwa;
	public string tw_Opis;
	public string vat_Stawka;
	public string tw_Charakter;
	public string tw_GrupaId;
	public string tw_Grupa;
	public string tc_CenaNetto1;
	public string tc_CenaNetto2;
	public string tc_CenaNetto3;
	public string tc_CenaNetto4;
	public string tc_CenaNetto5;
	public string tc_CenaNetto6;
	public string tc_CenaNetto7;
	public string tc_CenaNetto8;
	public string tw_Usuniety;
	public string st_Stan;
	public string st_StanRez;
	public string tw_JednMiary;
	public string tw_Ean;
	public string tw_DodJednostkaMiary;
	public string tw_Przelicznik;
}

