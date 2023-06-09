<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document;

use PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\Backend\Structure\DocumentVisitor;

readonly class Image extends BaseDocumentStructure
{
    final public const TYPE_JPG = 'jpg';
    final public const TYPE_JPEG = 'jpeg';
    final public const TYPE_PNG = 'png';
    final public const TYPE_GIF = 'gif';

    public function __construct(private string $imageContent, private string $type, private int $width, private int $height, private int $maxUsedWidth, private int $maxUsedHeight)
    {
    }

    public function accept(DocumentVisitor $documentVisitor): \PdfGenerator\Backend\Catalog\Image
    {
        return $documentVisitor->visitImage($this);
    }

    public function getImageContent(): string
    {
        return $this->imageContent;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getMaxUsedWidth(): int
    {
        return $this->maxUsedWidth;
    }

    public function getMaxUsedHeight(): int
    {
        return $this->maxUsedHeight;
    }
}
