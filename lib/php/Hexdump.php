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
 *  This library contains two functions:
 *  
 *      - hexdump()     prints a hexdump of data. you can configure the 
 *                      output.
 *
 *      - asciidump()   prints an ascii string. non ascii chars will be 
 *                      replaced by a configurable char
 *
 *  @author thorsten heymann 
 *  @copyright thorsten heymann
 */

/**
 *  prints a hexdump of data. you can configure the output.
 *
 *  @param string $data
 *  @param integer $ncolumns = 8
 */
function hexdump($data, $ncolumns = 16) {

    if (is_string($data)){
        $len = strlen($data);
    } else {
        throw new InvalidArgumentException(
            '$data expected to be array or string. ' . gettype($data) . ' found.');
    }

    for($i = 0; $i < $len; $i++){
        printf('%02x ', ord($data[$i]));
        $c = $i % $ncolumns;
        // printf a new line after n * 2 chars
        if($c === $ncolumns - 1) {
            // after n hex chars we print the ascii dump
            for($ii = $i - $c; $ii < $i + 1; $ii++) {
                asciidump($data[$ii]);
            }
            print(PHP_EOL);
        }
    }


    // if the last line has no exactly $ncolumn chars
    // we insert padding chars
    $rest = $ncolumns - ($i % $ncolumns);
    if($rest !== $ncolumns) {
        for($ii = 0; $ii < $rest; $ii++){
            printf('   ');
        }
        for($ii = $i - ($ncolumns - $rest); $ii < $i; $ii++){
            asciidump($data[$ii]);
        }
    }
    print(PHP_EOL);
}


/**
 *  Prints $data. Non ascii chars will be converted
 *  to $placeholder
 *
 *  @param string $data
 *  @param string $placeholder='.'
 *  @return void
 */
function asciidump($data, $placeholder = '.') {

    if (is_string($data)){
        $len = strlen($data);
    } else {
        throw new InvalidArgumentException(
            '$data expected to be array or string. ' . gettype($data) . ' found.');
    }

    for($i = 0; $i < $len; $i++) {
        if(ord($data[$i]) > 31 && ord($data[$i]) < 127) {
            print($data[$i]);
        } else {
            print('.');
        }
    }
}


