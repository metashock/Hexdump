<?php
/*

    Copyright (c) 2011, Thorsten Heymann
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

    - Redistributions of source code must retain the above copyright 
      notice, this list of conditions and the following disclaimer.

    - Redistributions in binary form must reproduce the above copyright 
      notice, this list of conditions and the following disclaimer in the 
      documentation and/or other materials provided with the distribution.


    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
    AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
    IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
    ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE 
    LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
    CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
    POSSIBILITY OF SUCH DAMAGE.

*/

/**
 *  This library contains the hexdump function
 *  
 *      - hexdump()     prints a hexdump of data. you can configure the output.
 * 
 *  @author thorsten heymann 
 *  @copyright thorsten heymann
 */
/**
 *  prints a hexdump of data. you can configure the output.
 *
 *  @param string $data
 *  @param integer $ncolumns = 16
 *  @param string $linedelim = PHP_EOL
 *  @param boolean $uppercase = FALSE
 */
function hexdump (
    $data,
    $ncolumns = 16,
    $linedelim = PHP_EOL,
    $uppercase = FALSE
) {
    ob_start(NULL, 1024);

    if (is_string($data)){
        $len = strlen($data);
    } else if (is_array($data)) {
        $len = count($data);
    } else {
        throw new InvalidArgumentException(
            '$data expected to be array or string. ' . gettype($data) . ' found.');
    }

    // using translation table for ascii output
    // @thanks to mindplay.dk for the strtr() idea
    //
    // Formerly I translated char by char to ascii. Now I'm using a static 
    // translation buffer and translate $ncolumns chars at once 
    // delegating the inner work to strtr(). In my tests this was slight faster.
    // See http://stackoverflow.com/questions/1057572/how-can-i-get-a-hex-dump-of-a-string-in-php
    // for more details
    static $from = '';
    static $to = '';
    if ($from === '') {
        for ($i=0; $i<=0xFF; $i++) {
            $from .= chr($i);
            $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : '.';
        }
    }
    
    $c = 1;
    $offset = 0;
    echo '00000000: ';
    for($i = 0; $i < $len; $i++, $c++){
        if($uppercase) {
            echo strtoupper(bin2hex($data[$i])) . ' ';
        } else {
            echo bin2hex($data[$i]) . ' ';
        }
        // printf a new line after n * 2 chars
        if($c === $ncolumns) {
            // after each $ncolumns chars we print the ascii dump and newline
            echo '|' . strtr(substr($data, $i - $ncolumns + 1, $ncolumns),
                $from, $to) . '|' . $linedelim;
            $c = 0;
            $offset += $ncolumns;
            if($uppercase) {
                printf('%08X: ', $offset);
            } else {
                printf('%08x: ', $offset);
            }
        }
    }

    // the last line needs special attention
    $remains = $ncolumns - ($i % $ncolumns);

    // display whitespaces for each 'missing' byte
    echo str_repeat('   ', $remains)
    // and the asciis for the last bytes
        . '|' . strtr(substr($data, $i - ($i % $ncolumns)), $from, $to) . '|'
    // and newline
        . $linedelim;

    ob_end_flush();
}


