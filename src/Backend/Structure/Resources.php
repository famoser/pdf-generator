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
     * @var Font[]
     */
    private $fonts = [];

    /**
     * @var Image[]
     */
    private $images = [];

    /**
     * Resources constructor.
     *
     * @param Font[] $fonts
     * @param Image[] $images
     */
    public function __construct(array $fonts, array $images)
    {
        $this->fonts = $fonts;
        $this->images = $images;
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
