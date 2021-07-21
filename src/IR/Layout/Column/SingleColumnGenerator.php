<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Layout\Column;

use PdfGenerator\IR\Cursor;
use PdfGenerator\IR\Structure\Document;

class SingleColumnGenerator implements ColumnGenerator
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var float[]
     */
    private $pageSize;

    /**
     * @var float[]
     */
    private $margin;

    /**
     * @var Cursor
     */
    private $lastCursor;

    public function __construct(Document $document, $pageSize = [210, 297], $margin = [25, 25, 25, 25])
    {
        $this->document = $document;
        $this->pageSize = $pageSize;
        $this->margin = $margin;
    }

    public function getNextColumn(): Column
    {
        $nextPageIndex = $this->lastCursor ? $this->lastCursor->getPageIndex() + 1 : 0;
        $page = new Document\Page($nextPageIndex, $this->pageSize);
        $this->document->addPage($page);

        $xCoordinate = $this->margin[3];
        $yCoordinate = $this->pageSize[1] - $this->margin[0];
        $this->lastCursor = new Cursor($xCoordinate, $yCoordinate, $nextPageIndex);

        $width = $this->pageSize[0] - $this->margin[1] - $this->margin[3];
        $height = $this->pageSize[1] - $this->margin[0] - $this->margin[2];

        return new Column($this->lastCursor, $width, $height);
    }
}
