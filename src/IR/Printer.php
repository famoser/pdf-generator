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
use PdfGenerator\IR\Printer\StatefulPrinter;

class Printer extends StatefulPrinter
{
    /**
     * @var Document
     */
    private $document;

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
    }

    /**
     * @param float $xPosition
     * @param float $yPosition
     * @param string $text
     */
    public function printText(float $xPosition, float $yPosition, string $text)
    {
        $this->ensureConfigurationApplied();

        $page = $this->getActivePageBuilder();
        $font = $this->getFontCollection()->getHelvetica();

        $contentBuilder = $page->getContentsBuilder();
        $contentBuilder->addContent(new TextContent($xPosition, $yPosition, $text, $font, $this->configuration->getFontSize()));
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

        $page = $this->getActivePageBuilder();
        $image = $this->getImageCollection()->getOrCreateImage($imagePath);

        $contentBuilder = $page->getContentsBuilder();
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
