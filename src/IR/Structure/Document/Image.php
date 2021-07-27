<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document;

use PdfGenerator\IR\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\IR\Structure\DocumentVisitor;

class Image extends BaseDocumentStructure
{
    /**
     * @var string
     */
    private $src;

    /**
     * @var string
     */
    private $data;

    const TYPE_JPG = 'jpg';
    const TYPE_JPEG = 'jpeg';
    const TYPE_PNG = 'png';
    const TYPE_GIF = 'gif';

    /**
     * @var string
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
     * Image constructor.
     */
    public function __construct(string $src, string $data, string $type, int $width, int $height)
    {
        $this->src = $src;
        $this->data = $data;
        $this->type = $type;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function accept(DocumentVisitor $visitor)
    {
        return $visitor->visitImage($this);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->src;
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getData(): string
    {
        return $this->data;
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
}
