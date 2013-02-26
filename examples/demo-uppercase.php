<?php

require_once 'Hexdump.php';

$data = 'Metashock Hexdump';

// display uppercased hex chars
hexdump($data, 8, PHP_EOL, TRUE);

?>
