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

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\Layout\Parts\Row;
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\Layout\Table;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;
use PdfGenerator\IR\Document\Content\Common\Color;

$margin = 15;
$document = new LinearDocument([210, 297], $margin);

$normalFont = Font::createFromDefault();
$normalText = new TextStyle($normalFont);
$boldFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
$boldText = new TextStyle($boldFont);
$headerText = new TextStyle($boldFont, $normalText->getFontSize() * 1.6);
$secondaryText = new TextStyle($normalFont, $normalText->getFontSize(), $normalText->getLineHeight(), Color::createFromHex('#6c757d'));

$sectionMargin = [10, 0, 0, 0];

// target address
$printer = $document->createPrinter(0, $margin, 60);
$printer->printText("Universität Zürich\nZentrale Informatik\nStampfenbachstrasse 73\n8006 Zürich\nSchweiz", $normalText);

// source address
$phrases = [];
$phrases[] = new Paragraph\Phrase("famoser GmbH\n", $boldText);
$phrases[] = new Paragraph\Phrase("c/o Florian Moser\nMoosburgstrasse 25\n8307 Effretikon\nSchweiz", $normalText);
$printer = $document->createPrinter(0, 160, $margin);
$printer->printPhrases($phrases);

// header
$printer = $document->createPrinter(0, $margin, 100);
$printer->printText('Invoice UZH-ZI-5', $headerText);
$printer = $printer->position(0, 8);
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));
$printer = $printer->position(0, 3);

$columns = [
    "Date:\nPayment until:",
    "30.06.2023\n30.07.2023",
    "Performance Period:\nProject reference:",
    "01.01.2023 - 30.06.2023\nZI-2812-F",
];
$columnPrinter = $printer;
foreach ($columns as $column) {
    $columnPrinter->printText($column, $normalText);
    $columnPrinter = $columnPrinter->position(45, 0);
}

$printer = $printer->position(0, 11);
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));

// description
$flow = new Flow();
$paragraph = new Paragraph();
$paragraph->add($headerText, "Concept & Implementation ZI-2812-F\n");
$paragraph->add($normalText, "To whom it may concern\n\nI would like to thank you for the order and the trust you have placed in me. The tasks according to the offer of 01.01.2023 were implemented on time and accepted by the client on 30.06.2023. Only the work actually incurred will be invoiced.");
$flow->addContent($paragraph);
$document->position(130);
$document->add($flow);

// table
$table = new Table([ColumnSize::MINIMAL, ColumnSize::AUTO, ColumnSize::MINIMAL, ColumnSize::MINIMAL, ColumnSize::MINIMAL]);
$table->setMargin([0, 3, 0, 5]);
$headerText = [$boldText, 'Pos.', 'Description', 'Quantity', 'Unit price', 'Price'];
$bodyText = [
    [$normalText, '1', 'Concept work: Requirements engineering, UX concept, PoC', '12 h', '160.00', '1\'920.00'],
    [$normalText, '2', 'Implementation', '34 h', '160.00', '5\'440.00'],
    [$boldText, '', 'Sub-Total', '', '', '7\'360.00'],
    [$secondaryText, '', 'plus VAT 14%', '', '', '1\'030.40'],
    [$boldText, '', 'Total', '', '', '8\'390.40'],
];

function createRow(array $rowDefinition): Row
{
    $row = new Row();
    $textStyle = array_shift($rowDefinition);
    foreach ($rowDefinition as $index => $text) {
        $paragraph = new Paragraph();
        $paragraph->add($textStyle, $text);

        $content = new ContentBlock($paragraph);
        $content->setPadding([1, 0.3, 1, 0.3]);

        $row->set($index, $content);
    }

    return $row;
}

$row = createRow($headerText);
$table->addHead($row);
foreach ($bodyText as $line) {
    $row = createRow($line);
    $table->addBody($row);
}

$document->add($table);

// greeting formula
$flow = new Flow();
$paragraph = new Paragraph();
$paragraph->add($normalText, "In case of questions, please do not hesitate to ask.\n\nBest regards\nFlorian Moser");
$flow->addContent($paragraph);
$document->add($flow);

$result = $document->save();
file_put_contents('invoice.pdf', $result);
