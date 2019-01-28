<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Pdf\Layout;

use PdfGenerator\Layout\TableRowLayoutInterface;
use PdfGenerator\Pdf\Layout\Base\StatefulColumnedLayout;
use PdfGenerator\Pdf\PdfDocumentInterface;

class TableRowLayout extends StatefulColumnedLayout implements TableRowLayoutInterface
{
    /**
     * ColumnLayout constructor.
     *
     * @param PdfDocumentInterface $pdfDocument
     * @param float $columnGutter
     * @param float $totalWidth
     * @param array $columnWidths
     */
    public function __construct(PdfDocumentInterface $pdfDocument, float $columnGutter, float $totalWidth, array $columnWidths)
    {
        parent::__construct($pdfDocument, $columnGutter, $totalWidth, $columnWidths);
    }
}
