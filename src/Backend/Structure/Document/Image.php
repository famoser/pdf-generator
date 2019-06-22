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
    const TYPE_JPG = 'jpg';
    const TYPE_JPEG = 'jpeg';
    const TYPE_PNG = 'png';
    const TYPE_GIF = 'gif';

    /**
     * @var string
     */
    private $imageContent;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $maxUsedWidth;

    /**
     * @var int
     */
    private $maxUsedHeight;

    /**
     * Image constructor.
     *
     * @param string $imageContent
     * @param int $imageType
     * @param int $width
     * @param int $height
     * @param int $maxUsedWidth
     * @param int $maxUsedHeight
     */
    public function __construct(string $imageContent, int $imageType, int $width, int $height, int $maxUsedWidth, int $maxUsedHeight)
    {
        $this->imageContent = $imageContent;
        $this->type = $imageType;
        $this->width = $width;
        $this->height = $height;
        $this->maxUsedWidth = $maxUsedWidth;
        $this->maxUsedHeight = $maxUsedHeight;
    }

    /**
     * @param DocumentVisitor $documentVisitor
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitImage($this);
    }

    /**
     * @return string
     */
    public function getImageContent(): string
    {
        return $this->imageContent;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getMaxUsedWidth(): int
    {
        return $this->maxUsedWidth;
    }

    /**
     * @return int
     */
    public function getMaxUsedHeight(): int
    {
        return $this->maxUsedHeight;
    }
}
