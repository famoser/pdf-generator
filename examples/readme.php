<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;

include '../vendor/autoload.php';

// places "Hello world" in the top-left corner of the document.
$document = new Document();
$bodyText = new TextStyle(Font::createFromDefault());
$printer = $document->createPrinter();
$printer->printText("Hello earth", $bodyText);
file_put_contents('readme_1.pdf', $document->save());

// adds a rectangle, followed by "Hello moon".
// placement is decided by Flow.
$flow = new Flow();

$rectangle = new Rectangle(width: 120, height: 80, style: new DrawingStyle());
$flow->addContent($rectangle);

$text = new Text();
$text->addSpan("Hello moon", $bodyText);
$flow->add($text);

$document->add($flow);
file_put_contents('readme_2.pdf', $document->save());
