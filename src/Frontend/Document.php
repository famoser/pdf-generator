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

use DocumentGenerator\DocumentInterface;
use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Column;
use PdfGenerator\Frontend\Content\Base\Content;

class Document implements DocumentInterface
{
    /**
     * @var \PdfGenerator\IR\Structure\Document
     */
    private $document;

    /**
     * @var Cursor
     */
    private $cursor;

    /**
     * @var PageGenerator
     */
    private $pageGenerator;

    public function __construct(PageGenerator $pageGenerator = null, Cursor $cursor = null)
    {
        $this->pageGenerator = $pageGenerator ?? new PageGenerator();
    }

    public function addContent(Content $content)
    {
        $measuredContent = $this->measure($content);
        $column = new Column();
        $column->addBlock($measuredContent);
    }

    public function add(Block $block)
    {
        $measuredBlock = $this->measure($block);
        $locatedBlocks = $this->locate($measuredBlock);

        foreach ($locatedBlocks as $locatedBlock) {
            $this->print($locatedBlock);
        }
    }

    public function measure(Content $content): MeasuredBlock
    {
        $measuredBlock = new MeasuredBlock();

        return $measuredBlock;
    }

    public function locate(MeasuredBlock $measuredBlock): array
    {
        $cursor = $cursor ?? $this->cursor;

        return [new LocatedBlock($cursor, $measuredBlock)];
    }

    public function print(LocatedBlock $block)
    {
    }

    public function save(): string
    {
        return $this->document->save();
    }
}
