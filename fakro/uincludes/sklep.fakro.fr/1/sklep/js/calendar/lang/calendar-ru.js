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
("ÒÞáÚàÕáÕÝìÕ",
 "ßÞÝÕÔÕÛìÝØÚ",
 "ÒâÞàÝØÚ",
 "áàÕÔÐ",
 "çÕâÒÕàÓ",
 "ßïâÝØæÐ",
 "áãÑÑÞâÐ",
 "ÒÞáÚàÕáÕÝìÕ");

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
("ÒáÚ",
 "ßÞÝ",
 "Òâà",
 "áàÔ",
 "çÕâ",
 "ßïâ",
 "áãÑ",
 "ÒáÚ");

// full month names
Calendar._MN = new Array
("ïÝÒÐàì",
 "äÕÒàÐÛì",
 "ÜÐàâ",
 "ÐßàÕÛì",
 "ÜÐÙ",
 "ØîÝì",
 "ØîÛì",
 "ÐÒÓãáâ",
 "áÕÝâïÑàì",
 "ÞÚâïÑàì",
 "ÝÞïÑàì",
 "ÔÕÚÐÑàì");

// short month names
Calendar._SMN = new Array
("ïÝÒ",
 "äÕÒ",
 "ÜÐà",
 "Ðßà",
 "ÜÐÙ",
 "ØîÝ",
 "ØîÛ",
 "ÐÒÓ",
 "áÕÝ",
 "ÞÚâ",
 "ÝÞï",
 "ÔÕÚ");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "¾ ÚÐÛÕÝÔÐàÕ...";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"ºÐÚ ÒëÑàÐâì ÔÐâã:\n" +
"- ¿àØ ßÞÜÞéØ ÚÝÞßÞÚ \xab, \xbb ÜÞÖÝÞ ÒëÑàÐâì ÓÞÔ\n" +
"- ¿àØ ßÞÜÞéØ ÚÝÞßÞÚ " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " ÜÞÖÝÞ ÒëÑàÐâì ÜÕáïæ\n" +
"- ¿ÞÔÕàÖØâÕ íâØ ÚÝÞßÚØ ÝÐÖÐâëÜØ, çâÞÑë ßÞïÒØÛÞáì ÜÕÝî ÑëáâàÞÓÞ ÒëÑÞàÐ.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"ºÐÚ ÒëÑàÐâì ÒàÕÜï:\n" +
"- ¿àØ ÚÛØÚÕ ÝÐ çÐáÐå ØÛØ ÜØÝãâÐå ÞÝØ ãÒÕÛØçØÒÐîâáï\n" +
"- ßàØ ÚÛØÚÕ á ÝÐÖÐâÞÙ ÚÛÐÒØèÕÙ Shift ÞÝØ ãÜÕÝìèÐîâáï\n" +
"- ÕáÛØ ÝÐÖÐâì Ø ÔÒØÓÐâì ÜëèÚÞÙ ÒÛÕÒÞ/ÒßàÐÒÞ, ÞÝØ ÑãÔãâ ÜÕÝïâìáï ÑëáâàÕÕ.";

Calendar._TT["PREV_YEAR"] = "½Ð ÓÞÔ ÝÐ×ÐÔ (ãÔÕàÖØÒÐâì ÔÛï ÜÕÝî)";
Calendar._TT["PREV_MONTH"] = "½Ð ÜÕáïæ ÝÐ×ÐÔ (ãÔÕàÖØÒÐâì ÔÛï ÜÕÝî)";
Calendar._TT["GO_TODAY"] = "ÁÕÓÞÔÝï";
Calendar._TT["NEXT_MONTH"] = "½Ð ÜÕáïæ ÒßÕàÕÔ (ãÔÕàÖØÒÐâì ÔÛï ÜÕÝî)";
Calendar._TT["NEXT_YEAR"] = "½Ð ÓÞÔ ÒßÕàÕÔ (ãÔÕàÖØÒÐâì ÔÛï ÜÕÝî)";
Calendar._TT["SEL_DATE"] = "²ëÑÕàØâÕ ÔÐâã";
Calendar._TT["DRAG_TO_MOVE"] = "¿ÕàÕâÐáÚØÒÐÙâÕ ÜëèÚÞÙ";
Calendar._TT["PART_TODAY"] = " (áÕÓÞÔÝï)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "¿ÕàÒëÙ ÔÕÝì ÝÕÔÕÛØ ÑãÔÕâ %s";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "·ÐÚàëâì";
Calendar._TT["TODAY"] = "ÁÕÓÞÔÝï";
Calendar._TT["TIME_PART"] = "(Shift-)ÚÛØÚ ØÛØ ÝÐÖÐâì Ø ÔÒØÓÐâì";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%e %b, %a";

Calendar._TT["WK"] = "ÝÕÔ";
Calendar._TT["TIME"] = "²àÕÜï:";
