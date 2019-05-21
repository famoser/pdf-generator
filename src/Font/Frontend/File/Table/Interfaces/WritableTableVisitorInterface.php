<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Interfaces;

use PdfGenerator\Font\Frontend\File\Table\CMapTable;

interface WritableTableVisitorInterface
{
    /**
     * @param CMapTable $cMapTable
     */
    public function visitCMap(CMapTable $cMapTable);
}
