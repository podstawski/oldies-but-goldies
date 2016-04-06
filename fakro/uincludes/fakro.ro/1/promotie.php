
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript">

  jQuery(function(){
    jQuery(".img_ars").attr('src', jQuery("#img_1_ars").attr('src')  );

    jQuery(".img_amz").attr('src', jQuery("#img_1_amz").attr('src')  );
    jQuery(".img_arf").attr('src', jQuery("#img_1_arf").attr('src')  );
    jQuery(".img_wgt").attr('src', jQuery("#img_1_wgt").attr('src')  );
    jQuery(".img_lws").attr('src', jQuery("#img_1_lws").attr('src')  );
    jQuery(".img_lwm").attr('src', jQuery("#img_1_lwm").attr('src')  );
  })

    var cadou = 0;
    var ales = 0;
    var total = 0;
    var err = 0;

    var lista = {'c3':3, 'c4':4, 'c5':5, 'c6':6, 'c7':7, 'c8':7, 'c9':8, 'c10':8};

    function eNumar(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
      return true;
    }

    function checkValue(obj) {
      if(obj.value == "") { obj.value = 0; };
    }

    function checkPoints() {
      err = 0;
      total = Number(document.getElementById('c3').value)*lista['c3'] + Number(document.getElementById('c4').value)*lista['c4'] + Number(document.getElementById('c5').value)*lista['c5'] + Number(document.getElementById('c6').value)*lista['c6'] + Number(document.getElementById('c7').value)*lista['c7'] + Number(document.getElementById('c8').value)*lista['c8'] + Number(document.getElementById('c9').value)*lista['c9'] + Number(document.getElementById('c10').value)*lista['c10'];
      if((cadou - total) < 0) {
        err++;
      };
      return err;
    }

    function showAdresa(cat) {
      if(cat == 0) {
        $('#fro_adr_dom').css('display', 'none');
      } else {
        $('#fro_adr_dom').css('display', 'block');
      };
    }

    function resetPromo() {
      $("#fro_nume, #fro_cnp, #fro_ci").val('');
      document.fro_promo.fro_adresa[0].checked = true;
      document.fro_promo.fro_sondaj[0].checked = true;
      $("#fro_judet,#fro_oras,#fro_strada,#fro_num,#fro_tel,#fro_mail,#fro_nr,#fro_data,#fro_emitent").val('');
      $("#fro_cadou,#c3,#c4,#c5,#c6,#c7,#c8,#c9,$c10").val(0);
      $("#fro_dispo").html('0');
      cadou = 0;
      total = 0;
    }

    function trimiteCerere() {

      checkPoints();

      for(var i = 0; i < document.fro_promo.fro_sondaj.length; i++) { if(document.fro_promo.fro_sondaj[i].checked) { var sondaj = document.fro_promo.fro_sondaj[i].value;};};
      for(var i = 0; i < document.fro_promo.fro_adresa.length; i++) { if(document.fro_promo.fro_adresa[i].checked) { var adresa = document.fro_promo.fro_adresa[i].value;};};

      if(cadou == 0 ) {
        alert("Nu ati completat numarul de ferestre achizitionate!");
      } else {
        if(err > 0 ) {
          alert("Nu aveti puncte suficiente pentru cadoul ales!");
        } else {
          if(total == 0 ) {
            alert("Nu ati ales nici un cadou in contul punctelor disponibile!");
          } else {

            var promoform = "nume=" + $("#fro_nume").val() +
            "&cnp=" + $("#fro_cnp").val() +
            "&ci=" + $("#fro_ci").val() +
            "&adresa=" + adresa +
            "&judet=" + $("#fro_judet").val() +
            "&oras=" + $("#fro_oras").val() +
            "&strada=" + $("#fro_strada").val() +
            "&numar=" + $("#fro_num").val() +
            "&tel=" + $("#fro_tel").val() +
            "&mail=" + $("#fro_mail").val() +
            "&nr=" + $("#fro_nr").val() +
            "&data=" + $("#fro_data").val() +
            "&emitent=" + $("#fro_emitent").val() +
            "&ferestre=" + $("#fro_cadou").val() +
            "&sondaj=" + sondaj +
            "&ars=" + $("#c3").val() +
            "&amz=" + $("#c4").val() +
            "&arf=" + $("#c5").val() +
            "&wgt=" + $("#c6").val() +
            "&lws60=" + $("#c7").val() +
            "&lws70=" + $("#c8").val() +
            "&lwm60=" + $("#c9").val() +
            "&lwm70=" + $("#c10").val();

            $.ajax({
              'type': "POST",
              'url': window.location.href,
              'data': promoform,
              'dataType':'text',
              'success': function(response) {
                alert("Talonul dumneavoastra a fost trimis. Va multumim!");
                resetPromo();
              }
            });// end AJAX

          };
        };
      };

    }// end function trimiteCerere()

  jQuery(function(){
    jQuery("#fro_trimite").click( function(){
      trimiteCerere();
    });
  })

</script>

