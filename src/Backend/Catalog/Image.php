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

    const IMAGE_TYPE_JPEG = 0;

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
    private $content;

    /**
     * Image constructor.
     */
    public function __construct(string $identifier, int $type, string $content, float $width, float $height)
    {
        $this->setIdentifier($identifier);

        \assert($type === self::IMAGE_TYPE_JPEG);

        $this->content = $content;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
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
