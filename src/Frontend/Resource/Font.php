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

class Font
{
    final public const NAME_HELVETICA = 'NAME_HELVETICA';

    private ?string $name = null;

    final public const WEIGHT_NORMAL = 'WEIGHT_NORMAL';
    final public const WEIGHT_BOLD = 'WEIGHT_BOLD';

    private ?string $weight = null;

    final public const STYLE_ROMAN = 'STYLE_ROMAN';
    final public const STYLE_ITALIC = 'STYLE_ITALIC';
    final public const STYLE_OBLIQUE = 'STYLE_OBLIQUE'; // like auto-generated italic

    private ?string $style = null;

    private ?string $src = null;

    private function __construct()
    {
    }

    public static function createFromDefault(string $name = self::NAME_HELVETICA, string $weight = self::WEIGHT_NORMAL, string $style = self::STYLE_ROMAN): self
    {
        $font = new self();

        $font->name = $name;
        $font->weight = $weight;
        $font->style = $style;

        return $font;
    }

    public static function createFromFile(string $src): self
    {
        $font = new self();

        $font->src = $src;

        return $font;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }
}
