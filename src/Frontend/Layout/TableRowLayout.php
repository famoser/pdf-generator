<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use DocumentGenerator\Layout\TableRowLayoutInterface;
use PdfGenerator\Frontend\Layout\Base\StatefulColumnedLayout;
use PdfGenerator\Frontend\PdfDocument;

class TableRowLayout extends StatefulColumnedLayout implements TableRowLayoutInterface
{
    /**
     * ColumnLayout constructor.
     *
     * @param PdfDocument $pdfDocument
     * @param float $columnGutter
     * @param float $totalWidth
     * @param array $columnWidths
     */
    public function __construct(PdfDocument $pdfDocument, float $columnGutter, float $totalWidth, array $columnWidths)
    {
        parent::__construct($pdfDocument, $columnGutter, $totalWidth, $columnWidths);
    }
}
