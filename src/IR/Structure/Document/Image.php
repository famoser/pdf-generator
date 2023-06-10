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

use PdfGenerator\IR\Structure\Document\Base\BaseDocumentResource;
use PdfGenerator\IR\Structure\DocumentVisitor;

readonly class Image extends BaseDocumentResource
{
    final public const TYPE_JPG = 'TYPE_JPG';
    final public const TYPE_JPEG = 'TYPE_JPEG';
    final public const TYPE_PNG = 'TYPE_PNG';
    final public const TYPE_GIF = 'TYPE_GIF';

    public function __construct(private string $src, private string $data, private string $type, private int $width, private int $height)
    {
    }

    public static function create(string $imagePath, string $type): self
    {
        $data = file_get_contents($imagePath);
        [$width, $height] = getimagesizefromstring($data);

        return new self($imagePath, $data, $type, $width, $height);
    }

    public function accept(DocumentVisitor $visitor): \PdfGenerator\Backend\Structure\Document\Image
    {
        return $visitor->visitImage($this);
    }

    public function getIdentifier(): string
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
