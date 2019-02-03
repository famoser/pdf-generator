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

use PdfGenerator\Backend\Content\TextContent;
use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Builder\PageBuilder;
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
     * @param string $text
     * @param float $xPosition
     * @param float $yPosition
     */
    public function printText(string $text, float $xPosition, float $yPosition)
    {
        $this->ensureConfigurationApplied();

        $page = $this->getActivePageBuilder();
        $font = $this->getActiveFont();

        $contentBuilder = $page->getContentsBuilder();
        $contentBuilder->addContent(new TextContent($font, $this->configuration->getFontSize(), $xPosition, $yPosition, $text));
    }

    /**
     * @param string $imagePath
     * @param float $width
     * @param float $height
     */
    public function printImage(string $imagePath, float $width, float $height)
    {
        $this->ensureConfigurationApplied();
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
     * @return \PdfGenerator\Backend\Structure\Font
     */
    protected function getActiveFont()
    {
        return $this->document->getResourcesBuilder()->getFontCollection()->getHelvetica();
    }

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author)
    {
    }

    /**     *
     * @throws \Exception
     */
    public function save()
    {
        return $this->document->render();
    }
}
