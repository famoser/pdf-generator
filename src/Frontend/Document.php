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
    private \PdfGenerator\IR\Structure\Document $document;

    private ?Cursor $cursor;

    private PageGenerator $pageGenerator;

    private FontRepository $fontRepository;

    private ImageRepository $imageRepository;

    private WordSizerRepository $wordSizerRepository;

    private ContentVisitor $contentVisitor;

    public function __construct(PageGenerator $pageGenerator = null, Cursor $cursor = null)
    {
        $this->pageGenerator = $pageGenerator ?? new PageGenerator();
        $this->cursor = $cursor;

        $this->fontRepository = new FontRepository();
        $this->imageRepository = new ImageRepository();
        $this->wordSizerRepository = new WordSizerRepository();

        $this->contentVisitor = new ContentVisitor($this->imageRepository, $this->fontRepository, $this->wordSizerRepository);
    }

    public function addContent(Content $content): void
    {
        $measuredContent = $this->measureContent($content);

        $contentBlock = new \PdfGenerator\Frontend\Block\Content($measuredContent);
        $this->add($contentBlock);
    }

    public function add(Block $block): void
    {
        $locatedBlocks = $this->locate($block);

        foreach ($locatedBlocks as $locatedBlock) {
            $this->print($locatedBlock);
        }
    }

    public function measureContent(Content $content): MeasuredContent
    {
        return $content->accept($this->contentVisitor);
    }

    public function locate(Block $block, Cursor $startCursor = null): array
    {
        $allocator = $block->createAllocator();
        $allocator->place();

        $startCursor = $startCursor ?? $this->cursor;

        return [new LocatedBlock($startCursor, $block)];
    }

    public function print(LocatedBlock $block)
    {
    }

    public function save(): string
    {
        return $this->document->save();
    }
}
