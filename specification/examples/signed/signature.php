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

use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

$document = new LinearDocument([210, 297]);

$normalFont = Font::createFromDefault();
$normalText = new TextStyle($normalFont);

// target address
$margin = 15;
$printer = $document->createPrinter(0, $margin, $margin);
$printer->printText("Hello world", $normalText);

// TODO: sign document
$result = $document->save();
file_put_contents('signature.pdf', $result);
