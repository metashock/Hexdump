<?php
/**
 * This library contains a hexdump function.
 *
 * PHP Version 8.3.0
 *
 * @category  Debug
 * @package   Hexdump
 * @author    Thorsten Heymann <info@metashock.net>
 * @copyright 2011 - 2024 Thorsten Heymann
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   1.2.0
 * @link      http://www.metashock.de/pirum
 * @since     0.1.0
 */
/**
 * Prints a hexdump of data. You can configure the number of
 * bytes per line, the line delimiter and choose whether hexadecimal
 * numbers should be uppercase or not.
 *
 * @param string $data The data to be hexdumped
 * @param integer|null $ncols The number of bytes per line
 * @param string|null $format The output format. Either plain or html
 * @param boolean|null $uppercase If true, hexadecimal number will be uppercase
 * @param string|null $output Where should the output go to?
 *
 *                           Either stdout, stderr or none. If It's none, then
 *                           hexdump will just be returned
 *
 * @return string
 *
 * @example examples/demo.php
 *
 * @version 1.2.0
 * @since 0.1.0
 */
function hexdump (
    string $data,
    int    $ncols = null,
    string $format = null,
    bool   $uppercase = null,
    string $output = null,
) {
    static $printer;
    if(!$printer) {
        $printer = new Hexdump();
    }

    return $printer->draw($data, $ncols, $format, $uppercase, $output);
}

/**
 * Usually one will just use the hexdump() function as It's less to type.
 * So why an extra class? The sense of the Hexdump class is to act as a
 * global configuration container for hexdump() thus to keep the syntax
 * of the function itself short.
 *
 * Also, it is responsible for param validation
 *
 *
 * @version 1.2.0
 * @since 1.1.0
 */
class Hexdump
{
    /**
     * @var array|null $options
     */
    protected static ?array $options = null;

    /**
     * Gets or sets global options
     *
     * @param string $optname Name of the option
     * @param mixed $optval If omitted, the method will return the current
     *                         value. If set, the option will be set to it
     *
     * @return mixed
     *
     * @throws \UnexpectedValueException if either, the option name is unknown
     * or the optval isn't valid for the option.
     * @version 1.2.0
     * @since 1.1.0
     *
     */
    public static function option(string $optname, mixed $optval = null): mixed
    {
        if (!is_array(self::$options)) {
            self::$options = array(
                'ncols' => 16,
                'output' => 'stdout',
                'format' => 'plain',
                'uppercase' => false
            );
        }

        // Check that the option name is valid
        if (!array_key_exists($optname, self::$options)) {
            throw new \UnexpectedValueException (
                'Unknown option \'' . $optname . '\'. Expected was one of the '
                . 'following: ncols|output|format|uppercase'
            );
        }

        // If $optval is null, fn acts as a getter and returns the value.
        // Note, this will work only because none of the options currently
        // allows NULL values. If this is the case sometimes, the check
        // will have to use func_get_args() instead.
        if (is_null($optval)) {
            return self::$options[$optname];
        }

        // The option is requested to set. We have to validate optval
        // before its usage. static::validateOption() will throw an Exception
        // if $optval isn't valid
        static::validateOption($optname, $optval);

        // Finally, set it
        self::$options[$optname] = $optval;

        return null;
    }

    /**
     * Validates an option value
     *
     * @param string $optname
     * @param mixed $optval
     *
     * @return boolean
     *
     * @throws \InvalidArgumentException if $optval isn't from the expected type
     * @throws \UnexpectedValueException if $optval is not in the value range
     * @since 1.1.0
     *
     * @version 1.2.0
     */
    protected static function validateOption(string $optname, mixed $optval): bool
    {
        switch ($optname) {
            case 'ncols':
                // check the type of $optval
                if (!is_int($optval)) {
                    throw new \InvalidArgumentException(
                        'The value for ncols expected to be an integer. '
                        . gettype($optval) . ' found.'
                    );
                }

                // check whether $ncolumns is greater than zero
                if ($optval < 1) {
                    throw new \UnexpectedValueException(
                        'The value for ncols expected to be greater than zero.'
                        . ' Got: \'' . strval($optval) . '\''
                    );
                }
                break;

            case 'output':
                // check the type of $optval
                if (!is_string($optval)) {
                    throw new \InvalidArgumentException(
                        'The value for output expected to be a string. '
                        . gettype($optval) . ' found.'
                    );
                }

                // check if the value is valid
                if (!in_array($optval, array('stdout', 'stderr', 'none'))) {
                    throw new \UnexpectedValueException(
                        'The value for output expected to be one of the '
                        . 'following: stdout|stderr|none. zero. '
                        . 'Got: \'' . $optval . '\''
                    );
                }
                break;

            case 'format':
                // check the type of $optval
                if (!is_string($optval)) {
                    throw new \InvalidArgumentException(
                        'The value for format expected to be a string. '
                        . gettype($optval) . ' found.'
                    );
                }

                // check if the value is valid
                if (!in_array($optval, array('plain', 'html'))) {
                    throw new \UnexpectedValueException(
                        'The value for format expected to be one of the '
                        . 'following: plain|html. '
                        . 'Got: \'' . $optval . '\''
                    );
                }
                break;

            case 'uppercase':
                // check the type of $optval
                if (!is_bool($optval)) {
                    throw new \InvalidArgumentException(
                        'The value for uppercase expected to be a boolean. '
                        . gettype($optval) . ' found.'
                    );
                }
                break;

            default:
                throw new \UnexpectedValueException (
                    'Unknown option \'' . $optname . '\'. Expected was one of the '
                    . 'following: ncols|output|format|uppercase'
                );
        }

        return true;
    }

