<?php

// BIBLIOTEKA PL
// ver 0.9
// 2005.09.20
//
//
//Kodowanie polskich znaków:
//
//  ISO-8859-2      - polskie znaki iso
//  WINDOWS-1250    - polskie znaki win
//  ASCII           - brak jakichkolwiek polskich znaków
//  WIN-AND-ISO     - plik zepsuty: zawiera zarówno znaki WIN jak i ISO (specyficzne)
//  WIN-OR-ISO      - plik nie zawiera znaków specyficznych żadnego kodu, ale zawiera znaki wspólne
//  UTF-8           - kodowanie utf-8
//  UTF-16          - kodowanie utf-16


// Biblioteka mb, a w szczególnoci funkcja mb_detect_encoding() nie
// umożliwiajš wykrycia innego kodowania niż ISO-8859-2 lub UTF-8 (w stosunku do polskich znaków).
// Wywołanie:
//     echo mb_detect_encoding($org, 'ISO-8859-2, WINDOWS-1250, UTF-8');
// nie da pożšdanych efektów, gdyż kodowanie WINDOWS-1250 nie jest rozpoznawane.
//
// Ponadto funkcja iconv() w przypadku napotkania niedozwolonych znaków kończy przetwarzanie.
// Stšd potrzeba przygotowania własnej funkcji pl_detect().
//
//
// (c)2005 gajdaw
//  http://www.gajdaw.pl
//
//

function napisZawieraZnakiZNapisu($AString, $AChars)
{
    $l = strlen($AChars);
    for ($i = 0; $i < $l; $i++) {
        if (strstr($AString, $AChars[$i]) !== false) {
            return true;
        }
    }
    return false;
}

function str_replace_arrays($ArrayIn, $ArrayOUT, $AStr)
{
    $tmpcount = count($ArrayIn);
    if ($tmpcount != count($ArrayOUT)) {
        return false;
    };

    for ($i = 0; $i < $tmpcount; $i++) {
        $AStr = str_replace($ArrayIn[$i], $ArrayOUT[$i], $AStr);
    }

    return $AStr;
}


//define('PL_ISO_ALL', 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ');  //linijka zakodowana jako iso
define('PL_ISO_ALL', "\xb1\xe6\xea\xb3\xf1\xf3\xb6\xbc\xbf\xa1\xc6\xca\xa3\xd1\xd3\xa6\xac\xaf");
$ARRAY_PL_ISO_ALL = array(
    "\xb1", "\xe6", "\xea", "\xb3", "\xf1", "\xf3", "\xb6", "\xbc", "\xbf",
    "\xa1", "\xc6", "\xca", "\xa3", "\xd1", "\xd3", "\xa6", "\xac", "\xaf"
);


//define('PL_ISO_ALL_UPPER', 'ĄĆĘŁŃÓŚŹŻ');  //linijka zakodowana jako iso
define('PL_ISO_ALL_UPPER', "\xa1\xc6\xca\xa3\xd1\xd3\xa6\xac\xaf");
$ARRAY_PL_ISO_ALL_UPPER = array(
    "\xa1", "\xc6", "\xca", "\xa3", "\xd1", "\xd3", "\xa6", "\xac", "\xaf"
);


//define('PL_ISO_ALL_LOWER', 'ąćęłńóśźż');  //linijka zakodowana jako iso
define('PL_ISO_ALL_LOWER', "\xb1\xe6\xea\xb3\xf1\xf3\xb6\xbc\xbf");
$ARRAY_PL_ISO_ALL_LOWER = array(
    "\xb1", "\xe6", "\xea", "\xb3", "\xf1", "\xf3", "\xb6", "\xbc", "\xbf"
);


//define('PL_ISO_SPECIFIC', 'ąśźĄŚŹ');  //linijka zakodowana jako iso
define('PL_ISO_SPECIFIC', "\xb1\xb6\xbc\xa1\xa6\xac");
$ARRAY_PL_ISO_SPECIFIC = array(
    "\xb1", "\xb6", "\xbc",
    "\xa1", "\xa6", "\xac"
);


