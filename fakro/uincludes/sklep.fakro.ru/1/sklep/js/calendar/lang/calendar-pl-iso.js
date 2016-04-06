// ** I18N
// Calendar PL language
// Author: Artur Filipiak, <imagen@poczta.fm>
// January, 2004
// Encoding: ISO
Calendar._DN = new Array
("Niedziela", "PoniedziaГek", "Wtorek", "Іroda", "Czwartek", "PiБtek", "Sobota", "Niedziela");

Calendar._SDN = new Array
("N", "Pn", "Wt", "Іr", "Cz", "Pt", "So", "N");

Calendar._MN = new Array
("Styczeё", "Luty", "Marzec", "Kwiecieё", "Maj", "Czerwiec", "Lipiec", "Sierpieё", "Wrzesieё", "PaМdziernik", "Listopad", "Grudzieё");

Calendar._SMN = new Array
("Sty", "Lut", "Mar", "Kwi", "Maj", "Cze", "Lip", "Sie", "Wrz", "PaМ", "Lis", "Gru");


// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "O kalendarzu";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2003\n" + // don't translate this this ;-)
"For latest version visit: http://dynarch.com/mishoo/calendar.epl\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Wybѓr daty:\n" +
"- aby wybraц rok uПyj przyciskѓw \xab, \xbb\n" +
"- aby wybraц miesiБc uПyj przyciskѓw " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + "\n" +
"- aby przyspieszyц wybѓr przytrzymaj wciЖniъty przycisk myszy nad ww. przyciskami.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Wybѓr czasu:\n" +
"- aby zwiъkszyц wartoЖц kliknij na dowolnym elemencie selekcji czasu\n" +
"- aby zmniejszyц wartoЖц uПyj dodatkowo klawisza Shift\n" +
"- moПesz rѓwnieП poruszaц myszkъ w lewo i prawo wraz z wciЖniъtym lewym klawiszem.";

Calendar._TT["PREV_YEAR"] = "Poprz. rok (trzymaj dla menu)";
Calendar._TT["PREV_MONTH"] = "Poprz. miesiБc (trzymaj dla menu)";
Calendar._TT["GO_TODAY"] = "PokaП dziЖ";
Calendar._TT["NEXT_MONTH"] = "Nast. miesiБc (trzymaj dla menu)";
Calendar._TT["NEXT_YEAR"] = "Nast. rok (trzymaj dla menu)";
Calendar._TT["SEL_DATE"] = "Wybierz datъ";
Calendar._TT["DRAG_TO_MOVE"] = "Przesuё okienko";
Calendar._TT["PART_TODAY"] = " (dziЖ)";
Calendar._TT["MON_FIRST"] = "PokaП PoniedziaГek jako pierwszy";
Calendar._TT["SUN_FIRST"] = "PokaП Niedzielъ jako pierwszБ";
Calendar._TT["CLOSE"] = "Zamknij";
Calendar._TT["TODAY"] = "DziЖ";
Calendar._TT["TIME_PART"] = "(Shift-)klik | drag, aby zmieniц wartoЖц";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Display %s first";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d-%m-%Y";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "wk";
Calendar._TT["TIME"] = "Time:";

