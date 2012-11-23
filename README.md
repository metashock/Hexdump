Hexdump
=======

This mini library provides a hexdump() function for php. I needed this during the development of a client for a binary network protocol. However it may be useful in any cases whem debugging a PHP script that has to handle binary data.

Installation
------------

- You can install Hexdump using pear:

    # pear channel-discover metashock.de/pirum
    # pear install metashock/Hexdump-0.1.0

- You can just download Hexdump.php and put in anywhere into your
  include\_path

Examples
--------

This usage examples refer to demo.php. You can execute it by typing 

    php demo.php

in terminal. The following three calls demonstrate the usage of hexdump():

    $data = file_get_contents(__FILE__);

    // display with default settings
    hexdump($data);

    // display 8 bytes per line
    hexdump($data, 8);

    // display uppercased hex chars
    hexdump($data, 16, PHP_EOL, TRUE);
  
