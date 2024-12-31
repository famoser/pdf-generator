<?php

include '../vendor/autoload.php';

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\IR\Document\Meta;

$meta = Meta::createMeta('en', 'Cat in the rain', ['Ernest Hemingway']);
$document = new LinearDocument([210, 297], 20, $meta);

$font = Font::createFromFile('cruft.ttf');
$normalText = new TextStyle($font, 6.6);
$headerText = new TextStyle($font, $normalText->getFontSize() * 1.6);

$text = file_get_contents('ernest-hemingway---cat-in-the-rain.txt');
$textParagraphs = explode("\n\n", $text);

// title
$flow = new Flow(FlowDirection::COLUMN);
$title = new Text(Text\Structure::Title);
$title->add($headerText, "Cat in the rain");
$flow->add($title);

$author = new Text(Text\Structure::Header_1);
$author->add($normalText, "Ernest Hemingway");
$flow->add($author);

// black border under title
$rectangleBlock = new ContentBlock();
$rectangleBlock->setHeight(0.5);
$rectangleBlock->setMargin([0, 0, 0, $headerText->getFontSize() * 1.6]);
$rectangleBlock->setStyle(new ElementStyle(0.5, new Color(0,0, 0)));
$flow->add($rectangleBlock);

// text
foreach ($textParagraphs as $textParagraph) {
    $paragraph = new Text(alignment: Text\Alignment::ALIGNMENT_JUSTIFIED);
    $paragraph->setMargin([0,$headerText->getFontSize(),0,0]);
    $paragraph->add($normalText, $textParagraph);
    $flow->add($paragraph);
}
$document->add($flow);

// odd/even page numbers
for ($i = 0; $i < $document->getPageCount(); $i++) {
    $printer = $document->createPrinter($i, 20 + ($i % 2) * 166, 272);
    $printer->printText($i + 1, $normalText);
}

$result = $document->save();
file_put_contents('book.pdf', $result);
