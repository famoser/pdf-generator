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

class Image extends BaseDocumentStructure
{
    public const TYPE_JPG = 'jpg';
    public const TYPE_JPEG = 'jpeg';
    public const TYPE_PNG = 'png';
    public const TYPE_GIF = 'gif';

    private string $imageContent;

    private string $type;

    private int $width;

    private int $height;

    private int $maxUsedWidth;

    private int $maxUsedHeight;

    /**
     * Image constructor.
     */
    public function __construct(string $imageContent, string $imageType, int $width, int $height, int $maxUsedWidth, int $maxUsedHeight)
    {
        $this->imageContent = $imageContent;
        $this->type = $imageType;
        $this->width = $width;
        $this->height = $height;
        $this->maxUsedWidth = $maxUsedWidth;
        $this->maxUsedHeight = $maxUsedHeight;
    }

    /**
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
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
