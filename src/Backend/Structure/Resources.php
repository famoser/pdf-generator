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
use PdfGenerator\Backend\Structure\Font\Type0;
use PdfGenerator\Backend\Structure\Font\Type1;
use PdfGenerator\Backend\StructureVisitor;

class Resources extends BaseStructure
{
    /**
     * @var int
     */
    private $resourceCounter;

    /**
     * @var Type1[]
     */
    private $type1Fonts = [];

    /**
     * @var Type0[]
     */
    private $type0Fonts = [];

    /**
     * @var Image[]
     */
    private $images = [];

    /**
     * @return Type0
     */
    public function addType0Font()
    {
        $identifier = $this->generateIdentifier('F');
        $font = new Type0($identifier);
        $this->type0Fonts[$identifier] = $font;

        return $font;
    }

    /**
     * @param string $baseFont
     *
     * @return Type1
     */
    public function addType1Font(string $baseFont)
    {
        $identifier = $this->generateIdentifier('F');
        $font = new Type1($identifier, $baseFont);
        $this->type1Fonts[$identifier] = $font;

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
     * @return Type0[]
     */
    public function getType0Fonts(): array
    {
        return $this->type0Fonts;
    }

    /**
     * @return Type1[]
     */
    public function getType1Fonts(): array
    {
        return $this->type1Fonts;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
