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

    public const TYPE_JPG = 'TYPE_JPG';
    public const TYPE_JPEG = 'TYPE_JPEG';
    public const TYPE_PNG = 'TYPE_PNG';
    public const TYPE_GIF = 'TYPE_GIF';

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
     * @throws \Exception
     */
    public static function create(string $imagePath): self
    {
        $data = file_get_contents($imagePath);
        list($width, $height) = getimagesizefromstring($data);
        $type = self::getImageType($imagePath);

        return new self($imagePath, $data, $type, $width, $height);
    }

    /**
     * @throws \Exception
     */
    private static function getImageType(string $imagePath): string
    {
        /** @var string $extension */
        $extension = pathinfo($imagePath, \PATHINFO_EXTENSION);
        switch ($extension) {
            case 'jpg':
                return self::TYPE_JPG;
            case 'jpeg':
                return self::TYPE_JPEG;
            case 'png':
                return self::TYPE_PNG;
            case 'gif':
                return self::TYPE_GIF;
            default:
                throw new \Exception('Image type not supported: '.$extension.'. Use jpg, jpeg, png or gif');
        }
    }

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
