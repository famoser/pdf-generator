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

use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Frontend\Resource\Meta;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

$margin = 15;
$meta = Meta::basic('en', 'Invoice UZH-ZI-5', ['Florian Moser']);
$document = new Document([210, 297], $margin, $meta);
$flow = new Flow(FlowDirection::COLUMN);

$normalFont = Font::createFromDefault();
$normalText = new TextStyle($normalFont);
$secondaryText = new TextStyle($normalFont, Color::createFromHex('#6c757d'));
$boldFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
$boldText = new TextStyle($boldFont);

$textFontSize = 4.5;
$headerFontSize = $textFontSize * 1.6;
$spacer = $headerFontSize;

// source address
$text = new Text(alignment: Text\Alignment::ALIGNMENT_RIGHT);
$text->addSpan("famoser GmbH\n", $boldText, $textFontSize);
$text->addSpan("c/o Florian Moser\nOchsengasse 66\n4123 Allschwil\nSchweiz", $normalText, $textFontSize);
$flow->add($text);

// target address
$text = new Text();
$text->addSpan("Universität Zürich\nZentrale Informatik\nStampfenbachstrasse 73\n8006 Zürich\nSchweiz", $normalText, $textFontSize);
$text->setMargin([0, $spacer*2, 0, 0]);
$flow->add($text);

// title
$text = new Text(Text\Structure::Title);
$text->addSpan('Invoice UZH-ZI-5', $boldText, $headerFontSize);
$text->setMargin([0, $headerFontSize, 0, 0]);
$flow->add($text);

$document->add($flow);

// print overview
$printer = $document->createPrinter();
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));

$columns = [
    "Date:\nPayment until:",
    "30.06.2023\n30.07.2023",
    "Performance Period:\nProject reference:",
    "01.01.2023 - 30.06.2023\nZI-2812-F",
];
$columnPrinter = $printer->position(top: 3);
foreach ($columns as $column) {
    $columnPrinter->printText($column, $normalText);
    $columnPrinter = $columnPrinter->position(45);
}

$document->shiftPosition(15);
$printer = $document->createPrinter();
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));

// description
$flow = new Flow(FlowDirection::COLUMN);
$title = new Text(Text\Structure::Header_1);
$title->addSpan("Concept & Implementation ZI-2812-F\n", $boldText);
$flow->add($title);

$thankYou = new Text(Text\Structure::Paragraph);
$thankYou->addSpan("To whom it may concern\n\nI would like to thank you for the order and the trust you have placed in me. The tasks according to the offer of 01.01.2023 were implemented on time and accepted by the client on 30.06.2023. Only the work actually incurred will be invoiced.", $normalText);
$flow->add($thankYou);

$flow->setMargin([0, $spacer, 0, 0]);
$document->add($flow);

// cost positions
$table = new Table([ColumnSize::MINIMAL, ColumnSize::AUTO, ColumnSize::MINIMAL, ColumnSize::MINIMAL, ColumnSize::MINIMAL]);
$table->setMargin([0, $spacer, 0, $spacer]);
$textByRow = [
    [$boldText, 'Pos.', 'Description', 'Quantity', 'Unit price', 'Price'],
    [$normalText, '1', 'Concept work: Requirements engineering, UX concept, PoC', '12 h', '160.00', '1\'920.00'],
    [$normalText, '2', 'Implementation', '34 h', '160.00', '5\'440.00'],
    [$boldText, '', 'Sub-Total', '', '', '7\'360.00'],
    [$secondaryText, '', 'plus VAT 14%', '', '', '1\'030.40'],
    [$boldText, '', 'Total', '', '', '8\'390.40'],
];
$verticalPadding = $spacer / 20;
foreach ($textByRow as $rowIndex => $rowDefinition) {
    $row = new Row();
    $textStyle = array_shift($rowDefinition);
    foreach ($rowDefinition as $index => $text) {
        $regards = new Text();
        $regards->addSpan($text, $textStyle);
        $startPadding = $index > 0 ? $spacer / 2 : 0;
        $regards->setPadding([$startPadding, $verticalPadding, 0, $verticalPadding]);

        $row->set($index, $regards);
    }

    if (0 === $rowIndex) {
        $table->addHead($row);
    } else {
        $table->addBody($row);
    }
}
$document->add($table);

// greeting formula
$flow = new Flow();
$regards = new Text();
$regards->addSpan("In case of questions, please do not hesitate to ask.\n\nBest regards\nFlorian Moser", $normalText);
$document->add($regards);

$result = $document->save();
file_put_contents('invoice.pdf', $result);
