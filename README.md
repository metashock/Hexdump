# Hexdump

This library provides a hexdump() function for php. I've developed it once when I was required to create a PHP client for a binary network protocol and used it to debug the traffic.

What you can see now is the result from grabbing the code from my old harddisk, refactoring it, adding some features, creating a PEAR installer and finally exporting it to github.

Enjoy!


## Installation

To install Hexdump you can use the PEAR installer or get a tarball and install the files manually.

___
### Using the PEAR installer

If you didn't already discovered my pear channel you'll have to do it. Also you should issue a channel update:

    pear channel-discover metashock.de/pear
    pear channel-update metashock

After this you can install Hexdump. The following command will install the lastest stable version:

    pear install -a metashock/Hexdump

If you want to install a specific version or a beta version you'll have to specify this version on the command line. For example:

    pear install -a metashock/Hexdump-0.2.0

___
### Manually download and install files

Instead, you can just download the package from http://www.metashock.de/pear and put into a folder listed in your `include_path`. Please refer to the php.net [documentation](http://php.net/manual/en/ini.core.php#ini.include-path) of the `include_path` directive.

## Documentation

Before using the `hexdump()` function you'll have to include Hexdump.php. If you followed the installation instructions you won't worry where the file is located in your classpath. Type:

```php
require_once 'Hexdump.php';
```

If the `require_once` fails, something went wrong with the installation.

___
### hello world


A simple hello world should be most intuitive. Just call the function `hexdump()` with the data as argument:

```php
hexdump("hello world!\n");
```

Output:

    00000000: 68 65 6c 6c 6f 20 77 6f 72 6c 64 21 0a          |hello.world!.|
___
### The `Hexdump` class behind the scenes


Although you will in most cases just use the function `hexdump()`, the work behind the scenes is done by the `Hexdump` class. `hexdump()` is just a wrapper for `Hexdump::draw()`

```php
$hexdump = new Hexdump();
$hexdump->draw("hello world!\n");
```
This code will behave exactly the same as the hello world example above.

So why an extra class? The sense of the Hexdump class is to act as a global configuration container for `hexdump()` thus to keep the syntax of the function itself short.

Options can be set or get using the static method `Hexdump::option()`

```php
// set option value
Hexdump::option('option_name', $option_value);
// get option value
$option_value = Hexdump::option('option_name');
```

Note that you can overwrite global options once set using arguments passed to `hexdump()`

___
### Available Options

The following options are available. Default values are printed bold.

<table>
  <tr>
    <th>Option</th>
    <th>Type</th>
    <th>Possible Values</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>ncols</td>
    <td>unsinged integer</td>
    <td>The value must be greater than zero. Defaults to <b>16</b></td>
    <td>The number of bytes printed per line</td>
  </tr>
  <tr>
    <td>format</td>
    <td>string</td>
    <td><ul><li><b>plain</b></li><li>html</li></ul></td>
    <td>When set to true hexdump() will return the hexdump as a string instead of directly output it to stdout</td>
  </tr>
  <tr>
    <td>uppercase</td>
    <td>boolean</th>
    <td><ul><li>true</li><li><b>false</b></li></ul></td>
    <td>Uppercasing hex numbers? (FF instead of ff)</td>
  </tr>
  <tr>
    <td>output</td>
    <td>string</th>
    <td><ul><li><b>stdout</b></li><li>stderr</li><li>none</li></ul></td>
    <td>When set to true hexdump() will return the hexdump as a string instead of directly output it to stdout</td>
  </tr>
</table>

## Code Examples

Note that all example assume that you have included Hexdump.php

```php
<?php

require_once 'Hexdump.php';
```

### hello world (8 columns)

```php
hexdump("hello world!\n", 8);
```

Output:

    00000000: 68 65 6c 6c 6f 20 77 6f |hello.wo|
    00000008: 72 6c 64 21 0a          |rld!.|

___
### Uppercased Hex chars


```php
hexdump("hello world!\n", 8, PHP_EOL, TRUE);
```

