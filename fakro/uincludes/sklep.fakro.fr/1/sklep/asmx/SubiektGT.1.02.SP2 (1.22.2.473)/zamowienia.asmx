<% @WebService Language="C#" Class="subiekt24" %>

using System;
using System.Web;
using System.Data;
using System.Data.SqlClient;
using System.Data.SqlTypes;
using System.Web.Services;
using System.Xml.Serialization;
using System.Web.Services.Protocols;
using System.Globalization;

[WebService(Namespace="http://firma24.pl/webservices/")]
public class subiekt24 : WebService {

    [WebMethod]
    public string noweZamowienie(string mag_Symbol, Zamowienie zamowienie)
    {

	Kontrahent tmpK = new Kontrahent();

	tmpK = zamowienie.kontrahent;

        NumberFormatInfo nfi = new NumberFormatInfo();
        nfi.NumberDecimalSeparator = ".";

        DateTime czas = new DateTime(10);
        czas = DateTime.Now;
        string dzisiaj = czas.Year.ToString()+"-"+czas.Month.ToString()+"-"+czas.Day.ToString();

        int returnVal = 0;
        string errString = "";
        string connectionString = pobierzDb();

        SqlConnection dbConnection = new SqlConnection(connectionString);

		dbConnection.Open();

        int kh_id = 0;
        int adr_id = 0;

        string queryString = "SELECT " +
        "[kh_Id] FROM [kh__Kontrahent] " +
        "WHERE ([kh_Symbol] = \'"+tmpK.ko_kod+"\')";
		

        SqlCommand command = new SqlCommand();
        command.CommandText = queryString;
        command.Connection = dbConnection;

        SqlDataReader reader = command.ExecuteReader();
		

        bool Any = reader.Read();
        if (Any) kh_id = reader.GetInt32(0);
        reader.Close();

        queryString = "BEGIN TRANSACTION";
        command.CommandText = queryString;
        command.ExecuteNonQuery();

        int adrh_id = -1;
	    int ile = -1;
		string sumsql = "";
        try
        {
            //jeÅ¼eli go nie ma to go dodajemy
            if (!Any)
            {
                queryString = "SELECT MAX([kh_Id]) AS [max_kh] FROM [kh__Kontrahent]";
                command.CommandText = queryString;

				try
				{
	                kh_id = (int)command.ExecuteScalar();
			        kh_id++;
				}
				catch
                {
                    
					queryString = "SELECT COUNT(*) FROM [kh__Kontrahent]";
					command.CommandText = queryString;
					ile = (int)command.ExecuteScalar();
					if (ile > 0)
					{
						errString = "BÅÄd przy pobieraniu identyfikatora kontrahenta.";
						Exception noId = new Exception(errString);
						throw (noId);
					}
					else
					{
						kh_id=5;
					}

                }

				queryString = "INSERT INTO [kh__Kontrahent] ("+
				"[kh_Id],"+
				"[kh_Symbol],"+
				"[kh_Rodzaj],"+
				"[kh_REGON],"+
				"[kh_CentrumAut],"+
				"[kh_InstKredytowa],"+
				"[kh_WWW],"+
				"[kh_EMail],"+
				"[kh_IdGrupa],"+
				"[kh_PlatOdroczone],"+
				"[kh_OdbDet],"+
				"[kh_MaxDokKred],"+
				"[kh_MaxDniSp],"+
				"[kh_ZgodaDO],"+
				"[kh_ZgodaMark],"+
				"[kh_ZgodaEMail],"+
				"[kh_CzyKomunikat],"+
				"[kh_ProcKarta],"+
				"[kh_ProcKredyt],"+
				"[kh_ProcGotowka],"+
				"[kh_ProcPozostalo],"+
				"[kh_PodVATZarejestrowanyWUE]) VALUES ("+
				""+kh_id+","+ 
				"\'"+tmpK.ko_kod+"\', "+
				"0, "+
				"'', "+
				"0, "+
				"0, "+
				"'', "+
				"\'"+tmpK.ko_email+"\', "+
				"1, "+
				"1, "+
				"0, "+
				"0, "+
				"0, "+
				"0, "+
				"0, "+
				"0, "+
				"0, "+
				"$0.0000, "+
				"$0.0000, "+
				"$100.0000, "+
				"$0.0000, "+
				"0)";
				sumsql += queryString;
                command.CommandText = queryString;
                if (command.ExecuteNonQuery()==0)
                {
                    errString = "BÅÄd przy dodawaniu kontrahenta";
                    Exception noInsert = new Exception(errString);
                    throw (noInsert);

                }

            }

            queryString = "SELECT " +
            "[adr_Id] FROM [adr__Ewid] " +
            "WHERE ([adr_Symbol] = \'"+tmpK.ko_kod+"\')";
            command.CommandText = queryString;

			try
			{
				adr_id = (int)command.ExecuteScalar();
				Any = true;
			}
			catch
			{
				Any = false;
			}
		
            if (!Any)
            {
                int obiekt_id = 0;
                queryString = "SELECT MAX([adr_Id]) AS [max_adr], MAX([adr_IdObiektu]) FROM [adr__Ewid]";
                command.CommandText = queryString;
                reader = command.ExecuteReader();
                if (!reader.Read())
                {
					queryString = "SELECT COUNT(*) FROM [adr__Ewid]";
					command.CommandText = queryString;
					ile = (int)command.ExecuteScalar();
					if (ile > 0)
					{
						errString = "BÅÄd przy pobieraniu identyfikatora ewid";
						Exception noId = new Exception(errString);
						throw (noId);
					}
					else
					{
						adr_id = 5;
						obiekt_id = 5;
					}
				}
				else
				{
					adr_id = reader.GetInt32(0);
					obiekt_id = reader.GetInt32(1);
					adr_id++;
					obiekt_id++;
					reader.Close();
				}

//				return null;
                int typadd = 1;

                queryString = "INSERT INTO [adr__Ewid]"+
                "("+
                "[adr_Id],"+
	               "[adr_IdObiektu],"+
	               "[adr_TypAdresu],"+
	               "[adr_Nazwa],"+
	               "[adr_NazwaPelna],"+
	               "[adr_Telefon],"+
	               "[adr_Faks],"+
	               "[adr_Ulica],"+
	               "[adr_NrDomu],"+
	               "[adr_NrLokalu],"+
	               "[adr_Kod],"+
	               "[adr_Miejscowosc],"+
	               "[adr_NIP],"+
	               "[adr_Poczta],"+
	               "[adr_Gmina],"+
	               "[adr_Powiat],"+
	               "[adr_Skrytka],"+
	               "[adr_IdPanstwo],"+
	               "[adr_Symbol]"+
                ") VALUES ("+
                ""+adr_id+","+
	               ""+kh_id+","+
	               ""+typadd+","+
	               "\'"+tmpK.ko_nazwa+"\',"+
	               "\'"+tmpK.ko_nazwa_p+"\',"+
	               "\'"+tmpK.ko_telefon+"\',"+
	               "\'\',"+
	               "\'"+tmpK.ko_ulica+"\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'"+tmpK.ko_kod_pocztowy+"\',"+
	               "\'"+tmpK.ko_miasto+"\',"+
	               "\'"+tmpK.ko_nip+"\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "1,"+
	               "\'"+tmpK.ko_kod+"\');";

                command.CommandText = queryString;
				sumsql += queryString;
                if (command.ExecuteNonQuery()==0)
                {
                    errString = "BÅÄd przy dodawaniu ewid";
                    Exception noInsert = new Exception(errString);
                    throw (noInsert);

                }

                typadd = 2;
                queryString = "INSERT INTO [adr__Ewid]"+
                "("+
                "[adr_Id],"+
	               "[adr_IdObiektu],"+
	               "[adr_TypAdresu],"+
	               "[adr_Nazwa],"+
	               "[adr_NazwaPelna],"+
	               "[adr_Telefon],"+
	               "[adr_Faks],"+
	               "[adr_Ulica],"+
	               "[adr_NrDomu],"+
	               "[adr_NrLokalu],"+
	               "[adr_Kod],"+
	               "[adr_Miejscowosc],"+
	               "[adr_NIP],"+
	               "[adr_Poczta],"+
	               "[adr_Gmina],"+
	               "[adr_Powiat],"+
	               "[adr_Skrytka],"+
	               "[adr_IdPanstwo],"+
	               "[adr_Symbol]"+
                ") VALUES ("+
                ""+(adr_id+1)+","+
	               ""+kh_id+","+
	               ""+typadd+","+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "1,"+
	               "\'\');";

                command.CommandText = queryString;
				sumsql += queryString;
                if (command.ExecuteNonQuery()==0)
                {
                    errString = "BÅÄd przy dodawaniu ewid";
                    Exception noInsert = new Exception(errString);
                    throw (noInsert);

                }

                queryString = "SELECT MAX([adrh_Id]) AS [max_adr] FROM [adr_Historia]";
                command.CommandText = queryString;


				try
				{
					adrh_id = (int)command.ExecuteScalar();
					adrh_id++;
				}
				catch
				{
					queryString = "SELECT COUNT(*) FROM [adr_Historia]";
					command.CommandText = queryString;
					ile = (int)command.ExecuteScalar();
					if (ile >0)
					{
						errString = "BÅÄd przy pobieraniu identyfikatora hist";
						Exception noId = new Exception(errString);
						throw (noId);
					}
					else
					{
						adrh_id=5;
					}
				}
				
				queryString = "SELECT MIN([adr_id]) FROM [adr__Ewid] WHERE ([adr_Symbol] = \'"+tmpK.ko_kod+"\')";
				command.CommandText = queryString;
				int objid = (int)command.ExecuteScalar();

                queryString = "INSERT INTO [adr_Historia]"+
                "("+
                "[adrh_Id],"+
	               "[adrh_IdAdresu],"+
	               "[adrh_Nazwa],"+
	               "[adrh_NazwaPelna],"+
	               "[adrh_Telefon],"+
	               "[adrh_Faks],"+
	               "[adrh_Ulica],"+
	               "[adrh_NrDomu],"+
	               "[adrh_NrLokalu],"+
	               "[adrh_Kod],"+
	               "[adrh_Miejscowosc],"+
	               "[adrh_NIP],"+
	               "[adrh_Poczta],"+
	               "[adrh_Gmina],"+
	               "[adrh_Powiat],"+
	               "[adrh_Skrytka],"+
	               "[adrh_IdPanstwo],"+
	               "[adrh_Symbol]"+
                ") VALUES ("+
                ""+adrh_id+","+
	               ""+objid+","+
	               "\'"+tmpK.ko_nazwa+"\',"+
	               "\'"+tmpK.ko_nazwa_p+"\',"+
	               "\'"+tmpK.ko_telefon+"\',"+
	               "\'\',"+
	               "\'"+tmpK.ko_ulica+"\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'"+tmpK.ko_kod_pocztowy+"\',"+
	               "\'"+tmpK.ko_miasto+"\',"+
	               "\'"+tmpK.ko_nip+"\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "\'\',"+
	               "1,"+
	               "\'"+tmpK.ko_kod+"\');";

                command.CommandText = queryString;
				sumsql += queryString;
                if (command.ExecuteNonQuery()==0)
                {
                    errString = "BÅÄd przy dodawaniu historii";
                    Exception noInsert = new Exception(errString);
                    throw (noInsert);

                }


            }
			
            if (adrh_id == -1)
            {
                queryString = "SELECT MAX([adrh_Id]) AS [max_hist] FROM [adr_Historia] WHERE [adrh_Symbol] = \'"+tmpK.ko_kod+"\'";
                command.CommandText = queryString;
				try
				{
					adrh_id = (int)command.ExecuteScalar();	
					//adrh_id++;
				}
				catch
				{                    
					queryString = "SELECT COUNT(*) FROM [adr_Historia] WHERE [adrh_Symbol] = \'"+tmpK.ko_kod+"\'";
					command.CommandText = queryString;
					ile = (int)command.ExecuteScalar();
					if (ile > 0)
					{
						errString = "BÅÄd przy pobieraniu nr historii";
						Exception noId = new Exception(errString);
						throw (noId);
					}
					else
					{
						adrh_id = 5;
					}

				 }
			 }

            queryString = "SELECT MAX([dok_Id]) AS [max_dok] FROM [dok__Dokument]";
            command.CommandText = queryString;
			int dok_id;

			try
			{
				dok_id = (int)command.ExecuteScalar();
				dok_id++;
			}
			catch
			{
				queryString = "SELECT COUNT(*) FROM [dok__Dokument]";
				command.CommandText = queryString;
				ile = (int)command.ExecuteScalar();
				if (ile > 0)
				{
					errString = "BÅÄd przy pobieraniu nr dok";
					Exception noId = new Exception(errString);
					throw (noId);
				}
				else
				{
					dok_id=5;
				}
	
            }


			double wartoscZam = zamowienie.wartoscZamowienia();
            double wartoscBruttoZam = zamowienie.wartoscBruttoZamowienia();
            double wartoscVat = zamowienie.wartoscBruttoZamowienia()-zamowienie.wartoscZamowienia();

            //Wrzucamy zamowienia
            queryString = "INSERT INTO [dok__Dokument] ("+
           "[dok_Id]," +
	       "[dok_Typ]," +
	       "[dok_Podtyp]," +
	       "[dok_MagId]," +
	       "[dok_NrPelny]," +
	       "[dok_NrPelnyOryg]," +
	       "[dok_DoDokNrPelny]," +
	       "[dok_PlatTermin]," +
	       "[dok_MscWyst]," +
	       "[dok_DataWyst]," +
	       "[dok_DataMag]," +
	       "[dok_PersonelId]," +
	       "[dok_CenyPoziom]," +
	       "[dok_KwDoZaplaty]," +
	       "[dok_JestVatAuto]," +
	       "[dok_PlatnikId]," +
	       "[dok_PlatnikAdreshId]," +
	       "[dok_OdbiorcaId]," +
	       "[dok_OdbiorcaAdreshId]," +
	       "[dok_Wystawil]," +
	       "[dok_Odebral]," +
	       "[dok_CenyTyp]," +
	       "[dok_CenyKurs]," +
	       "[dok_WartUsNetto]," +
	       "[dok_WartUsBrutto]," +
	       "[dok_WartTwNetto]," +
	       "[dok_WartTwBrutto]," +
	       "[dok_WartMag]," +
	       "[dok_WartMagP]," +
	       "[dok_WartMagR]," +
	       "[dok_WartNetto]," +
	       "[dok_WartVat]," +
	       "[dok_WartBrutto]," +
	       "[dok_KwWartosc]," +
	       "[dok_KwGotowka]," +
	       "[dok_Waluta]," +
	       "[dok_Uwagi]," +
	       "[dok_KatId]," +
	       "[dok_Tytul]," +
	       "[dok_Podtytul]" +
            ") VALUES ("+
            ""+dok_id+"," +
	       "16," +
	       "0," +
	       "1," +
	       "\'"+zamowienie.za_numer+"\'," +
	       "\'"+zamowienie.za_numer+"\'," +
	       "\'\'," +
	       "'"+dzisiaj+"'," +
	       "\'PoznaÅ\'," +
	       "'"+dzisiaj+"'," +
	       "'"+dzisiaj+"'," +
	       "1," +
	       "2," +
	       "0," +
	       "1," +
	       ""+kh_id+"," +
	       ""+adrh_id+"," +
	       ""+kh_id+"," +
	       ""+adrh_id+"," +
	       "\'\'," +
	       "\'\'," +
	       "1," +
	       "1," +
	       "0," +
	       "0," +
	       " "+wartoscZam.ToString(nfi)+" ," +
	       " "+wartoscBruttoZam.ToString(nfi)+" ," +
	       "0," +
	       "0," +
	       "0," +
	       " "+wartoscZam.ToString(nfi)+" ," +
	       " "+wartoscVat.ToString(nfi)+"," +
	       " "+wartoscBruttoZam.ToString(nfi)+" ," +
	       " "+wartoscBruttoZam.ToString(nfi)+" ," +
	       "0," +
	       "\'PLN\'," +
           "\'"+zamowienie.za_numer+"\'," +
	       "1," +
	       "\'zamÃ³wienie od klienta\'," +
	       "\'"+zamowienie.kontrahent.ko_kod+"\')";

            command.CommandText = queryString;
			sumsql += queryString;
            if (command.ExecuteNonQuery()==0)
            {
                errString = "BÅÄd przy dodawaniu zamÃ³wienia";
                Exception noInsert = new Exception(errString);
                throw (noInsert);

            }

			returnVal = dok_id;

            Pozycja poz = new Pozycja();

            double brutto = 0;
            double vat = 0;
            double wNetto = 0;
            double wBrutto = 0;
            double wVat = 0;

            int ob_id = 0;
            int tw_id = 0;

            queryString = "SELECT MAX([ob_Id]) AS [max_ob] FROM [dok_Pozycja]";
            command.CommandText = queryString;

			try
			{
				ob_id = (int)command.ExecuteScalar();
			}	
			catch
			{
				queryString = "SELECT COUNT(*) FROM [dok_Pozycja]";
				command.CommandText = queryString;
				ile = (int)command.ExecuteScalar();
				if (ile > 0)
				{
					errString = "BÅÄd przy pobieraniu nr pozycji";
					Exception noId = new Exception(errString);
					throw (noId);
				}
				else
				{
				   ob_id = 5;
				}

			}

            int vat_id = 0;

            for (int i=0; i< zamowienie.pozycje.Length; i++)
            {

                poz = zamowienie.pozycje[i];

                queryString = "SELECT [vat_Id] FROM [sl_StawkaVat]"+
                "WHERE ([vat_Symbol] = \'"+poz.po_vat+"\')";

                command.CommandText = queryString;
                try
                {
                    vat_id = (int)command.ExecuteScalar();
                }
                catch
                {
                    errString = "Brak stawki VAT: "+poz.po_vat+" w bazie.";
                    Exception noArticle = new Exception(errString);
                    throw (noArticle);

                }

                ob_id++;

                queryString = "SELECT [tw_Id] FROM [tw__Towar]"+
                "WHERE ([tw_Symbol] = \'"+poz.po_towar+"\')";

                command.CommandText = queryString;
                try
                {
                    tw_id = (int)command.ExecuteScalar();
                }
                catch
                {
                    errString = "Brak towaru o kodzie "+poz.po_towar+" w bazie.";
                    Exception noArticle = new Exception(errString);
                    throw (noArticle);

                }

                vat = poz.po_cena*(poz.po_vat/100);
                brutto = poz.po_cena-(poz.po_cena*(poz.po_rabat/100)) + vat;
                wNetto = poz.wartoscPozycji();
                wVat = vat*poz.po_ilosc;
                wBrutto = poz.wartoscBruttoPozycji();

                queryString = "INSERT INTO [dok_Pozycja] ("+
	           "[ob_Id],"+
	           "[ob_DokHanId],"+
	           "[ob_Znak],"+
	           "[ob_TowId],"+
	           "[ob_Ilosc],"+
	           "[ob_Jm],"+
	           "[ob_CenaWaluta],"+
	           "[ob_CenaNetto],"+
	           "[ob_CenaBrutto],"+
	           "[ob_WartNetto],"+
	           "[ob_WartVat],"+
	           "[ob_WartBrutto],"+
	           "[ob_VatProc],"+
	           "[ob_VatId],"+
	           "[ob_Rabat],"+
	           "[ob_Termin]"+
                ") VALUES ("+
	           ""+ob_id+","+
	           ""+dok_id+","+
	           "1,"+
	           ""+tw_id+","+
	           ""+poz.po_ilosc.ToString(nfi)+","+
	           "\'szt.\',"+
	           ""+poz.po_cena.ToString(nfi)+","+
	           ""+poz.po_cena.ToString(nfi)+","+
	           ""+brutto.ToString(nfi)+","+
	           ""+wNetto.ToString(nfi)+","+
	           ""+wVat.ToString(nfi)+","+
	           ""+wBrutto.ToString(nfi)+","+
	           ""+poz.po_vat.ToString(nfi)+","+
	           ""+vat_id+","+
	           ""+poz.po_rabat.ToString(nfi)+","+
	           "'"+dzisiaj+"')";
/*				
				sumsql += queryString;
				errString = sumsql;
                Exception noIns = new Exception(errString);
                throw (noIns);
				return null;
*/
                command.CommandText = queryString;
                if (command.ExecuteNonQuery()==0)
                {
                    errString = "BÅÄd przy dodawaniu pozycj";
                    Exception noInsert = new Exception(errString);
                    throw (noInsert);

                }

            } //for

        } // try
        catch (Exception e)
        {
            queryString = "ROLLBACK";
            command.CommandText = queryString;
            command.ExecuteNonQuery();
            dbConnection.Close();
            throw (e);

        }

        queryString = "SELECT [dok_NrPelny] FROM dok__Dokument WHERE ([dok_Id] = "+returnVal+")";
        command.CommandText = queryString;
        string nrDok = (string)command.ExecuteScalar();

        queryString = "COMMIT";
        command.CommandText = queryString;
        command.ExecuteNonQuery();
        dbConnection.Close();
        
        return nrDok;

	}//zamowienie

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

