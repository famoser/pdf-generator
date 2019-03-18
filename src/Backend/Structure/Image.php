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
use PdfGenerator\Backend\Structure\Base\IdentifiableStructure;
use PdfGenerator\Backend\StructureVisitor;

class Image extends IdentifiableStructure
{
    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * @var string
     */
    private $imageData;

    /**
     * Image constructor.
     *
     * @param string $identifier
     * @param float $width
     * @param float $height
     * @param string $imagePath
     */
    public function __construct(string $identifier, string $imagePath)
    {
        parent::__construct($identifier);

        list($width, $height) = getimagesize($imagePath);
        $this->width = $width;
        $this->height = $height;
        $this->imageData = file_get_contents($imagePath);
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitImage($this, $file);
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getImageData(): string
    {
        return $this->imageData;
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return 'JPXDecode';
    }
}