<style type="text/css">
  <!--

  #fro_promo {padding: 0 0px 0px;text-align: left;width: 485px;margin: 0 auto;}
  #fro_promo input { width: 146px; _width: 145px; }
  #fro_promo label {display: block;margin-bottom: 5px;}
  #fro_promo div {margin-bottom: 5px;}
  #fro_promo span {float: left;width: 150px;line-height: 21px;}
  .fro_cadou {clear: both;margin-bottom: 15px !important;height: 40px;}
  .fro_cadou img {float: left;margin: 3px 10px 0 0;width: 50px;}
  .fro_cadou span {width: 250px !important;}
  .fro_cadou input {width: 50px !important;float: left;margin-top: 5px;}
  #fro_trimite {-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;background-color: #006400;color: #FFFF00;display: block;font-size: 15px;line-height: 40px;margin: 0 auto 20px;text-align: center;text-decoration: none;width: 220px;}
  #fro_nume, #fro_adresa, #fro_emitent, #fro_mail { width: 305px !important; }
  .fro_sondaj {width: 20px !important;margin-left: 140px;}
  #fro_strada { width: 230px !important; }
  #fro_num { width: 50px !important; }
  #fro_adr_dom {display: none;}

  -->
</style>

<h1>Talon de participare la "Axa Castigurilor"</h1>
<form name="fro_promo" id="fro_promo" method="post" action="">

  <h2>Date personale</h2>
  <label><span>Nume si Prenume</span><input type="text" name="fro_nume" id="fro_nume" /></label>
  <label><span>CNP</span><input type="text" name="fro_cnp" id="fro_cnp" /></label>
  <label><span>CI Serie / Numar</span><input type="text" name="fro_ci" id="fro_ci" /></label>
  <label><span>Telefon</span><input type="text" name="fro_tel" id="fro_tel" /></label>
  <label><span>E-mail</span><input type="text" name="fro_mail" id="fro_mail" /></label>
  <label><span>Adresa de livrare</span><input name="fro_adresa" type="radio" value="distribuitor" checked="checked" style="width: 15px;" onclick="showAdresa(0)" />adresa distribuitorului (nu se completeaza)</label>
  <label><span>&nbsp;</span><input name="fro_adresa" type="radio" value="domiciliu" style="width: 15px; float: left;" onclick="showAdresa(1)" />domiciliu - optiune valabila numai in cazul in care domiciliul este in oras resedinta de judet</label>
  <div id="fro_adr_dom">
    <div><span>Strada</span><input type="text" name="fro_strada" id="fro_strada" /> Nr. <input type="text" name="fro_num" id="fro_num" /></div>
    <label><span>Oras</span><input type="text" name="fro_oras" id="fro_oras" /></label>
    <label><span>Judet</span><input type="text" name="fro_judet" id="fro_judet" /></label>
  </div>
  <br />
  <br />
  <h2>Informatii factura</h2>
  <div><span>Nr. / Data</span><input type="text" name="fro_nr" id="fro_nr" /> / <input type="text" name="fro_data" id="fro_data" /></div>
  <label><span>Distribuitor</span><input type="text" name="fro_emitent" id="fro_emitent" /></label>
  <label><span>Nr. ferestre achizitionate</span><input type="text" name="fro_cadou" id="fro_cadou" value="0" onkeypress="return eNumar(event)" onchange="cadou = (this.value != '') ? this.value : 0; checkPoints(); document.getElementById('fro_dispo').innerHTML = cadou;" /> (<strong id="fro_dispo">0</strong> puncte cadou disponibile)</label>
  <br />
  Dori&#355;i s&#259; participa&#355;i &icirc;n urm&#259;torul an la un sondaj privind gradul de mul&#355;umire fa&#355;&#259; de produsele Fakro?
  <br /><br />
  <input name="fro_sondaj" type="radio" value="da" checked="checked" class="fro_sondaj" /> Da   <input name="fro_sondaj" type="radio" value="nu" class="fro_sondaj" /> Nu
  <br />
  <br />
  <h2>Cadouri</h2>
  <p style="width: 460px; padding: 10px; border: 1px solid #e0e0e0; text-align: justify;"><!--&Icirc;n func&#355;ie de num&#259;rul de ferestre de mansard&#259; achizi&#355;ionate, alege&#355;i cadourile dorite, not&acirc;nd &icirc;n dreptul acestora numarul de buca&#355;i pentru care opta&#355;i.<br />
    <br />-->
    &Icirc;n func&#355;ie de num&#259;rul de ferestre de mansard&#259; achizi&#355;ionate, alege&#355;i cadourile dorite, not&acirc;nd &icirc;n dreptul acestora num&#259;rul de buc&#259;&#355;i pentru care opta&#355;i.<br />
    <br />
    Pute&#355;i opta pentru oricare dintre cadourile ce sunt oferite pentru o cantitate mai mic&#259; de ferestre. Asigura&#355;i-v&#259; c&#259; num&#259;rul de ferestre achizi&#355;ionate (conform facturii) v&#259; permite alegerea cadourilor selectate (exemplu: nu pute&#355;i selecta un rulou ARF dac&#259 a&#355;i cump&#259;rat doar 4 ferestre de mansard&#259;).<br />
    <br />
    <strong>Not&#259;:</strong> Respectarea coresponden&#355;ei expuse in Regulament v&#259; las&#259; dreptul alegerii oric&#259;rei combina&#355;ii &icirc;ntre acestea.  </p>
  <br />
  <label class="fro_cadou"><img class="img_ars" src="images/ars.jpg" alt="rulou ARS" border="0" /><span><strong>rulou ARS I cod culoare 002 (crem)</strong><br />(min. 3 ferestre + rame de etan&#351;are)</span><input type="text" name="ars" id="c3" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_amz" src="images/amz.jpg" alt="rulou AMZ" border="0" /><span><strong>rulou AMZ I cod culoare 089 (gri)</strong><br />(min. 4 ferestre + rame de etan&#351;are)</span><input type="text" name="c4" id="c4" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_arf" src="images/arf.jpg" alt="rulou ARF" border="0" /><span><strong>rulou ARF I cod culoare 052 (crem)</strong><br />(min. 5 ferestre + rame de etan&#351;are)</span><input type="text" name="c5" id="c5" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_wgt" src="images/wgt.jpg" alt="rulou WGT" border="0" /><span><strong>luminator WGT</strong><br />(min. 6 ferestre + rame de etan&#351;are)</span><input type="text" name="c6" id="c6" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_lws" src="images/lws.jpg" alt="rulou LWS" border="0" /><span><strong>scara modular&#259; din lemn LWS 60x120</strong><br />(min. 7 ferestre + rame de etan&#351;are)</span><input type="text" name="c7" id="c7" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_lws" src="images/lws.jpg" alt="rulou LWS" border="0" /><span><strong>scara modular&#259; din lemn LWS 70x120</strong><br />(min. 7 ferestre + rame de etan&#351;are)</span><input type="text" name="c8" id="c8" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_lwm" src="images/lwm.jpg" alt="rulou LWM" border="0" /><span><strong>scara modular&#259; metalic&#259; LWM 60x120</strong><br />(min. 8 ferestre + rame de etan&#351;are)</span><input type="text" name="c9" id="c9" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <label class="fro_cadou"><img class="img_lwm" src="images/lwm.jpg" alt="rulou LWM" border="0" /><span><strong>scara modular&#259; metalic&#259; LWM 70x120</strong><br />(min. 8 ferestre + rame de etan&#351;are)</span><input type="text" name="c10" id="c10" onkeypress="return eNumar(event)" onchange="checkValue(this)" value="0" /></label>
  <p style="width: 460px; padding: 10px; border: 1px solid #e0e0e0; color: #006400; margin-bottom: 0;">Prin trimiterea acestui formular admiteti cunoasterea si acceptarea conditiilor descrise in Regulamentul acestei promotii!</p>
  <br />
  <br />
  <a href="javascript:;" id="fro_trimite" >Trimite</a>
</form>

<?php

  if($_POST) {

    include("Mail.php");

    $email    = "promotie@kronlux.ro";
    $from     = "From: Promotie FAKRO";
    $subject  = "Talon AXA CASTIGURILOR";
    $m  = &Mail::Factory("smtp",$params);

    $body = "Nume: "        . $_POST['nume'] .
    "\nCNP: "               . $_POST['cnp'] .
    "\nCI: "                . $_POST['ci'] .
    "\nTelefon: "           . $_POST['tel'] .
    "\nE-mail: "            . $_POST['mail'] .
    "\n\nAdresa livrare: "  . $_POST['adresa'] .
    "\nStrada: "            . $_POST['strada'] . "; Numarul: " . $_POST['numar'] .
    "\nOras: "              . $_POST['oras'] .
    "\nJudet: "             . $_POST['judet'] .
    "\n\nNr. factura: "     . $_POST['nr'] .
    "\nDin data: "          . $_POST['data'] .
    "\nEmitent: "           . $_POST['emitent'] .
    "\nFerestre achizitionate: " . $_POST['ferestre'] .
    "\n\nSondaj: "          . $_POST['sondaj'] .
    "\n\nARS: "             . $_POST['ars'] .
    "\n\nAMZ: "             . $_POST['amz'] .
    "\n\nARF: "             . $_POST['arf'] .
    "\n\nWGT: "             . $_POST['wgt'] .
    "\n\nLWS60: "           . $_POST['lws60'] .
    "\n\nLWS70: "           . $_POST['lws70'] .
    "\n\nLWM60: "           . $_POST['lwm60'] .
    "\n\nLWM70: "           . $_POST['lwm70'];

    $header['From']         = "promotie@kronlux.ro";
    $header['To']           = 'promotie@kronlux.ro';
    $header['Subject']      = 'Talon AXA CASTIGURILOR';
    $header['Content-Type'] = "text/plain;\n\tharset=ISO-8859-1;";

    $error = $m->send($header['To'], $header, $body);
  };
?>