Output:
  
    00000000: 68 65 6C 6C 6F 20 77 6F |hello.wo|
    00000008: 72 6C 64 21 0A          |rld!.|

___
### Dump this document


```php
hexdump(file_get_contents('README.md'));
```

Output:

    00000000: 23 20 48 65 78 64 75 6d 70 0a 0a 54 68 69 73 20 |#.Hexdump..This.|
    00000010: 6d 69 6e 69 20 6c 69 62 72 61 72 79 20 70 72 6f |mini.library.pro|
    00000020: 76 69 64 65 73 20 61 20 68 65 78 64 75 6d 70 28 |vides.a.hexdump(|
    00000030: 29 20 66 75 6e 63 74 69 6f 6e 20 66 6f 72 20 70 |).function.for.p|
    00000040: 68 70 2e 20 49 20 64 65 76 65 6c 6f 70 65 64 20 |hp..I.developed.|
    00000050: 69 74 20 6f 6e 63 65 20 77 68 65 6e 20 49 20 77 |it.once.when.I.w|
    00000060: 61 73 20 72 65 71 75 69 72 65 64 20 74 6f 20 63 |as.required.to.c|
    00000070: 72 65 61 74 65 20 61 20 50 48 50 20 63 6c 69 65 |reate.a.PHP.clie|
    00000080: 6e 74 20 66 6f 72 20 61 20 62 69 6e 61 72 79 20 |nt.for.a.binary.|
    00000090: 6e 65 74 77 6f 72 6b 20 70 72 6f 74 6f 63 6f 6c |network.protocol|
    000000a0: 2e 0a 0a 57 68 61 74 20 79 6f 75 20 73 65 65 20 |...What.you.see.|
    000000b0: 6e 6f 77 20 69 73 20 74 68 65 20 72 65 73 75 6c |now.is.the.resul|
    000000c0: 74 20 66 72 6f 6d 20 67 72 61 62 62 69 6e 67 20 |t.from.grabbing.|
    000000d0: 74 68 65 20 63 6f 64 65 20 66 72 6f 6d 20 6d 79 |the.code.from.my|
    000000e0: 20 6f 6c 64 20 68 61 72 64 64 69 73 6b 2c 20 72 |.old.harddisk,.r|
    000000f0: 65 66 61 63 74 6f 72 69 6e 67 20 69 74 2c 20 61 |efactoring.it,.a|
    00000100: 64 64 69 6e 67 20 73 6f 6d 65 20 66 65 61 74 75 |dding.some.featu|
    ... (more lines will follow)
___
### Dumping to stderr

```php
// set the option globally
Hexdump::option('output', 'stderr');

// all pending hexdumps will go to stderr
hexdump($data);
hexdump($moreData);
// ...
```

---
### Formatting as HTML


```php

// to get a HTML dump do either 
hexdump("hello world!\n", 16, 'html');

// ... or set the option globally
Hexdump::option('format', 'html');

// now all pending hexdumps will be formatted as HTML
hexdump($data);
// ...
```

Output:


## Using `phphd` from the command line

Beside from Hexdump.php the package contains a command line executable `phphd`. It prints hexdumps either from files or from stdin. Although a linux distribution will regulary already have a hexdump program on board, it might be helpful in some cases. Especially when you are working on Windows. 

`phphd` comes with the following options:

    Usage : ./phphd [OPTIONS] [FILE]

    Prints a hexdump of FILE to stdout. If FILE was omitted ./phphd reads from stdin.

    -c COLUMNS     Number of bytes per row. COLUMNS must be a
                   postive integer and greater than zero

    -f FORMAT      The output format. Can be plain or html    

    -u             Prints uppercased hex numbers

### Example usage

Note that the examples assume that you are using the bash shell.

___
Printing a hexdump from file.txt:

    $ phphd -c 8 file.txt

___
Printing an uppercased hexdump from stdin:

    $ phphd -u
    test
    ^D

or:

    $ phphd -u <<<EOF
    test
    EOF



