<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page;

use PdfGenerator\IR\Structure\Analysis\AnalysisResult;
use PdfGenerator\IR\Structure\PageContent\Common\Size;
use PdfGenerator\IR\Structure\PageContent\ContentVisitor;
use PdfGenerator\IR\Structure\PageContent\ImagePlacement;
use PdfGenerator\IR\Structure\PageContent\Rectangle;
use PdfGenerator\IR\Structure\PageContent\Text;

class AnalyzeContentVisitor extends ContentVisitor
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
     * @param ImagePlacement $placement
     */
    public function visitImagePlacement(ImagePlacement $placement)
    {
        $identifier = $placement->getImage()->getIdentifier();

        if (!\in_array($identifier, $this->maxSizePerImage, true)) {
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

    /**
     * @param Rectangle $rectangle
     */
    public function visitRectangle(Rectangle $rectangle)
    {
    }

    /**
     * @param Text $param
     */
    public function visitText(Text $param)
    {
        $identifier = $param->getStyle()->getFont()->getIdentifier();
        if (!\in_array($identifier, $this->textPerFont, true)) {
            $this->textPerFont[$identifier] = '';
        }

        $this->textPerFont[$identifier] .= $param->getText();
    }

    /**
     * @return AnalysisResult
     */
    public function getAnalysisResult()
    {
        return new AnalysisResult($this->maxSizePerImage, $this->textPerFont);
    }
}
