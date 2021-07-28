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
use PdfGenerator\Frontend\Content\Base\Content;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;
use PdfGenerator\Frontend\MeasuredContent\Utils\FontRepository;
use PdfGenerator\Frontend\MeasuredContent\Utils\ImageRepository;
use PdfGenerator\IR\Text\WordSizer\WordSizerRepository;

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

    /**
     * @var FontRepository
     */
    private $fontRepository;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var WordSizerRepository
     */
    private $wordSizerRepository;

    /**
     * @var ContentVisitor
     */
    private $contentVisitor;

    public function __construct(PageGenerator $pageGenerator = null, Cursor $cursor = null)
    {
        $this->pageGenerator = $pageGenerator ?? new PageGenerator();

        $this->fontRepository = new FontRepository();
        $this->imageRepository = new ImageRepository();
        $this->wordSizerRepository = new WordSizerRepository();

        $this->contentVisitor = new ContentVisitor($this->imageRepository, $this->fontRepository, $this->wordSizerRepository);
    }

    public function addContent(Content $content)
    {
        $measuredContent = $this->measure($content);
        $contentBlock = new \PdfGenerator\Frontend\Block\Content($measuredContent);
        $this->add($contentBlock);
    }

    public function add(Block $block)
    {
        $locatedBlocks = $this->locate($measuredBlock);

        foreach ($locatedBlocks as $locatedBlock) {
            $this->print($locatedBlock);
        }
    }

    public function measure(Content $content): MeasuredContent
    {
        return $content->accept($this->contentVisitor);
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
