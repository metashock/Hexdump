<?php
/**
 *
 *
 */
/**
 *
 *
 */
class HexdumpText extends PHPUnit_Framework_TestCase
{

    /**
     * @requires the library
     */
    public function setUp() {
        require_once 'Hexdump.php';
    }


   /**
    *
    */
   public function testHelloWorld() {
        $expected = <<<EOF
00000000: 68 65 6c 6c 6f 20 77 6f 72 6c 64 21 0a          |hello.world!.|

EOF;
        $this->expectOutputString($expected);
        hexdump("hello world!\n");
   } 


   /**
    *
    *
    */
   public function testHelloWorld8Cols() {
        $expected = <<<EOF
00000000: 68 65 6c 6c 6f 20 77 6f |hello.wo|
00000008: 72 6c 64 21 0a          |rld!.|

EOF;
        $this->expectOutputString($expected);
        hexdump("hello world!\n", 8);
   } 


   /**
    *
    *
    */
   public function testFullLastLine() {
        $expected = <<<EOF
00000000: 68 65 6c 6c 6f 20 77 6f |hello.wo|
00000008: 72 6c 64 20 31 32 33 0a |rld.123.|

EOF;
        $this->expectOutputString($expected);
        hexdump("hello world 123\n", 8);
   }



   /**
    *
    *
    */
   public function testUppercasedOuput() {
        $expected = <<<EOF
00000000: 68 65 6C 6C 6F 20 77 6F |hello.wo|
00000008: 72 6C 64 21 0A          |rld!.|

EOF;
        $this->expectOutputString($expected);
//        echo hexdump("hello world!\n", 8, NULL, TRUE);

        // test the global option
        Hexdump::option('uppercase', TRUE);
        $this->assertEquals(TRUE, Hexdump::option('uppercase'));

        $this->expectOutputString($expected);
        hexdump("hello world!\n", 8);
   }



    public function testOutputToStderr() {
        ob_start();
        hexdump("hello world!\n", NULL, NULL, NULL, 'stderr');
        $output = ob_get_contents();
        ob_end_clean();
        // should be empty as all outputs has gone to stderr
        $this->assertEmpty($output);
    }



    public function testFormatHtml() {
        $expected = <<<EOF
<pre>00000000: 68 65 6c 6c 6f 20 77 6f 72 6c 64 21 20 54 68 69 |hello.world!.Thi|
00000010: 73 20 69 73 20 6f 75 74 70 75 74 20 66 6f 72 20 |s.is.output.for.|
00000020: 3c 68 74 6d 6c 3e 0a                            |&lt;html&gt;.|
</pre>
EOF;
        $this->expectOutputString($expected);
        Hexdump::option('format', 'html');
        Hexdump::option('uppercase', FALSE);
        hexdump("hello world! This is output for <html>\n");
    }


    /**
     * Tests that only valid option names can be passed to Hexdump::option()
     *
     * @expectedException UnexpectedValueException
     */
    public function testBadOption() {
        Hexdump::option('foo');
    }
}

