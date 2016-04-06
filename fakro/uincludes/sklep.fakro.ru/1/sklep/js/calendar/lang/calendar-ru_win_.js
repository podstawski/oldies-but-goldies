// ** I18N

// Calendar RU language
// Translation: Sly Golovanov, http://golovanov.net, <sly@golovanov.net>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("тюёъ№хёхэќх",
 "яюэхфхыќэшъ",
 "тђю№эшъ",
 "ё№хфр",
 "їхђтх№у",
 "яџђэшір",
 "ёѓссюђр",
 "тюёъ№хёхэќх");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("тёъ",
 "яюэ",
 "тђ№",
 "ё№ф",
 "їхђ",
 "яџђ",
 "ёѓс",
 "тёъ");

// full month names
Calendar._MN = new Array
("џэтр№ќ",
 "єхт№рыќ",
 "ьр№ђ",
 "ря№хыќ",
 "ьрщ",
 "шўэќ",
 "шўыќ",
 "ртуѓёђ",
 "ёхэђџс№ќ",
 "юъђџс№ќ",
 "эюџс№ќ",
 "фхърс№ќ");

// short month names
Calendar._SMN = new Array
("џэт",
 "єхт",
 "ьр№",
 "ря№",
 "ьрщ",
 "шўэ",
 "шўы",
 "рту",
 "ёхэ",
 "юъђ",
 "эюџ",
 "фхъ");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "Ю ърыхэфр№х...";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Ъръ тћс№рђќ фрђѓ:\n" +
"- Я№ш яюьюљш ъэюяюъ \xab, \xbb ьюцэю тћс№рђќ уюф\n" +
"- Я№ш яюьюљш ъэюяюъ " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " ьюцэю тћс№рђќ ьхёџі\n" +
"- Яюфх№цшђх §ђш ъэюяъш эрцрђћьш, їђюсћ яюџтшыюёќ ьхэў сћёђ№юую тћсю№р.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Ъръ тћс№рђќ т№хьџ:\n" +
"- Я№ш ъышъх эр їрёрѕ шыш ьшэѓђрѕ юэш ѓтхышїштрўђёџ\n" +
"- я№ш ъышъх ё эрцрђющ ъыртшјхщ Shift юэш ѓьхэќјрўђёџ\n" +
"- хёыш эрцрђќ ш фтшурђќ ьћјъющ тыхтю/тя№ртю, юэш сѓфѓђ ьхэџђќёџ сћёђ№хх.";

Calendar._TT["PREV_YEAR"] = "Эр уюф эрчрф (ѓфх№цштрђќ фыџ ьхэў)";
Calendar._TT["PREV_MONTH"] = "Эр ьхёџі эрчрф (ѓфх№цштрђќ фыџ ьхэў)";
Calendar._TT["GO_TODAY"] = "бхуюфэџ";
Calendar._TT["NEXT_MONTH"] = "Эр ьхёџі тях№хф (ѓфх№цштрђќ фыџ ьхэў)";
Calendar._TT["NEXT_YEAR"] = "Эр уюф тях№хф (ѓфх№цштрђќ фыџ ьхэў)";
Calendar._TT["SEL_DATE"] = "Тћсх№шђх фрђѓ";
Calendar._TT["DRAG_TO_MOVE"] = "Ях№хђрёъштрщђх ьћјъющ";
Calendar._TT["PART_TODAY"] = " (ёхуюфэџ)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Ях№тћщ фхэќ эхфхыш сѓфхђ %s";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Чръ№ћђќ";
Calendar._TT["TODAY"] = "бхуюфэџ";
Calendar._TT["TIME_PART"] = "(Shift-)ъышъ шыш эрцрђќ ш фтшурђќ";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%e %b, %a";

Calendar._TT["WK"] = "эхф";
Calendar._TT["TIME"] = "Т№хьџ:";
