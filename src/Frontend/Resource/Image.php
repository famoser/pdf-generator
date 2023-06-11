<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Resource;

class Image
{
    final public const TYPE_JPG = 'TYPE_JPG';
    final public const TYPE_JPEG = 'TYPE_JPEG';
    final public const TYPE_PNG = 'TYPE_PNG';
    final public const TYPE_GIF = 'TYPE_GIF';

    private function __construct(private readonly string $src, private readonly string $type = self::TYPE_JPG)
    {
    }

    public static function createFromFile(string $src): self
    {
        $type = self::resolveType($src);

        return new self($src, $type);
    }

    private static function resolveType(string $src): string
    {
        $extension = pathinfo($src, PATHINFO_EXTENSION);

        return match ($extension) {
            'jpeg' => self::TYPE_JPEG,
            'png' => self::TYPE_PNG,
            'gif' => self::TYPE_GIF,
            default => self::TYPE_JPG,
        };
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
