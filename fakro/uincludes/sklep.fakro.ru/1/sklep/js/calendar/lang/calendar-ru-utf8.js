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
("аВаОбаКбаЕбаЕаНбаЕ",
 "аПаОаНаЕаДаЕаЛбаНаИаК",
 "аВбаОбаНаИаК",
 "ббаЕаДаА",
 "баЕбаВаЕбаГ",
 "аПббаНаИбаА",
 "ббаБаБаОбаА",
 "аВаОбаКбаЕбаЕаНбаЕ");

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
("аВбаК",
 "аПаОаН",
 "аВбб",
 "ббаД",
 "баЕб",
 "аПбб",
 "ббаБ",
 "аВбаК");

// full month names
Calendar._MN = new Array
("баНаВаАбб",
 "баЕаВбаАаЛб",
 "аМаАбб",
 "аАаПбаЕаЛб",
 "аМаАаЙ",
 "аИбаНб",
 "аИбаЛб",
 "аАаВаГббб",
 "баЕаНббаБбб",
 "аОаКббаБбб",
 "аНаОбаБбб",
 "аДаЕаКаАаБбб");

// short month names
Calendar._SMN = new Array
("баНаВ",
 "баЕаВ",
 "аМаАб",
 "аАаПб",
 "аМаАаЙ",
 "аИбаН",
 "аИбаЛ",
 "аАаВаГ",
 "баЕаН",
 "аОаКб",
 "аНаОб",
 "аДаЕаК");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "а аКаАаЛаЕаНаДаАбаЕ...";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"ааАаК аВбаБбаАбб аДаАбб:\n" +
"- абаИ аПаОаМаОбаИ аКаНаОаПаОаК \xab, \xbb аМаОаЖаНаО аВбаБбаАбб аГаОаД\n" +
"- абаИ аПаОаМаОбаИ аКаНаОаПаОаК " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " аМаОаЖаНаО аВбаБбаАбб аМаЕббб\n" +
"- ааОаДаЕбаЖаИбаЕ ббаИ аКаНаОаПаКаИ аНаАаЖаАббаМаИ, ббаОаБб аПаОбаВаИаЛаОбб аМаЕаНб аБббббаОаГаО аВбаБаОбаА.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"ааАаК аВбаБбаАбб аВбаЕаМб:\n" +
"- абаИ аКаЛаИаКаЕ аНаА баАбаАб аИаЛаИ аМаИаНббаАб аОаНаИ баВаЕаЛаИбаИаВаАбббб\n" +
"- аПбаИ аКаЛаИаКаЕ б аНаАаЖаАбаОаЙ аКаЛаАаВаИбаЕаЙ Shift аОаНаИ баМаЕаНббаАбббб\n" +
"- аЕбаЛаИ аНаАаЖаАбб аИ аДаВаИаГаАбб аМббаКаОаЙ аВаЛаЕаВаО/аВаПбаАаВаО, аОаНаИ аБбаДбб аМаЕаНббббб аБббббаЕаЕ.";

Calendar._TT["PREV_YEAR"] = "ааА аГаОаД аНаАаЗаАаД (баДаЕбаЖаИаВаАбб аДаЛб аМаЕаНб)";
Calendar._TT["PREV_MONTH"] = "ааА аМаЕббб аНаАаЗаАаД (баДаЕбаЖаИаВаАбб аДаЛб аМаЕаНб)";
Calendar._TT["GO_TODAY"] = "аЁаЕаГаОаДаНб";
Calendar._TT["NEXT_MONTH"] = "ааА аМаЕббб аВаПаЕбаЕаД (баДаЕбаЖаИаВаАбб аДаЛб аМаЕаНб)";
Calendar._TT["NEXT_YEAR"] = "ааА аГаОаД аВаПаЕбаЕаД (баДаЕбаЖаИаВаАбб аДаЛб аМаЕаНб)";
Calendar._TT["SEL_DATE"] = "абаБаЕбаИбаЕ аДаАбб";
Calendar._TT["DRAG_TO_MOVE"] = "ааЕбаЕбаАбаКаИаВаАаЙбаЕ аМббаКаОаЙ";
Calendar._TT["PART_TODAY"] = " (баЕаГаОаДаНб)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "ааЕбаВбаЙ аДаЕаНб аНаЕаДаЕаЛаИ аБбаДаЕб %s";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "ааАаКбббб";
Calendar._TT["TODAY"] = "аЁаЕаГаОаДаНб";
Calendar._TT["TIME_PART"] = "(Shift-)аКаЛаИаК аИаЛаИ аНаАаЖаАбб аИ аДаВаИаГаАбб";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%e %b, %a";

Calendar._TT["WK"] = "аНаЕаД";
Calendar._TT["TIME"] = "абаЕаМб:";
