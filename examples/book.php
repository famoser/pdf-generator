<?php

include '../vendor/autoload.php';

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Meta;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

$meta = Meta::basic('en', 'Cat in the rain', ['Ernest Hemingway']);
$document = new Document([210, 297], 20, $meta);

$font = Font::createFromFile('cruft.ttf');
$normalFontSize = 6.6;
$headerFontSize = $normalFontSize * 1.6;
$default = new TextStyle($font);

$fullText = file_get_contents('ernest-hemingway---cat-in-the-rain.txt');
$paragraphs = explode("\n\n", $fullText);

// title
$flow = new Flow(FlowDirection::COLUMN);
$title = new Text(Text\Structure::Title);
$title->addSpan("Cat in the rain", $default, $headerFontSize);
$flow->add($title);

$author = new Text(Text\Structure::Header_1);
$author->addSpan("Ernest Hemingway", $default, $normalFontSize);
$flow->add($author);

// black border under title
$rectangleBlock = new ContentBlock();
$rectangleBlock->setHeight(0.5);
$rectangleBlock->setMargin([0, 0, 0, $headerFontSize]);
$rectangleBlock->setStyle(new ElementStyle(0.5, new Color(0,0, 0)));
$flow->add($rectangleBlock);

// text
foreach ($paragraphs as $paragraph) {
    $text = new Text(alignment: Text\Alignment::ALIGNMENT_JUSTIFIED);
    $text->addSpan($paragraph, $default, $normalFontSize);
    $text->setMargin([0,$normalFontSize,0,0]);
    $flow->add($text);
}
$document->add($flow);

// odd/even page numbers
for ($i = 0; $i < $document->getPageCount(); $i++) {
    $document->createPrinter(255, $i)
        ->position(($i % 2) * 166)
        ->printText($i + 1, $default);
}

$result = $document->save();
file_put_contents('book.pdf', $result);