	[WebMethod]
	public Realizacja statusZamowienia(string mag_Symbol, string nr_Zam)
	{

		string connectionString = pobierzDb();

		SqlConnection dbConnection = new SqlConnection(connectionString);

		dbConnection.Open();

		Realizacja realizacja = new Realizacja();		

		string qS = "SELECT [mag_Id] FROM [sl_Magazyn] WHERE ([mag_Symbol] = \'"+mag_Symbol+"\')";
		SqlCommand command = new SqlCommand();
		command.Connection = dbConnection;
		command.CommandText = qS;

		int mag = 1;

		try
		{
			mag = (int)command.ExecuteScalar();
		}
		catch(Exception e)
		{
			dbConnection.Close();
			return null;
		}

		qS = "SELECT [dok_DoDokNrPelny] FROM [dok__Dokument] WHERE ([dok_NrPelny] = \'"+nr_Zam+"\' AND [dok_MagId] = "+mag+")";


		command.CommandText = qS;

		string fvSymb = "";
		try
		{
			fvSymb = (string)command.ExecuteScalar();
		}
		catch(Exception e)
		{
			dbConnection.Close();
			return null;
		}


		realizacja.nr_dokumentu = fvSymb;

		qS = "SELECT * FROM [dok__Dokument] WHERE ([dok_NrPelny] = \'"+fvSymb+"\' AND [dok_MagId] = "+mag+")";
		command.CommandText = qS;

	        SqlDataReader reader = command.ExecuteReader();

		int po;
		if (reader.Read())
		{
			po = (int)reader.GetValue(reader.GetOrdinal("dok_Id"));
			realizacja.wartosc_netto = reader.GetValue(reader.GetOrdinal("dok_WartTwNetto")).ToString();
			realizacja.wartosc_brutto = reader.GetValue(reader.GetOrdinal("dok_WartTwBrutto")).ToString();
			reader.Close();
		}
		else
		{
			dbConnection.Close();
			return null;
		}
				
		dbConnection.Close();

		realizacja.pozycje = pobierzPoz(po);

		return realizacja;
	}

