<?php

include '../vendor/autoload.php';

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;

$margin = 15;
$document = new LinearDocument([210, 297], $margin);

$normalFont = Font::createFromDefault();
$normalText = new TextStyle($normalFont);
$boldFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
$boldText = new TextStyle($boldFont);
$headerText = new TextStyle($boldFont, $normalText->getFontSize() * 1.6);

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
$printer->printText("Invoice UZH-ZI-5", $headerText);
$printer = $printer->position(0, 8);
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));
$printer = $printer->position(0, 3);

$columns = [
    "Date:\nPayment until:",
    "30.06.2023\n30.07.2023",
    "Performance Period:\nProject reference:",
    "01.01.2023 - 30.06.2023\nUZH ZI NTP",
];
$columnPrinter = $printer;
foreach ($columns as $column) {
    $columnPrinter->printText($column, $normalText);
    $columnPrinter = $columnPrinter->position(45, 0);
}

$printer = $printer->position(0, 11);
$printer->printRectangle(180, 0.1, new DrawingStyle(0.1));

// content
$flow = new Flow();
$paragraph = new Paragraph();
$paragraph->add($headerText, "Concept & Implementation NTP\n");
$paragraph->add($normalText, "To whom it may concern\n\nI would like to thank you for the order and the trust you have placed in me. The tasks according to the offer of 01.01.2023 were implemented on time and accepted by the client on 30.06.2023. Only the work actually incurred will be invoiced.\n\nBest regards\nFlorian Moser");
$flow->addContent($paragraph);
$document->position(130);
$document->add($flow);

$result = $document->save();
file_put_contents('invoice.pdf', $result);
