<?php
require_once 'lib/php/Hexdump.php';

// we display ourself
$data =@file_get_contents($argv[0]);
if(!$data) {
    die('cannot open ' . $argv[0]);
}

// display with default settings
hexdump($data);

// display 8 bytes per line
hexdump($data, 8);

// display uppercased hex chars
hexdump($data, 16, PHP_EOL, TRUE);

// display as html
echo '<pre>';
hexdump($data, 16, '<br />');
echo '</pre>' . PHP_EOL;

