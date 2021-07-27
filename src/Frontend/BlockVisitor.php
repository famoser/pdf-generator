<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

class BlockVisitor
{
    /**
     * @var \PdfGenerator\IR\Structure\Document
     */
    private $document;

    /**
     * @var PageGenerator
     */
    private $pageGenerator;

    /**
     * BlockVisitor constructor.
     */
    public function __construct(\PdfGenerator\IR\Structure\Document $document, PageGenerator $pageGenerator)
    {
        $this->document = $document;
        $this->pageGenerator = $pageGenerator;
    }

    public function getDocument(): \PdfGenerator\IR\Structure\Document
    {
        return $this->document;
    }

    public function visitParagraph(Block\Paragraph $param, float $maxWidth, float $maxHeight)
    {
        [$width, $height] = $this->getWidthHeight($param, $maxWidth, $maxHeight);
    }

    private function getWidthHeight(Block\Paragraph $param, float $maxWidth, float $maxHeight)
    {
        // if dimensions set, ignore
        if ($param->getDimensions()) {
            return $param->getDimensions();
        }

        return [$maxWidth, $maxHeight];
    }
}
