<?php
/**
 * This library contains a hexdump function.
 *
 * PHP Version 5.1.0
 * 
 * @category  Debug
 * @package   Hexdump
 * @author    Thorsten Heymann <info@metashock.net>
 * @copyright 2011 - 2012 Thorsten Heymann
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   1.0.0
 * @link      http://www.metashock.de/pirum
 * @since     0.1.0
 */

/**
 * Prints a hexdump of data. You can configure the number of
 * bytes per line, the linedelimiter and choose whether hexadecimal
 * numbers should be uppercased or not.
 *
 * @example examples/demo.php
 *
 * @param string  $data      The data to be hexdumped 
 * @param integer $ncolumns  The number of bytes per line 
 * @param string  $linedelim The line delimiter
 * @param boolean $uppercase If true hexadecimal number will be uppercased
 *
 * @return void
 *
 * @throws InvalidArgumentException if one of the params data type mismatch
 * @throws UnexpectedValueException if $ncolumns is lower than 1
 *
 * @version 1.0.0
 * @since 0.1.0
 */
function hexdump (
    $data,
    $ncolumns = 16,
    $linedelim = PHP_EOL,
    $uppercase = false
) {

    // check the type of $data
    if (!is_string($data)) {
        throw new InvalidArgumentException(
            '$data expected to be string. ' . gettype($data) . ' found.'
        );
    }

    // check the type of $ncolumns
    if (!is_int($ncolumns)) {
        throw new InvalidArgumentException(
            '$ncolumns expected to be integer. ' . gettype($ncolumns) . ' found.'
        );
    }

    // check whether $ncolumns is a positive integer and greater than zero
    if ($ncolumns < 1 ) {
        throw new UnexpectedValueException(
            '$ncolumns expected to be greater than zero. Got ' . strval($ncolumns)
        );
    }

    // check the type of $linedelim
    if (!is_string($linedelim)) {
        throw new InvalidArgumentException(
            '$linedelim expected to be string. ' . gettype($linedelim) . ' found.'
        );
    }

    // check the type of $uppercase
    if (!is_bool($uppercase)) {
        throw new InvalidArgumentException(
            '$uppercase expected to be string. ' . gettype($uppercase) . ' found.'
        );
    }

    // will contain a binary string with all non printable bytes
    static $from = '';
    // will contain a string containing length of $from times '.'
    static $to = '';
    // the column of the current byte
    $c = 1;
    // total offset in $data
    $offset = 0;
    // total number of bytes to process
    $len = strlen($data);

    // using output buffering to increase performance
    ob_start(null, 4096);

    // prepare a translation table to convert non printable bytes
    // to a '.' char. the translation table will be created statically 
    // the first time hexdump is called. Using a translation table with 
    // php's strtr() function appeared slight faster then translating them
    // in this script directly. @thanks to mindplay.dk for the strtr() idea
    // @see http://stackoverflow.com/questions/1057572/\ ...
    // ... how-can-i-get-a-hex-dump-of-a-string-in-php
    if ($from === '') {
        for ($i = 0; $i < 0x21; $i++) {
            $from .= chr($i);
            $to .= '.';
        }

        for ($i = 0x7E; $i <= 0xFF; $i++) {
            $from .= chr($i);
            $to .= '.';
        }
    }

    
    // iterate through $data
    echo '00000000: ';
    for ($i = 0; $i < $len; $i++, $c++) {

        // get byte at current position and convert it to a hex string
        if ($uppercase) {
            echo strtoupper(bin2hex($data[$i])) . ' ';
        } else {
            echo bin2hex($data[$i]) . ' ';
        }

        // after the number of bytes has been reached we print
        // the ascii representation of the last line.
        if ($c === $ncolumns) {
            echo '|' ,
            // non printable characters have to be converted to '.'
            strtr(substr($data, $i - $ncolumns + 1, $ncolumns), $from, $to) ,
            '|' , $linedelim; // end of line

            // next line
            $c = 0;

            // increment line offset and prepend it to the new line
            // if the end of data isn't reached now
            $offset += $ncolumns;
            if ( $offset === $len) {
                ob_end_flush();
                return;
            }
            if ($uppercase) {
                printf('%08X: ', $offset);
            } else {
                printf('%08x: ', $offset);
            }
        }
    }

    // the last line needs special attention because it may not contain
    // exactly $ncolumn bytes. the remaining gap between the last hex char 
    // and the ascii output has therefore to be filled with spaces

    // get the number of remaining bytes
    $remains = $ncolumns - ($i % $ncolumns);
    if ( $remains !== $ncolumns) {
        // display whitespaces for each remaining byte
        echo str_repeat('   ', $remains)
        // display the asciis for the last bytes
            , '|' , strtr(substr($data, $i - ($i % $ncolumns)), $from, $to) , '|'
        // and a final newline
            , $linedelim;
    }
    ob_end_flush();
}


