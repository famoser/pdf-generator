<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Analysis;

use Famoser\PdfGenerator\IR\Document\Content\Common\Size;
use Famoser\PdfGenerator\IR\Document\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Resource\Image;

readonly class AnalysisResult
{
    /**
     * @param Size[]   $maxSizePerImage
     * @param string[] $textPerFont
     */
    public function __construct(private array $maxSizePerImage, private array $textPerFont)
    {
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
