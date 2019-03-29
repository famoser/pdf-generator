<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content;

use PdfGenerator\Backend\Content\Base\PlacedContent;
use PdfGenerator\Backend\Content\Text\TextSymbol;
use PdfGenerator\Backend\ContentVisitor;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class TextContent extends PlacedContent
{
    /**
     * @var TextSymbol[]
     */
    private $textSymbols;

    /**
     * TextContent constructor.
     *
     * @param float $xCoordinate
     * @param float $yCoordinate
     * @param TextSymbol[] $textSymbols
     */
    public function __construct(float $xCoordinate, float $yCoordinate, array $textSymbols)
    {
        parent::__construct($xCoordinate, $yCoordinate);

        $this->textSymbols = $textSymbols;
    }

    /**
     * @param ContentVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitTextContent($this, $file);
    }

    /**
     * @return TextSymbol[]
     */
    public function getTextSymbols(): array
    {
        return $this->textSymbols;
    }
}
