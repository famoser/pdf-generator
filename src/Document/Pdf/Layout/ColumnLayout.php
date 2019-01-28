<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Pdf\Layout;

use PdfGenerator\Document\Layout\ColumnLayoutInterface;
use PdfGenerator\Document\Pdf\Layout\Base\StatefulColumnedLayout;
use PdfGenerator\Document\Pdf\PdfDocumentInterface;

class ColumnLayout extends StatefulColumnedLayout implements ColumnLayoutInterface
{
    /**
     * ColumnLayout constructor.
     *
     * @param PdfDocumentInterface $pdfDocument
     * @param int $columnCount
     * @param float $columnGutter
     * @param float $totalWidth
     */
    public function __construct(PdfDocumentInterface $pdfDocument, int $columnCount, float $columnGutter, float $totalWidth)
    {
        $gutterSpace = ($columnCount - 1) * $columnGutter;
        $columnWidth = (float)($totalWidth - $gutterSpace) / $columnCount;
        $columnWidths = [];
        for ($i = 0; $i < $columnCount; ++$i) {
            $columnWidths[] = $columnWidth;
        }

        parent::__construct($pdfDocument, $columnGutter, $totalWidth, $columnWidths);
    }
}