	public Pozycja[] pobierzPoz(int poz_Id)
	{
		

		string connectionString = pobierzDb();

		SqlConnection dbConnection = new SqlConnection(connectionString);

		dbConnection.Open();
		
		SqlCommand command = new SqlCommand();

		string qS = "SELECT COUNT(*) FROM [dok_Pozycja] WHERE ([ob_DokHanId] = "+poz_Id+")";

		command.CommandText = qS;
		command.Connection = dbConnection;

		int ile;
		try
		{
			ile = (int)command.ExecuteScalar();
		}
		catch (Exception e)
		{
			dbConnection.Close();
			return null;
		}

		Pozycja[] poz = new Pozycja[ile];


		qS = "SELECT * FROM [tw__Towar], [dok_Pozycja] WHERE ([tw_Id] = [ob_TowId] AND [ob_DokHanId] = "+poz_Id+")";
		command.CommandText = qS;
	        
		SqlDataReader reader = command.ExecuteReader();

		int i = 0;
		
		double cena;
		double rabat;
		string t;

		while (reader.Read())
		{
			poz[i] = new Pozycja();
			poz[i].po_towar = (string)reader.GetValue(reader.GetOrdinal("tw_Symbol"));
			t = reader.GetValue(reader.GetOrdinal("ob_Ilosc")).ToString();
			poz[i].po_ilosc = System.Convert.ToDouble(t);
			t = reader.GetValue(reader.GetOrdinal("ob_CenaNetto")).ToString();
			cena = System.Convert.ToDouble(t);
			t = reader.GetValue(reader.GetOrdinal("ob_Rabat")).ToString();
			rabat = System.Convert.ToDouble(t);
			poz[i++].po_ost_cena_netto = cena - (cena*(rabat/100));
		}
		reader.Close();

		return poz;

		dbConnection.Close();

	}

}//class


