<?
	if (!$CLASS_RIGHTS) return;


	$TEXTSTYLE_CSS="<link href='$IMAGES/textstyle.css' rel='stylesheet' type='text/css'>";
	if ( file_exists("$UIMAGES/$DEFAULT_TEXTFILE_CSS") )
	$TEXTSTYLE_CSS.="\n	<link href='$UIMAGES/$DEFAULT_TEXTFILE_CSS' rel='stylesheet' type='text/css'>";
	
	
?>
<? if (!$editmode) echo "<script>location.href='/';</script>"; ?>
<html>
<head>
    <title>KAMELEON: <?echo label("Class");?></title>
	  <?echo $TEXTSTYLE_CSS ?>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
    <?
      include_js("jquery-1.4");
      include_js("jquery-ui.min");
    ?>
</head>
<body>

<?
	include("include/helpbegin.h");
?>



<?
   include("include/navigation.h");
?>


<div class="km_toolbar">
<ul>
  <li>
    <a class="km_icon km_iconi_new" href="javascript:nowy_styl();" title="<?=label("New class")?>"><?=label("New class")?></a>
  </li>
  <li>
    <a class="km_icon km_iconi_save" href="<?echo $SCRIPT_NAME?>?action=ZapiszStyle" title="<?=label("Save classes")?>"><?=label("Save classes")?></a>
  </li>
  <li>
    <a class="km_icon km_iconi_loadcss" href="javascript:szablon_styl()" title="<?echo label("Set defaults from template")?>"><?echo label("Set defaults from template")?></a>
  </li>
  <li class="km_sep"></li>
    
<?
	$query="SELECT nazwa FROM class WHERE server=$SERVER_ID
		 	AND ver=$ver GROUP BY nazwa";
			
	//echo $query;			
	$style=ado_ObjectArray($adodb,$query);

	
    
    //SELECT ZE STYLAMI    
	if (is_array($style))
	{
        	echo "<li class=\"km_label\">
          <form name='form_exploreclass' action='$SCRIPT_NAME'>
          <label>".label('Select style')."</label>
                <select name=\"exploreclass\" class=\"km_select\" onchange=\"form_exploreclass.submit()\">
                    <option value=''>".label('Select style')."</option>\n";
			for ($i=0;is_array($style) && $i<count($style) ;$i++)
	    	{
            	$nazwa=$style[$i]->nazwa;
				if ($nazwa[0]==".") $display_nazwa=substr($nazwa,1);
				else $display_nazwa="&lt;".$nazwa."&gt;";
            
				if ($exploreclass==$nazwa)  
				{
					$class_select = "selected";
					$display_explore_nazwa=$display_nazwa;
				}
				else $class_select="";
            
				echo "<option value=$nazwa $class_select>$display_nazwa</option>\n";
        	}
			echo "</select></form></li>";

			$copyalt=label("Copy class");
			if (strlen($exploreclass))
			echo "
        <li>    
          <a class=\"km_icon km_iconi_delete\" href=\"javascript:usun_klase('$exploreclass','$display_explore_nazwa');\" title=\"".$alt_delete."\">".$alt_delete."</a>
        </li>
        <li>
          <a class=\"km_icon km_iconi_property\" href=\"$SCRIPT_NAME?exploreclass=$exploreclass\" title=\"".label("Class properity")."\">".label("Class properity")."</a>
				</li>
				<li>
				 	<a class=\"km_icon km_iconi_copy\" href=\"javascript:skopiuj_klase('$exploreclass','$display_explore_nazwa')\" title=\"".$copyalt."\">".$copyalt."</a>
        </li>
			";
    echo "
    <li class=\"km_sep\"></li>
    <li><a href=\"#\" id=\"km_lang_open\" class=\"km_icon km_iconi_lang_".$lang."\" title=\"".label($lang)."\">".label($lang)."</a></li>
    </ul></div>";
	}    
  else
	{
	  echo "</ul></div>";
		$query="SELECT DISTINCT(ver) FROM class WHERE server=".$SERVER_ID." AND ver<".$ver." ORDER BY ver";
  	$wersje=ado_ObjectArray($adodb,$query);
    echo "<ul class=\"copy_version\">";
  	if (is_array($wersje)) echo label("Copy from previous versions").": ";
		for ($i=0;is_array($wersje) && $i<count($wersje);$i++)
		{
			$src=$wersje[$i]->ver;
  		echo "<a class=\"km_icon km_iconi_copy\" href=\"$SCRIPT_NAME?action=SkopiujStyl&srcver=$src\" title=\"$src\">".$src."</a>";
  	}
    echo "
    <li class=\"km_sep\"></li>
    <li><a href=\"#\" id=\"km_lang_open\" class=\"km_icon km_iconi_lang_".$lang."\" title=\"".label($lang)."\">".label($lang)."</a></li>
    </ul>";
  }
  //SELECT ZE STYLAMI END
  
  include ("include/lang-change.h");
