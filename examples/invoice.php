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
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Layout\TextSpan;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\IR\Document\Meta;

$margin = 15;
$meta = Meta::createMeta('en', 'Invoice UZH-ZI-5', ['Florian Moser']);
$document = new LinearDocument([210, 297], $margin, $meta);

$normalFont = Font::createFromDefault();
$normalText = new TextStyle($normalFont);
$boldFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
$boldText = new TextStyle($boldFont);
$headerText = new TextStyle($boldFont, $normalText->getFontSize() * 1.6);
$secondaryText = new TextStyle($normalFont, $normalText->getFontSize(), $normalText->getLeading(), Color::createFromHex('#6c757d'));

$sectionMargin = [10, 0, 0, 0];

// target address
$printer = $document->createPrinter(0, $margin, 60);
$printer->printText("Universität Zürich\nZentrale Informatik\nStampfenbachstrasse 73\n8006 Zürich\nSchweiz", $normalText);

// source address
$printer = $document->createPrinter(0, 160, $margin);
$printer->printText("famoser GmbH\n", $boldText);
$printer->printText("c/o Florian Moser\nMoosburgstrasse 25\n8307 Effretikon\nSchweiz", $normalText);

// header
$printer = $document->createPrinter(0, $margin, 100);
$printer->printText('Invoice UZH-ZI-5', $headerText);
$printer = $printer->position(0, 8);
$printer->printRectangle(new Rectangle(180, 0.1, new DrawingStyle(0.1)));
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
$printer->printRectangle(new Rectangle(180, 0.1, new DrawingStyle(0.1)));

// description
$flow = new Flow();
$title = new Text(Text\Structure::Header_1);
$title->add($headerText, "Concept & Implementation ZI-2812-F\n");
$flow->add($title);

$thankYou = new Text(Text\Structure::Paragraph);
$thankYou->add($normalText, "To whom it may concern\n\nI would like to thank you for the order and the trust you have placed in me. The tasks according to the offer of 01.01.2023 were implemented on time and accepted by the client on 30.06.2023. Only the work actually incurred will be invoiced.");
$flow->add($thankYou);

$document->position(130);
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
        $regards->add($textStyle, $text);
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
$regards->add($normalText, "In case of questions, please do not hesitate to ask.\n\nBest regards\nFlorian Moser");
$document->add($regards);

$result = $document->save();
file_put_contents('invoice.pdf', $result);
