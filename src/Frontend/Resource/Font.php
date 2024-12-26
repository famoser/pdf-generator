<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Resource;

use Famoser\PdfGenerator\Frontend\Resource\Font\FontFamily;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontStyle;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontWeight;

class Font
{
    final public const NAME_HELVETICA = 'HELVETICA';

    private ?FontFamily $family = null;

    final public const WEIGHT_NORMAL = 'WEIGHT_NORMAL';
    final public const WEIGHT_BOLD = 'WEIGHT_BOLD';

    private ?FontWeight $weight = null;

    final public const STYLE_ROMAN = 'STYLE_ROMAN';
    final public const STYLE_ITALIC = 'STYLE_ITALIC';
    final public const STYLE_OBLIQUE = 'STYLE_OBLIQUE'; // like auto-generated italic

    private ?FontStyle $style = null;

    private ?string $src = null;

    private function __construct()
    {
    }

    public static function createFromDefault(FontFamily $family = FontFamily::Helvetica, FontWeight $weight = FontWeight::Normal, FontStyle $style = FontStyle::Roman): self
    {
        $font = new self();

        $font->family = $family;
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

    public function getFamily(): ?FontFamily
    {
        return $this->family;
    }

    public function getWeight(): ?FontWeight
    {
        return $this->weight;
    }

    public function getStyle(): ?FontStyle
    {
        return $this->style;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }
}
