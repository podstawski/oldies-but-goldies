<?
	$osoby_fields="";
	// label, nazwa pola, grupa


	$osoby_fields[]=array(sysmsg('th_first_name','system'),"su_imiona","personal",50);
	$osoby_fields[]=array(sysmsg('th_sur_name','system'),"su_nazwisko","personal",80);
	$osoby_fields[]=array(sysmsg('th_sex','system'),"su_plec","personal",1,array(" "=>"","K"=>"Kobieta","M"=>"MъПczyzna"));

	$osoby_fields[]=array(sysmsg('th_login','system'),"su_login","login",64);
	$osoby_fields[]=array(sysmsg('th_password','system'),"su_pass","login",64);
	$osoby_fields[]=array(sysmsg('th_pesel','system'),"su_pesel","login",11);


	$osoby_fields[]=array(sysmsg('th_address','system'),"su_ulica","address",80);
	$osoby_fields[]=array(sysmsg('th_house_no','system'),"su_nr_domu","address",10);
	$osoby_fields[]=array(sysmsg('th_flat_no','system'),"su_nr_mieszkania","address",10);
	$osoby_fields[]=array(sysmsg('th_zip','system'),"su_kod_pocztowy","address",6);
	$osoby_fields[]=array(sysmsg('th_town','system'),"su_miasto","address",80);


	$osoby_fields[]=array(sysmsg('th_telephone','system'),"su_telefon","contact",30);
	$osoby_fields[]=array(sysmsg('th_handy','system'),"su_gsm","contact",30);
	$osoby_fields[]=array(sysmsg('th_email','system'),"su_email","contact",100);



		$osoby_fields[]=array(sysmsg('Tax Id','system'),"su_nip","tax",10);



	$osoby_fields_grupy["personal"]="Dane osobowe";
	$osoby_fields_grupy["login"]="Identyfikacja elektroniczna";
	$osoby_fields_grupy["address"]="Adres";
	$osoby_fields_grupy["contact"]="Dane kontaktowe";
	$osoby_fields_grupy["tax"]="Identyfikacja sformalizowana";
	$osoby_fields_grupy["work"]="Lokalizacja";

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