//define('PL_ISO_SPECIFIC_UPPER', 'ĄŚŹ');  //linijka zakodowana jako iso
define('PL_ISO_SPECIFIC_UPPER', "\xa1\xa6\xac");
$ARRAY_PL_ISO_SPECIFIC_UPPER = array(
    "\xa1", "\xa6", "\xac"
);


//define('PL_ISO_SPECIFIC_LOWER', 'ąśź');  //linijka zakodowana jako iso
define('PL_ISO_SPECIFIC_LOWER', "\xb1\xb6\xbc");
$ARRAY_PL_ISO_SPECIFIC_LOWER = array(
    "\xb1", "\xb6", "\xbc"
);


//define('PL_WIN_ALL', 'šćęłńóżĽĆĘŁŃÓŻ');  //linijka zakodowana jako win
define('PL_WIN_ALL', "\xb9\xe6\xea\xb3\xf1\xf3\x9c\x9f\xbf\xa5\xc6\xca\xa3\xd1\xd3\x8c\x8f\xaf");
$ARRAY_PL_WIN_ALL = array(
    "\xb9", "\xe6", "\xea", "\xb3", "\xf1", "\xf3", "\x9c", "\x9f", "\xbf",
    "\xa5", "\xc6", "\xca", "\xa3", "\xd1", "\xd3", "\x8c", "\x8f", "\xaf"
);


//define('PL_WIN_ALL_UPPER', 'ĽĆĘŁŃÓŻ');  //linijka zakodowana jako win
define('PL_WIN_ALL_UPPER', "\xa5\xc6\xca\xa3\xd1\xd3\x8c\x8f\xaf");
$ARRAY_PL_WIN_ALL_UPPER = array(
    "\xa5", "\xc6", "\xca", "\xa3", "\xd1", "\xd3", "\x8c", "\x8f", "\xaf"
);


//define('PL_WIN_ALL_LOWER', 'šćęłńóż');  //linijka zakodowana jako win
define('PL_WIN_ALL_LOWER', "\xb9\xe6\xea\xb3\xf1\xf3\x9c\x9f\xbf");
$ARRAY_PL_WIN_ALL_LOWER = array(
    "\xb9", "\xe6", "\xea", "\xb3", "\xf1", "\xf3", "\x9c", "\x9f", "\xbf"
);


//define('PL_WIN_SPECIFIC', 'šĽ');  //linijka zakodowana jako win
define('PL_WIN_SPECIFIC', "\xb9\x9c\x9f\xa5\x8c\x8f");
$ARRAY_PL_WIN_SPECIFIC = array(
    "\xb9", "\x9c", "\x9f",
    "\xa5", "\x8c", "\x8f"
);


//define('PL_WIN_SPECIFIC_UPPER', 'Ľ');  //linijka zakodowana jako win
define('PL_WIN_SPECIFIC_UPPER', "\xa5\x8c\x8f");
$PL_WIN_SPECIFIC_UPPER = array(
    "\xa5", "\x8c", "\x8f"
);


//define('PL_WIN_SPECIFIC_LOWER', 'š');  //linijka zakodowana jako win
define('PL_WIN_SPECIFIC_LOWER', "\xb9\x9c\x9f");
$ARRAY_PL_WIN_SPECIFIC_LOWER = array(
    "\xb9", "\x9c", "\x9f"
);


//define('PL_COMMON', 'ćęłńóżĆĘŁŃÓŻ');  //linijka zakodowana jako win lub iso
define('PL_COMMON', "\xe6\xea\xb3\xf1\xf3\xbf\xc6\xca\xa3\xd1\xd3\xaf");
$ARRAY_PL_COMMON = array(
    "\xe6", "\xea", "\xb3", "\xf1", "\xf3", "\xbf",
    "\xc6", "\xca", "\xa3", "\xd1", "\xd3", "\xaf"
);


//define('PL_COMMON_UPPER', 'ĆĘŁŃÓŻ');  //linijka zakodowana jako win lub iso
define('PL_COMMON_UPPER', "\xc6\xca\xa3\xd1\xd3\xaf");
$ARRAY_PL_COMMON_UPPER = array(
    "\xc6", "\xca", "\xa3",
    "\xd1", "\xd3", "\xaf"
);


