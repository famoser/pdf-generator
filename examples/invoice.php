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
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Meta;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

$margin = 15;
$meta = Meta::basic('en', 'Invoice UZH-ZI-5', ['Florian Moser']);
$document = new Document([210, 297], $margin, $meta);

$normalFont = Font::createFromDefault();
$normalText = new TextStyle($normalFont);
$secondaryText = new TextStyle($normalFont, Color::createFromHex('#6c757d'));
$boldFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
$boldText = new TextStyle($boldFont);

$sectionMargin = [10, 0, 0, 0];

// source address
$text = new Text();
$text->addSpan("famoser GmbH\n", $boldText);
$text->addSpan("c/o Florian Moser\nMoosburgstrasse 25\n8307 Effretikon\nSchweiz", $normalText);
$allocation = $document->allocate($text);
$document->createPrinter()
    ->position(210-30-$allocation->getWidth()) // shift right-most
    ->print($allocation->getContent()[0]);

// target address
$printer = $document->createPrinter(60);
$printer->printText("Universität Zürich\nZentrale Informatik\nStampfenbachstrasse 73\n8006 Zürich\nSchweiz", $normalText);

// header
$printer = $printer->position(top: 40);
$printer->printText('Invoice UZH-ZI-5', $boldText);
$printer = $printer->position(top: 8);
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

$printer = $printer->position(top: 15);
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));

// description
$flow = new Flow(FlowDirection::COLUMN);
$title = new Text(Text\Structure::Header_1);
$title->addSpan("Concept & Implementation ZI-2812-F\n", $boldText);
$flow->add($title);

$thankYou = new Text(Text\Structure::Paragraph);
$thankYou->addSpan("To whom it may concern\n\nI would like to thank you for the order and the trust you have placed in me. The tasks according to the offer of 01.01.2023 were implemented on time and accepted by the client on 30.06.2023. Only the work actually incurred will be invoiced.", $normalText);
$flow->add($thankYou);

$document->setPosition(130);
$document->add($flow);

// cost positions
$table = new Table([ColumnSize::MINIMAL, ColumnSize::AUTO, ColumnSize::MINIMAL, ColumnSize::MINIMAL, ColumnSize::MINIMAL]);
$table->setMargin([0, 3, 0, 5]);
$textByRow = [
    [$boldText, 'Pos.', 'Description', 'Quantity', 'Unit price', 'Price'],
    [$normalText, '1', 'Concept work: Requirements engineering, UX concept, PoC', '12 h', '160.00', '1\'920.00'],
    [$normalText, '2', 'Implementation', '34 h', '160.00', '5\'440.00'],
    [$boldText, '', 'Sub-Total', '', '', '7\'360.00'],
    [$secondaryText, '', 'plus VAT 14%', '', '', '1\'030.40'],
    [$boldText, '', 'Total', '', '', '8\'390.40'],
];
foreach ($textByRow as $rowIndex => $rowDefinition) {
    $row = new Row();
    $textStyle = array_shift($rowDefinition);
    foreach ($rowDefinition as $index => $text) {
        $regards = new Text();
        $regards->addSpan($text, $textStyle);
        $regards->setPadding([1, 0.3, 1, 0.3]);

        $row->set($index, $regards);
    }

    if ($rowIndex === 0) {
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
