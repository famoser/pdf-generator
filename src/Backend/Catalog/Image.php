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

use PdfGenerator\Backend\Catalog\Base\BaseIdentifiableStructure;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;

class Image extends BaseIdentifiableStructure
{
    public const IMAGE_TYPE_JPEG = 0;

    private float $width;

    private float $height;

    private string $content;

    /**
     * Image constructor.
     */
    public function __construct(string $identifier, int $type, string $content, float $width, float $height)
    {
        $this->setIdentifier($identifier);

        \assert(self::IMAGE_TYPE_JPEG === $type);

        $this->content = $content;
        $this->width = $width;
        $this->height = $height;
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitImage($this);
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFilter(): string
    {
        return 'JPXDecode';
    }
}