?>
<div id="advanced">
<?
    //FORMA ZE STYLEM
    if (strlen($exploreclass))
    {
        if ($exploreclass[0]==".") $classname=substr($exploreclass,1);
    		else $classname=$exploreclass;
        
        echo "
        <h2>".label("Class name").": $classname</h2>
        <div class=\"sample\">".label("Przykładowy tekst")."</div>
        <div class=\"litem_2\">
          <label>".label("Field")."</label>
          <div class=\"inputer\">".label("Value")."</div>
        </div>\n";
        
        $query="SELECT pole,wart FROM class WHERE server=$SERVER_ID AND ver=$ver AND nazwa='$exploreclass' ORDER BY pole";

		//echo $query;
        $pola=ado_ObjectArray($adodb,$query);
        for ($p=0;is_array($pola) && $p<count($pola);$p++)
        {

            $pole=$pola[$p]->pole;
            $wart=$pola[$p]->wart;
			      $UZYTE_POLA[$pole]=1;
			
            echo "<div class=\"litem_".(($p % 2)+1)."\"><label>".$pole."</label>\n";
            $query="SELECT wart AS mozliwe_wart FROM classp WHERE pole='$pole'";
            parse_str(ado_query2url($query));
            
            echo "<div class=\"inputer\">";
            echo "<input type=hidden  name='pole[$p]' value='$pole'>";
            if (!strlen($mozliwe_wart)) 
            	echo "<input class=k_input id=wart_$p type=text size=45 name='wart[$p]' value='$wart'>";
            else
            {
            	$mozliwosci=explode(";",$mozliwe_wart);
            	for($m=0;$m<count($mozliwosci);$m++) $mozliwosci[$m]=array($mozliwosci[$m],$mozliwosci[$m]);
            	echo CreateFormField(array("",1,"select","wart[$p]",$wart,$mozliwosci));
            }
            if (strstr($pole,"color"))
              echo "<a href=\"javascript:otworzPalete('wart_$p',document.all['wart_$p'].value)\"><img border=0 src=img/i_colors_n.gif onmouseover=\"this.src='img/i_colors_a.gif'\" onmouseout=\"this.src='img/i_colors_n.gif'\" align=middle></a>";
            
            echo "($wart)";
            echo "<a href=\"javascript:usun_klase('$exploreclass::$pole','$display_explore_nazwa -> $pole')\"><img class=k_imgbutton border=0 src='img/i_delete_n.gif'  onmouseover=\"this.src='img/i_delete_a.gif'\" onmouseout=\"this.src='img/i_delete_n.gif'\"  alt='$alt_delete'></a>";
            echo "</div></div>\n";
        }
        
        $query="SELECT pole FROM classp ORDER BY pole";
        
		
		    $pola="";
        $pola=ado_ObjectArray($adodb,$query);
        $_pola[]=array("",label("Choose"));
        for ($p=0;is_array($pola) && $p<count($pola);$p++)
    		{
    			$pole=$pola[$p]->pole;
    			if ($UZYTE_POLA[$pole]) continue;
          $_pola[]=array($pola[$p]->pole,$pola[$p]->pole);
    		}
        
        if (is_array($pola)) echo CreateFormField(array(label("New").": ",1,"select","nowe_pole","",$_pola));
        echo "<input class=k_imgbutton type=image border=0 src='img/i_new_n.gif'  onmouseover=\"this.src='img/i_new_a.gif'\" onmouseout=\"this.src='img/i_new_n.gif'\"  alt='".label('Add field')."'>";
        echo "<input class=k_imgbutton type=image border=0 src='img/i_save_n.gif'  onmouseover=\"this.src='img/i_save_a.gif'\" onmouseout=\"this.src='img/i_save_n.gif'\"  alt='".label('Save')."'>";
        echo "</form>";
    }
    //FORMA ZE STYLEM END
?>
  
</div>
<form name=_styl method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=action value="DodajStyl">
 <input type=hidden name=nazwa value="">
 <input type=hidden name=src value=""> 
</form>

<script>
function nowy_styl()
{
	k=prompt("<?echo label("Class name")?>:","");
	if ( k!=null ) 
	{
		document._styl.nazwa.value=k;
		document._styl.submit();
	}
}

function szablon_styl()
{
	if (!confirm("<?echo label("Are you sure to overwrite all classes with template")?> ?")) return;

	document._styl.action.value="KopiujStyleZSzablonu";
	document._styl.submit();

}

function usun_klase(nazwa,display)
{
	if (!confirm("<?echo label("Delete")?>: "+display+" ")) return;

	document._styl.nazwa.value=nazwa;
	document._styl.action.value="UsunStyl";
	document._styl.submit();

}

function skopiuj_klase(nazwa,display)
{
	k=prompt("<?echo label("Destination class name")?>:",display);

	if ( k!=null ) 
	{
		document._styl.src.value=nazwa;
		document._styl.nazwa.value=k;
		document._styl.action.value="SkopiujStylNowaNazwa";
		document._styl.submit();
	}
}


pole_koloru="";
function ustawKolor(par,kolor)
{
	document.all[pole_koloru].value=kolor;
}

function otworzPalete(nazwa,kolor)
{
	pole_koloru=nazwa;

	if (kolor.substring(0,1)=="#") kolor=kolor.substring(1,7);
	a=open("kolory.<?echo $KAMELEON_EXT?>?u_color="+kolor,"Kolory","toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,resizable=0,width=400,height=400");
}

	
</script>

<?
	include("include/helpend.h");
?>


</body>
</html>
