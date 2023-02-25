<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class TrueType extends Type1
{
    /**
     * @var int[]
     */
    private $widths;

    /**
     * @var Font\Structure\FontDescriptor
     */
    private $fontDescriptor;

    public function __construct(string $identifier, Font\Structure\FontDescriptor $fontDescriptor, array $widths)
    {
        parent::__construct($identifier, $fontDescriptor->getFontName());

        $this->widths = $widths;
        $this->fontDescriptor = $fontDescriptor;
    }

    /**
     * @return int[]
     */
    public function getWidths(): array
    {
        return $this->widths;
    }

    public function getFontDescriptor(): Structure\FontDescriptor
    {
        return $this->fontDescriptor;
    }

    /**
     * @return BaseObject|BaseObject[]
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitTrueTypeFont($this);
    }

    public function encode(string $escaped): string
    {
        return mb_convert_encoding($escaped, 'Windows-1252', 'UTF-8');
    }
}
