<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Style\Part;

class Font
{
    const NAME_HELVETICA = 'NAME_HELVETICA';

    /**
     * @var string|null
     */
    private $name;

    public const STYLE_ROMAN = 'STYLE_ROMAN';
    public const STYLE_ITALIC = 'STYLE_ITALIC';
    public const STYLE_OBLIQUE = 'STYLE_OBLIQUE'; // like auto-generated italic

    /**
     * @var string|null
     */
    private $style;

    public const WEIGHT_NORMAL = 'WEIGHT_NORMAL';
    public const WEIGHT_BOLD = 'WEIGHT_BOLD';

    /**
     * @var string|null
     */
    private $weight;

    /**
     * @var string|null
     */
    private $src;

    private function __construct()
    {
    }

    public static function createFromDefault(string $name = self::NAME_HELVETICA, string $style = self::STYLE_ROMAN, string $weight = self::WEIGHT_NORMAL)
    {
        $font = new self();

        $font->name = $name;
        $font->style = $style;
        $font->weight = $weight;

        return $font;
    }

    public static function createFromFile(string $src)
    {
        $font = new self();

        $font->src = $src;

        return $font;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }
}
