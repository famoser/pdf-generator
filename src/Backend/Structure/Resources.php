<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\StructureVisitor;

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
     * @var Image[]
     */
    private $images = [];

    /**
     * @param string $subtype
     * @param string $baseFont
     *
     * @return Font
     */
    public function addFont(string $subtype, string $baseFont)
    {
        $identifier = $this->generateIdentifier('F');
        $font = new Font($identifier, $subtype, $baseFont);
        $this->fonts[$identifier] = $font;

        return $font;
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function addImage(string $imagePath)
    {
        $identifier = $this->generateIdentifier('I');
        $image = new Image($identifier, $imagePath);
        $this->images[$identifier] = $image;

        return $image;
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

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