//define('PL_COMMON_LOWER', 'ćęłńóż');  //linijka zakodowana jako win lub iso
define('PL_COMMON_LOWER', "\xe6\xea\xb3\xf1\xf3\xbf");
$ARRAY_PL_COMMON_LOWER = array(
    "\xe6", "\xea", "\xb3",
    "\xf1", "\xf3", "\xbf"
);


define('PL_ASCII_ALL',            'acelnoszzACELNOSZZ');
define('PL_ASCII_UPPER',          'ACELNOSZZ');
define('PL_ASCII_LOWER',          'acelnoszz');
define('PL_ASCII_SPECIFIC',       'aszASZ');
define('PL_ASCII_SPECIFIC_UPPER', 'ASZ');
define('PL_ASCII_SPECIFIC_LOWER', 'asz');
define('PL_ASCII_COMMON',         'celnozCELNOZ');
define('PL_ASCII_COMMON_UPPER',   'CELNOZ');
define('PL_ASCII_COMMON_LOWER',   'celnoz');



//*****ESCAPED********************************************************************************

//kodowanie backshlash-kod-szesnastkowy (php) kodu iso-8859-2
$ARRAY_PL_ISO_ESCAPE_BACKSHALH_HEX = array(
    '\xb1', '\xe6', '\xea', '\xb3', '\xf1', '\xf3', '\xb6', '\xbc', '\xbf',
    '\xa1', '\xc6', '\xca', '\xa3', '\xd1', '\xd3', '\xa6', '\xac', '\xaf'
);

//kodowanie backshlash-kod-szesnastkowy (php) kodu windows-1250
$ARRAY_PL_ISO_ESCAPE_BACKSHALH_HEX = array(
    '\xb9', '\xe6', '\xea', '\xb3', '\xf1', '\xf3', '\x9c', '\x9f', '\xbf',
    '\xa5', '\xc6', '\xca', '\xa3', '\xd1', '\xd3', '\x8c', '\x8f', '\xaf'
);

//wynik rawurlencode na kodzie iso-8859-2
$ARRAY_PL_ISO_URL = array(
    '%B1', '%E6', '%EA', '%B3', '%F1', '%F3', '%B6', '%BC', '%BF',
    '%A1', '%C6', '%CA', '%A3', '%D1', '%D3', '%A6', '%AC', '%AF'
);

//wynik rawurlencode na kodzie windows-1250
$ARRAY_PL_WIN_URL = array(
    '%B9', '%E6', '%EA', '%B3', '%F1', '%F3', '%9C', '%9F','%BF',
    '%A5', '%C6', '%CA', '%A3', '%D1', '%D3', '%8C', '%8F', '%AF'
);

//wynik funkcji escape z javascript
$ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT = array(
    '%u0105', '%u0107', '%u0119', '%u0142', '%u0144', '%u00F3', '%u015B', '%u017A', '%u017C',
    '%u0104', '%u0106', '%u0118', '%u0141', '%u0143', '%u00D3', '%u015A', '%u0179', '%u017B'
);


//*****Z ISO********************************************************************************

function pl_iso2win($AStr)
{
    return strtr($AStr, PL_ISO_SPECIFIC, PL_WIN_SPECIFIC);
}

function pl_iso2utf8($AStr)
{
    return iconv('ISO-8859-2', 'UTF-8', $AStr);
}


function pl_iso2utf16($AStr)
{
    return iconv('ISO-8859-2', 'UTF-16', $AStr);
}


function pl_iso2ascii($AStr)
{
    return strtr($AStr, PL_ISO_ALL, PL_ASCII_ALL);
}


//*****Z WIN********************************************************************************

function pl_win2iso($AStr)
{
    return strtr($AStr, PL_WIN_SPECIFIC, PL_ISO_SPECIFIC);
}

function pl_win2utf8($AStr)
{
    return iconv('WINDOWS-1250', 'UTF-8', $AStr);
}

function pl_win2utf16($AStr)
{
    return iconv('WINDOWS-1250', 'UTF-16', $AStr);
}

