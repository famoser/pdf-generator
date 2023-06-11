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
use PdfGenerator\Frontend\Block\Base\BaseBlock;
use PdfGenerator\FrontendResources\ContentVisitor;
use PdfGenerator\FrontendResources\Cursor;
use PdfGenerator\FrontendResources\MeasuredContent\Utils\FontRepository;
use PdfGenerator\FrontendResources\MeasuredContent\Utils\ImageRepository;
use PdfGenerator\FrontendResources\WordSizer\WordSizerRepository;

class Document implements DocumentInterface
{
    private readonly \PdfGenerator\IR\Document $document;

    private readonly PageGenerator $pageGenerator;

    private readonly FontRepository $fontRepository;

    private readonly ImageRepository $imageRepository;

    private readonly WordSizerRepository $wordSizerRepository;

    private readonly ContentVisitor $contentVisitor;

    public function __construct(PageGenerator $pageGenerator = null, private readonly ?Cursor $cursor = null)
    {
        $this->pageGenerator = $pageGenerator ?? new PageGenerator();

        $this->fontRepository = new FontRepository();
        $this->imageRepository = new ImageRepository();
        $this->wordSizerRepository = new WordSizerRepository();

        $this->contentVisitor = new ContentVisitor($this->imageRepository, $this->fontRepository, $this->wordSizerRepository);
    }

    public function add(BaseBlock $block): void
    {
        $locatedBlocks = $this->locate($block);

        foreach ($locatedBlocks as $locatedBlock) {
            $this->print($locatedBlock);
        }
    }

    public function locate(BaseBlock $block, Cursor $startCursor = null): array
    {
        $allocator = $block->createAllocator();
        $allocator->place();

        $startCursor ??= $this->cursor;

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
