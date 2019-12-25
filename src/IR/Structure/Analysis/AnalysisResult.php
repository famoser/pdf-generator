<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Analysis;

use PdfGenerator\IR\Structure\Document\Font;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Size;

class AnalysisResult
{
    /**
     * @var Size[]
     */
    private $maxSizePerImage;

    /**
     * @var string[]
     */
    private $textPerFont;

    /**
     * AnalysisResult constructor.
     *
     * @param Size[] $maxSizePerImage
     * @param string[] $textPerFont
     */
    public function __construct(array $maxSizePerImage, array $textPerFont)
    {
        $this->maxSizePerImage = $maxSizePerImage;
        $this->textPerFont = $textPerFont;
    }

    public function getMaxSizePerImage(Image $image): Size
    {
        return $this->maxSizePerImage[$image->getIdentifier()];
    }

    public function getTextPerFont(Font $font): string
    {
        return $this->textPerFont[$font->getIdentifier()];
    }
}
