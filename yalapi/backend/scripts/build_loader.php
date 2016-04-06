<?php

$public      = __DIR__ . '/../public';
$source      = $public . '/script/frontend.js';
$destination = $public . '/resource/data.html';
$index       = $public . '/index.html';

$lines = file($source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$parts = array();
$tmp   = array();

foreach ($lines as $line) {
    $line = trim($line);
    if (substr($line, 0, 2) == '//') {
        continue;
    }
    $tmp[] = addslashes($line);
    if (count($tmp) == 20) {
        $parts[] = '<script type="text/javascript">s("' . implode('\n', $tmp) . '");</script>';
        $tmp = array();
    }
}
if (!empty($tmp)) {
    $parts[] = '<script type="text/javascript">s("' . implode('\n', $tmp) . '");</script>';
}
$size = count($parts);

array_unshift($parts,
    '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
    '<html xmlns="http://www.w3.org/1999/xhtml">',
    '<head>',
    '<title>Data</title>',
    '<script type="text/javascript">parent.loader.script.size = ' . $size . ';function s(data){parent.loader.on_script_data(data);}</script>'
);
array_push($parts,
    '</head><body></body></html>'
);

$content = implode(PHP_EOL, $parts);
file_put_contents($destination, $content);

die('DONE, script size: ' . $size . PHP_EOL);