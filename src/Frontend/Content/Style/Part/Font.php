<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Style\Part;

class Font
{
    public const NAME_HELVETICA = 'NAME_HELVETICA';

    private ?string $name;

    public const WEIGHT_NORMAL = 'WEIGHT_NORMAL';
    public const WEIGHT_BOLD = 'WEIGHT_BOLD';

    private ?string $weight;

    public const STYLE_ROMAN = 'STYLE_ROMAN';
    public const STYLE_ITALIC = 'STYLE_ITALIC';
    public const STYLE_OBLIQUE = 'STYLE_OBLIQUE'; // like auto-generated italic

    private ?string $style;

    private ?string $src;

    private function __construct()
    {
    }

    public static function createFromDefault(string $name = self::NAME_HELVETICA, string $weight = self::WEIGHT_NORMAL, string $style = self::STYLE_ROMAN): Font
    {
        $font = new self();

        $font->name = $name;
        $font->weight = $weight;
        $font->style = $style;

        return $font;
    }

    public static function createFromFile(string $src): Font
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
