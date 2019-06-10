<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog;

use PdfGenerator\Backend\Catalog\Base\BaseStructure;
use PdfGenerator\Backend\Catalog\Base\IdentifiableStructureTrait;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class Image extends BaseStructure
{
    use IdentifiableStructureTrait;

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
     * @param string $imagePath
     */
    public function __construct(string $identifier, string $imagePath)
    {
        $this->setIdentifier($identifier);

        list($width, $height) = getimagesize($imagePath);
        $this->width = $width;
        $this->height = $height;
        $this->imageData = file_get_contents($imagePath);
    }

    /**
     * @param CatalogVisitor $visitor
     *
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitImage($this);
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
