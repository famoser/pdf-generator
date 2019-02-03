<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\Backend\File;
use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\IR\Structure\Base\BaseStructure;
use PdfGenerator\IR\Structure\Supporting\Font;
use PdfGenerator\IR\StructureVisitor;

class Resources extends BaseStructure
{
    /**
     * @var int
     */
    private $resourceCounter;

    /**
     * @var Font[]
     */
    private $fonts = [];

    /**
     * @param string $subtype
     * @param string $baseFont
     *
     * @return Font
     */
    public function addFont(string $subtype, string $baseFont)
    {
        $identifier = $this->generateIdentifier('F');
        $font = new Font($subtype, $baseFont, $identifier);
        $this->fonts[$identifier] = $font;

        return $font;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    private function generateIdentifier(string $prefix)
    {
        return $prefix . $this->resourceCounter++;
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitResources($this, $file);
    }

    /**
     * @return Font[]
     */
    public function getFonts(): array
    {
        return $this->fonts;
    }
}