function pl_win2ascii($AStr)
{
    return strtr($AStr, PL_WIN_ALL, PL_ASCII_ALL);
}


//*****Z UTF-8********************************************************************************

function pl_utf82iso($AStr)
{
    return iconv('UTF-8', 'ISO-8859-2', $AStr);
}


function pl_utf82win($AStr)
{
    return iconv('UTF-8', 'WINDOWS-1250', $AStr);
}

function pl_utf82utf16($AStr)
{
    return iconv('UTF-8', 'UTF-8', $AStr);
}


function pl_utf82ascii($AStr)
{
    $tmp = pl_utf82iso($AStr);
    $tmp = pl_iso2ascii($AStr);
    return iconv('ISO-8859-1', 'UTF-8', $AStr);
}


//*****Z UTF-16********************************************************************************

function pl_utf162iso($AStr)
{
    return iconv('UTF-16', 'ISO-8859-2', $AStr);
}


function pl_utf162win($AStr)
{
    return iconv('UTF-16', 'WINDOWS-1250', $AStr);
}

function pl_utf162utf8($AStr)
{
    return iconv('UTF-16', 'UTF-8', $AStr);
}

function pl_utf162ascii($AStr)
{
    $tmp = pl_utf162iso($AStr);
    $tmp = pl_iso2ascii($AStr);
    return iconv('ISO-8859-1', 'UTF-16', $AStr);
}


//*****ESCAPED********************************************************************************

function pl_iso2escapeURL($AStr)
{
    return rawurlencode($AStr);
}

function pl_win2escapeURL($AStr)
{
    return rawurlencode($AStr);
}


function pl_escapeURL2iso($AStr)
{
    return rawurldecode($AStr);
}

function pl_escapeURL2win($AStr)
{
    return rawurldecode($AStr);
}


function pl_iso_escape_URL2escape_javascript($AStr)
{
    global $ARRAY_PL_ISO_URL, $ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT;

    return str_replace_arrays($ARRAY_PL_ISO_URL, $ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT, $AStr);
}

function pl_win_escape_URL2escape_javascript($AStr)
{
    global $ARRAY_PL_WIN_URL, $ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT;

    return str_replace_arrays($ARRAY_PL_WIN_URL, $ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT, $AStr);
}


function pl_escape_javascript2iso_escape_URL($AStr)
{
    global $ARRAY_PL_ISO_URL, $ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT;

    return str_replace_arrays($ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT, $ARRAY_PL_ISO_URL, $AStr);
}

function pl_escape_javascript2win_escape_URL($AStr)
{
    global $ARRAY_PL_WIN_URL, $ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT;

    return str_replace_arrays($ARRAY_PL_UTF16_ESCAPE_JAVASCRIPT, $ARRAY_PL_WIN_URL, $AStr);
}

//*****DETEKCJA********************************************************************************

function pl_detect($AStr)
{
    $usedChars = count_chars($AStr, 3);

    $plCOMMON       = napisZawieraZnakiZNapisu($usedChars, PL_COMMON);
    $plWIN_SPECIFIC = napisZawieraZnakiZNapisu($usedChars, PL_WIN_SPECIFIC);
    $plISO_SPECIFIC = napisZawieraZnakiZNapisu($usedChars, PL_ISO_SPECIFIC);

    if ($plISO_SPECIFIC && $plWIN_SPECIFIC) {
        return 'WIN-AND-ISO';
    } else if ($plISO_SPECIFIC && !$plWIN_SPECIFIC) {
        return 'ISO-8859-2';
    } else if (!$plISO_SPECIFIC && $plWIN_SPECIFIC) {
        return 'WINDOWS-1250';
    } else if ($plCOMMON) {
        return 'WIN-OR-ISO';
    } else if (strtoupper(@mb_detect_encoding($org, 'UTF-8')) === 'UTF-8') {
        return 'UTF-8';
    } else if (strtoupper(@mb_detect_encoding($org, 'UTF-16')) === 'UTF-16') {
        return 'UTF-16';
    } else {
        return 'ASCII';
    };
}

?>
