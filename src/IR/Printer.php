<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\Backend\Content\ImageContent;
use PdfGenerator\Backend\Content\TextContent;
use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Builder\PageBuilder;
use PdfGenerator\Backend\Structure\Supporting\FontCollection;
use PdfGenerator\Backend\Structure\Supporting\ImageCollection;
use PdfGenerator\IR\Content\TextFactory;
use PdfGenerator\IR\Font\FontRepository;
use PdfGenerator\IR\Printer\StatefulPrinter;

class Printer extends StatefulPrinter
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var FontRepository
     */
    private $fontRepository;

    /**
     * @var TextFactory
     */
    private $textFactory;

    /**
     * @var PageBuilder[]
     */
    private $pageBuilders = [];

    /**
     * PdfDocument constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->document = new Document();
        $this->fontRepository = new FontRepository($this->document);
        $this->textFactory = new TextFactory($this->fontRepository);
    }

    /**
     * @param float $xPosition
     * @param float $yPosition
     * @param string $text
     */
    public function printText(float $xPosition, float $yPosition, string $text)
    {
        $this->ensureConfigurationApplied();

        $textSymbols = $this->textFactory->create($text, $this->configuration);
        $textContent = new TextContent($xPosition, $yPosition, $textSymbols);

        $contentBuilder = $this->getActiveContentBuilder();
        $contentBuilder->addContent($textContent);
    }

    /**
     * @param float $xPosition
     * @param float $yPosition
     * @param float $width
     * @param float $height
     * @param string $imagePath
     */
    public function printImage(float $xPosition, float $yPosition, float $width, float $height, string $imagePath)
    {
        $this->ensureConfigurationApplied();

        $image = $this->getImageCollection()->getOrCreateImage($imagePath);

        $contentBuilder = $this->getActiveContentBuilder();
        $contentBuilder->addContent(new ImageContent($xPosition, $yPosition, $image, $width, $height));
    }

    /**
     * @param float $width
     * @param float $height
     */
    public function printRectangle(float $width, float $height)
    {
        $this->ensureConfigurationApplied();
    }

    /**
     * @return PageBuilder
     */
    protected function getActivePageBuilder()
    {
        $pageBuildersCount = \count($this->pageBuilders);
        $targetPageBuilderIndex = $this->page - 1;

        while ($targetPageBuilderIndex >= $pageBuildersCount) {
            $pageBuilder = $this->document->addPage();
            $pageBuilder->setMediaBox($this->configuration->getPageWidth(), $this->configuration->getPageHeight());

            $this->pageBuilders[] = $pageBuilder;
            ++$pageBuildersCount;
        }

        return $this->pageBuilders[$targetPageBuilderIndex];
    }

    /**
     * @return \PdfGenerator\Backend\Structure\Builder\ContentsBuilder
     */
    protected function getActiveContentBuilder()
    {
        return $this->getActivePageBuilder()->getContentsBuilder();
    }

    /**
     * @return FontCollection
     */
    protected function getFontCollection()
    {
        return $this->document->getResourcesBuilder()->getFontCollection();
    }

    /**
     * @return ImageCollection
     */
    protected function getImageCollection()
    {
        return $this->document->getResourcesBuilder()->getImageCollection();
    }

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author)
    {
        // todo: use 14.3 for this
    }

    /**     *
     * @throws \Exception
     */
    public function save()
    {
        return $this->document->render();
    }
}
