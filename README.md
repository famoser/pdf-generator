# pdf-generator

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![PHP Composer](https://github.com/famoser/pdf-generator/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/pdf-generator/actions/workflows/php.yml)

## About

Generates pdf files without any dependencies. Includes a layout engine to handle flow content (e.g. text spanning over
more than one page).

```bash
composer require famoser/pdf-generator
```

This is still under active development ([contributions welcome!](./CONTRIBUTE.md)), and the public API is subject to
change. If you are looking for a more mature project, see https://github.com/tecnickcom/TCPDF.

## Getting started

Using the printer:

```php
// places "Hello world" in the top-left corner of the document.
$document = new Document();
$printer = $document->createPrinter();

$bodyText = new TextStyle(Font::createFromDefault());
$printer->printText('Hello world', $bodyText);

file_put_contents('example.pdf', $document->save());
```

The printer is useful when the exact position of elements is known. For example, for correspondence with fixed layout 
(e.g. the address in a letter) or for documents with page numbers. However, for many elements (such as tables)
determining the exact position is cumbersome. For this, layouts are provided.

Using layouts:

```php
// adds a rectangle, followed by "Hello moon".
// placement is decided by Flow, which places elements one-after-the-other.
$flow = new Flow();

$rectangle = new Rectangle(width: 120, height: 80, style: new DrawingStyle());
$flow->addContent($rectangle);

$text = new Text();
$text->addSpan('Hello moon', $bodyText);
$flow->add($text);

$document->add($flow);

file_put_contents('example.pdf', $document->save());
```

The layouts allow to declare *what* needs to be printed, and then takes care of the *where*. Provided are flows, grids, 
tables and text.

## Examples

### Invoice

[Code](./examples/invoice.php) | [.pdf](./examples/invoice.pdf)
<table>
    <tbody>
        <tr>
            <td><img src="examples/invoice.png?raw=true" alt="Invoice"></td>
        </tr>
    </tbody>
</table>

### Book

[Code](./examples/book.php) | [.pdf](./examples/book.pdf)

<table>
    <tbody>
        <tr>
            <td><img src="examples/book_1.png?raw=true" alt="Book page 1"></td>
            <td><img src="examples/book_2.png?raw=true" alt="Book page 2"></td>
        </tr>
        <tr>
            <td><img src="examples/book_3.png?raw=true" alt="Book page 3"></td>
            <td><img src="examples/book_4.png?raw=true" alt="Book page 4"></td>
        </tr>
    </tbody>
</table>

