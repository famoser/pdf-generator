<?php

include '../vendor/autoload.php';

use Famoser\PdfGenerator\Frontend\Content\Paragraph;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;

$document = new LinearDocument([210, 297], 20);

$font = Font::createFromFile('cruft.ttf');
$normalText = new TextStyle($font, 6.6);
$headerText = new TextStyle($font, $normalText->getFontSize() * 1.6);

$text = file_get_contents('ernest-hemingway---cat-in-the-rain.txt');
$textParagraphs = explode("\n\n", $text);

// title
$flow = new Flow(Flow::DIRECTION_COLUMN);
$paragraph = new Paragraph();
$paragraph->add($headerText, "Cat in the rain");
$paragraph->add($normalText, "\nErnest Hemingway\n");
$flow->addContent($paragraph);

// black border under title
$rectangle = new Rectangle(new DrawingStyle(0.5));
$rectangleBlock = new ContentBlock($rectangle);
$rectangleBlock->setHeight(0.5);
$rectangleBlock->setMargin([0, 0, 0, $headerText->getFontSize() * 1.6]);
$flow->add($rectangleBlock);

// text
foreach ($textParagraphs as $textParagraph) {
    $paragraph = new Paragraph();
    $paragraph->add($normalText, $textParagraph);
    $paragraphBlock = new ContentBlock($paragraph);
    $paragraphBlock->setMargin([0, 0, 0, $normalText->getFontSize() * 1.3]);
    $flow->add($paragraphBlock);
}
$document->add($flow);

// odd/even page numbers
for ($i = 0; $i < $document->getPageCount(); $i++) {
    $printer = $document->createPrinter($i, 20 + ($i % 2) * 166, 272);
    $printer->printText($i + 1, $normalText);
}

$result = $document->save();
file_put_contents('book.pdf', $result);
