<?
	$osoby_fields="";
	// label, nazwa pola, grupa


	$osoby_fields[]=array(sysmsg('th_name','system'),"su_nazwisko","personal",80);

	$osoby_fields[]=array(sysmsg('th_login','system'),"su_login","login",64);
	$osoby_fields[]=array(sysmsg('th_password','system'),"su_pass","login",64);


	$osoby_fields[]=array(sysmsg('th_addess','system'),"su_ulica","address",80);
	$osoby_fields[]=array(sysmsg('th_zip','system'),"su_kod_pocztowy","address",6);
	$osoby_fields[]=array(sysmsg('th_town','system'),"su_miasto","address",80);


	$osoby_fields[]=array(sysmsg('th_telephone','system'),"su_telefon","contact",30);
	$osoby_fields[]=array(sysmsg('th_handy','system'),"su_gsm","contact",30);
	$osoby_fields[]=array(sysmsg('th_email','system'),"su_email","contact",80);
	$osoby_fields[]=array(sysmsg('th_delivery_address','system').' 1',"su_adres1","contact",50,4);
	$osoby_fields[]=array(sysmsg('th_delivery_address','system').' 2',"su_adres2","contact",50,4);
	$osoby_fields[]=array(sysmsg('th_delivery_address','system').' 3',"su_adres3","contact",50,4);


	$osoby_fields[]=array(sysmsg('Tax Id','system'),"su_nip","tax",10);
	$osoby_fields[]=array(sysmsg('Delivery','system'),"su_dostawa","tax",20);
	$osoby_fields[]=array(sysmsg('Payment','system'),"su_platnosc","tax",20);



	$osoby_fields[]=array(sysmsg("crm_title_1","crm"),"su_wyroznik1","wyr",3,
					explode(",",sysmsg("crm_variables_1","crm")));

	$osoby_fields[]=array(sysmsg("crm_title_2","crm"),"su_wyroznik2","wyr",3,
					explode(",",sysmsg("crm_variables_2","crm")));

	$osoby_fields[]=array(sysmsg("crm_title_3","crm"),"su_wyroznik3","wyr",10,
					explode(",",sysmsg("crm_variables_3","crm")));

	$osoby_fields[]=array(sysmsg('th_baby_sitter','system'),"su_opiekun","wyr",1,array());

	$osoby_fields[]=array(sysmsg('th_balance','system'),"su_saldo","tax",20,"f");

	$osoby_fields[]=array(sysmsg('th_regon','system'),"su_regon","tax",20);
	$osoby_fields[]=array(sysmsg('th_payment_term','system'),"su_termin_platnosci","tax",20);



	$osoby_fields_grupy["personal"]="Dane osobowe";
	$osoby_fields_grupy["login"]="Identyfikacja elektroniczna";
	$osoby_fields_grupy["address"]="Adres";
	$osoby_fields_grupy["contact"]="Dane kontaktowe";
	$osoby_fields_grupy["tax"]="Identyfikacja sformalizowana";
	$osoby_fields_grupy["wyr"]="WyrѓПniki";

	$osoby_fields_validate["su_nazwisko"]=array(1,1,80);
	$osoby_fields_validate["su_imiona"]=array(1,1,50);
	$osoby_fields_validate["su_kod_pocztowy"]=array(1,6,6);
	$osoby_fields_validate["su_p_kod_pocztowy"]=array(1,6,6);
	$osoby_fields_validate["su_login"]=array(1,6,32);
	$osoby_fields_validate["su_pesel"]=array(1,11,11);
	$osoby_fields_validate["su_email"]=array('email',0,100);
	$osoby_fields_validate["su_ulica"]=array(1,4,80);
	$osoby_fields_validate["su_miasto"]=array(1,4,80);
	$osoby_fields_validate["su_nr_domu"]=array(1,1,10);
	$osoby_fields_validate["su_nr_mieszkania"]=array(1,0,10);
	$osoby_fields_validate["su_nip"]=array(1,10,13);
	$osoby_fields_validate["su_data_urodzenia"]=array(1,10,10);
	$osoby_fields_validate["su_miejsce_urodzenia"]==array(1,4,80);
?>
