<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Structure;

use PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use PdfGenerator\Backend\File\StructureVisitor;

class FileHeader extends BaseStructure
{
    /**
     * @var float
     */
    private $version = 1.7;

    /**
     * @param StructureVisitor $visitor
     *
     * @return string
     */
    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitFileHeader($this);
    }

    /**
     * @return float
     */
    public function getVersion(): float
    {
        return $this->version;
    }
}
