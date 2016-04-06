<?php
global $lang;

$flaga = '';
if($lang == 'fr') $flaga = 'be';
if($lang == 'be') $flaga = 'fr';

$nl_link=kameleon_href('','',"$flaga:$page");
?>