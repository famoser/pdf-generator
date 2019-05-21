<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend;

use PdfGenerator\Font\Frontend\File\Table\CMapTable;
use PdfGenerator\Font\Frontend\File\Table\Interfaces\WritableTableVisitorInterface;

class WriteTablesVisitor implements WritableTableVisitorInterface
{
    /**
     * @var StreamWriter
     */
    private $writer;

    /**
     * WriteTablesVisitor constructor.
     *
     * @param StreamWriter $writer
     */
    public function __construct(StreamWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param CMapTable $cMapTable
     */
    public function visitCMap(CMapTable $cMapTable)
    {
        $this->writer->writeInt16($cMapTable->getVersion());
    }

    /**
     * @return StreamWriter
     */
    public function getWriter(): StreamWriter
    {
        return $this->writer;
    }
}
