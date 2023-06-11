<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\CursorPrinter\Layout\Column;

use PdfGenerator\FrontendResources\CursorPrinter\Cursor;
use PdfGenerator\IR\Document;

class SingleColumnGenerator implements ColumnGenerator
{
    private ?Cursor $lastCursor = null;

    /**
     * @param float[] $pageSize
     * @param float[] $margin
     */
    public function __construct(private readonly Document $document, private $pageSize = [210, 297], private $margin = [25, 25, 25, 25])
    {
    }

    public function getNextColumn(): Column
    {
        $nextPageIndex = $this->lastCursor ? $this->lastCursor->getPageIndex() + 1 : 0;
        $page = new \PdfGenerator\IR\Document\Page($nextPageIndex, $this->pageSize);
        $this->document->addPage($page);

        $xCoordinate = $this->margin[3];
        $yCoordinate = $this->pageSize[1] - $this->margin[0];
        $this->lastCursor = new Cursor($xCoordinate, $yCoordinate, $nextPageIndex);

        $width = $this->pageSize[0] - $this->margin[1] - $this->margin[3];
        $height = $this->pageSize[1] - $this->margin[0] - $this->margin[2];

        return new Column($this->lastCursor, $width, $height);
    }
}