    /**
     * Wrapper for output operations. Separates between stdout
     * and stderr output and does HTML post-processing.
     *
     * @param array|string $data
     * @param string $format
     * @param string $output
     *
     * @return void
     * @since 1.1.0
     * @version 1.2.0
     *
     */
    protected function write(array|string $data, string $format, string $output): void
    {
        if (is_array($data)) {
            $string = implode('', $data);
        } else {
            $string = $data;
        }

        switch ($format) {
            case 'plain' :
                // nothing to do
                break;

            case 'html' :
                $string = htmlspecialchars($string);
                break;

            default :
                throw new \UnexpectedValueException(
                    'The value for format expected to be one of the '
                    . 'following: plain|html. '
                    . 'Got: \'' . $format . '\''
                );
        }

        // printing although $output === 'none' may sound awesome
        // but output buffering is used when output is none, so we
        // don't care at this point
        if ($output === 'stdout' || $output === 'none') {
            echo $string;
        } else if ($output === 'stderr') {
            fwrite(STDERR, $string);
        }
    }

    /**
     * Renders a hexdump of data. You can configure the number of
     * bytes per line, the line delimiter and choose whether hexadecimal
     * numbers should be uppercase or not.
     *
     * @param string $data The data to be hexdumped
     * @param integer|null $ncols The number of bytes per line
     * @param string|null $format The line delimiter
     * @param boolean|null $uppercase If true, hexadecimal number will be uppercase
     * @param string|null $output
     *
     * @return string
     *
     * @throws \InvalidArgumentException if one of the params data type mismatches
     * @throws \UnexpectedValueException if $ncols is lower than 1
     *
     * @example examples/demo.php
     *
     * @version 1.2.0
     * @since 1.1.0
     */
    public function draw(
        string $data,
        int    $ncols = null,
        string $format = null,
        bool   $uppercase = null,
        string $output = null
    ): string
    {
        // validate the remaining arguments
        if (!is_null($ncols)) {
            static::validateOption('ncols', $ncols);
        } else {
            $ncols = static::option('ncols');
        }

        if (!is_null($format)) {
            static::validateOption('format', $format);
        } else {
            $format = static::option('format');
        }

        if (!is_null($output)) {
            static::validateOption('output', $output);
        } else {
            $output = static::option('output');
        }

        if (!is_null($uppercase)) {
            static::validateOption('uppercase', $uppercase);
        } else {
            $uppercase = self::option('uppercase');
        }

        if ($output === 'stdout') {
            // using output buffering to increase performance
            ob_start(null, 4096);
        } else {
            ob_start();
        }

        // default line delim for plain output
        $linedelim = PHP_EOL;

        // when the format is html, we enclose the output in <pre> tags
        if ($format === 'html') {
            $this->write('<pre>', 'plain', $output);
        }

        // will contain a binary string with all non-printable bytes
        static $from = '';
        // will contain a string containing length of $from times '.'
        static $to = '';
        // the column of the current byte
        $c = 1;
        // total offset in $data
        $offset = 0;
        // total number of bytes to process
        $len = strlen($data);

        // return $data on empty input
        if ($len < 1) {
            return $data;
        }

        // prepare a translation table to convert non-printable bytes
        // to a '.' char. the translation table will be created statically
        // the first time hexdump is called. Using a translation table with
        // php's strtr() function appeared slight faster than translating them
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
        $this->write('00000000: ', $format, $output);
        for ($i = 0; $i < $len; $i++, $c++) {

            // get byte at current position and convert it to a hex string
            if ($uppercase) {
                $this->write(strtoupper(bin2hex($data[$i])) . ' ', $format, $output);
            } else {
                $this->write(bin2hex($data[$i]) . ' ', $format, $output);
            }

            // after the number of bytes has been reached, we print
            // the ascii representation of the last line.
            if ($c === $ncols) {
                $this->write(array(
                    '|',
                    // non-printable characters have to be converted to '.'
                    strtr(substr($data, $i - $ncols + 1, $ncols), $from, $to),
                    '|', $linedelim
                ), $format, $output); // end of line

                // next line
                $c = 0;

                // increment line offset and prepend it to the new line
                // if the end of data isn't reached now
                $offset += $ncols;
                if ($offset === $len) {
                    // when the format is html, we enclose the output in <pre> tags
                    if ($format === 'html') {
                        $this->write('</pre>', 'plain', $output);
                    }

                    $hexdump = ob_get_contents();
                    if ($output === 'stdout') {
                        ob_end_flush();
                    } else {
                        ob_end_clean();
                    }
                    return $hexdump;
                }
                if ($uppercase) {
                    $this->write(sprintf('%08X: ', $offset), $format, $output);
                } else {
                    $this->write(sprintf('%08x: ', $offset), $format, $output);
                }
            }
        }

        // the last line needs special attention because it may not contain
        // exactly $ncols bytes. the remaining gap between the last hex char
        // and the ascii output has therefore to be filled with spaces

        // get the number of remaining bytes
        $remains = $ncols - ($i % $ncols);
        if ($remains !== $ncols) {
            // display whitespaces for each remaining byte
            $this->write(array(
                str_repeat('   ', $remains)
                // display the asciis for the last bytes
            , '|'
            , strtr(substr($data, $i - ($i % $ncols)), $from, $to)
            , '|'
                // and a final newline
            , $linedelim
            ), $format, $output);
        }

        // when the format is html, we enclose the output in <pre> tags
        if ($format === 'html') {
            $this->write('</pre>', 'plain', $output);
        }

        $hexdump = ob_get_contents();
        if ($output === 'stdout') {
            ob_end_flush();
        } else {
            ob_end_clean();
        }

        return $hexdump;
    }
}