public class Zamowienie {
    public string za_numer;
    public string za_uwagi;
    public string za_data;
    public string za_adres;
    public string za_dostawa;
    public string za_platnosc;
    public Pozycja[] pozycje;
    public Kontrahent kontrahent;

    public double wartoscZamowienia()
    {
        double sumaCen = 0;
        for (int i = 0; i < pozycje.Length; i++)
            sumaCen += pozycje[i].wartoscPozycji();
        return sumaCen;
    }

    public double wartoscBruttoZamowienia()
    {
        double sumaCen = 0;
        for (int i = 0; i < pozycje.Length; i++)
            sumaCen += pozycje[i].wartoscBruttoPozycji();
        return sumaCen;
    }

}


public class Pozycja {
    public double po_ilosc;
    public double po_cena;
    public double po_vat;
    public double po_rabat;
    public string po_towar;
    public double po_ost_cena_netto;

    public double wartoscPozycji()
    {
        return po_ilosc * (po_cena - (po_cena*(po_rabat/100)));
    }

    public double wartoscBruttoPozycji()
    {
        return po_ilosc * ((po_cena - (po_cena*(po_rabat/100))) + (po_cena*(po_vat/100)));
    }

}

public class Realizacja {
    public string wartosc_netto;
    public string wartosc_brutto;
    public string nr_dokumentu;
    public Pozycja[] pozycje;
}

public class Kontrahent {
    public string ko_nazwa;
    public string ko_nazwa_p;
    public string ko_kod;
    public string ko_ulica;
    public string ko_kod_pocztowy;
    public string ko_miasto;
    public string ko_telefon;
    public string ko_email;
    public string ko_nip;
}
