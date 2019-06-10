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
    /**
     * @var string
     */
    private $imageContent;

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
    private $imageType;

    /**
     * Image constructor.
     *
     * @param string $imageContent
     * @param int $width
     * @param int $height
     * @param int $imageType
     */
    public function __construct(string $imageContent, int $width, int $height, int $imageType)
    {
        $this->imageContent = $imageContent;
        $this->width = $width;
        $this->height = $height;
        $this->imageType = $imageType;
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
    public function getImageType(): int
    {
        return $this->imageType;
    }
}
