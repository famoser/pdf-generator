<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Analysis;

use PdfGenerator\IR\Document\Page\Content\Common\Size;
use PdfGenerator\IR\Document\Page\Content\ImagePlacement;
use PdfGenerator\IR\Document\Page\Content\Rectangle;
use PdfGenerator\IR\Document\Page\Content\Text;
use PdfGenerator\IR\Document\Page\ContentVisitor;

class AnalyzeContentVisitor extends ContentVisitor
{
    /**
     * @var Size[]
     */
    private array $maxSizePerImage = [];

    /**
     * @var string[]
     */
    private array $textPerFont = [];

    public function visitImagePlacement(ImagePlacement $placement): void
    {
        $identifier = $placement->getImage()->getIdentifier();

        if (!\array_key_exists($identifier, $this->maxSizePerImage)) {
            $this->maxSizePerImage[$identifier] = $placement->getSize();

            return;
        }

        $existing = $this->maxSizePerImage[$identifier];
        $new = $placement->getSize();

        $maxWidth = max($existing->getWidth(), $new->getWidth());
        $maxHeight = max($existing->getHeight(), $new->getHeight());
        $size = new Size($maxWidth, $maxHeight);

        $this->maxSizePerImage[$identifier] = $size;
    }

    public function visitRectangle(Rectangle $rectangle)
    {
    }

    public function visitText(Text $param): void
    {
        $identifier = $param->getStyle()->getFont()->getIdentifier();
        if (!\array_key_exists($identifier, $this->textPerFont)) {
            $this->textPerFont[$identifier] = '';
        }

        $this->textPerFont[$identifier] .= $param->getText();
    }

    public function getAnalysisResult(): AnalysisResult
    {
        return new AnalysisResult($this->maxSizePerImage, $this->textPerFont);
    }
}
