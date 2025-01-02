<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include '../vendor/autoload.php';

use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Frontend\Resource\Meta;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

$title = 'Cat in the rain';
$author = 'Ernest Hemingway';
$fullText = file_get_contents('ernest-hemingway---cat-in-the-rain.txt');
$paragraphs = explode("\n\n", $fullText);

$meta = Meta::basic('en', $title, [$author]);
$document = new Document([210, 297], 20, $meta);
$flow = new Flow(FlowDirection::COLUMN);

// design
$normalFontSize = 6.6;
$headerFontSize = $normalFontSize * 1.6;
$font = Font::createFromFile('cruft.ttf');
$textStyle = new TextStyle($font);

// header
$titleText = new Text(Text\Structure::Title);
$titleText->addSpan($title, $textStyle, $headerFontSize);
$flow->add($titleText);

$authorText = new Text(Text\Structure::Header_2);
$authorText->addSpan('Ernest Hemingway', $textStyle, $normalFontSize);
$authorText->setMargin([0, 0, 0, $normalFontSize]);
$flow->add($authorText);

// black border under title
$blackBackground = new DrawingStyle(lineWidth: 0.5, fillColor: new Color(0, 0, 0));
$separator = new Rectangle(170, 0, $blackBackground);
$flow->addContent($separator);

// text
foreach ($paragraphs as $paragraph) {
    $text = new Text(alignment: Text\Alignment::ALIGNMENT_JUSTIFIED);
    $text->addSpan($paragraph, $textStyle, $normalFontSize);
    $text->setMargin([0, $normalFontSize, 0, 0]);
    $flow->add($text);
}
$document->add($flow);

// odd/even page numbers
for ($i = 0; $i < $document->getPageCount(); ++$i) {
    $document->createPrinter(255, $i)
        ->position(($i % 2) * 166)
        ->printText($i + 1, $textStyle);
}

$result = $document->save();
file_put_contents('book.pdf', $result);
