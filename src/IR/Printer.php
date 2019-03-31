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

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\Content\ImageContent;
use PdfGenerator\Backend\Content\TextContent;
use PdfGenerator\Backend\Document;
use PdfGenerator\IR\Configuration\LevelFactory;
use PdfGenerator\IR\Configuration\StateFactory;
use PdfGenerator\IR\Structure\ContentFactory;

class Printer
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var ContentFactory
     */
    private $contentFactor;

    /**
     * @var LevelFactory
     */
    private $levelFactory;

    /**
     * @var StateFactory
     */
    private $stateFactory;

    /**
     * PdfDocument constructor.
     */
    public function __construct()
    {
        $this->document = new Document();
        $this->contentFactor = new ContentFactory($this->document);
        $this->stateFactory = new StateFactory($this->contentFactor->getFontRepository());
        $this->levelFactory = new LevelFactory($this->stateFactory);
    }

    /**
     * @param string $text
     */
    public function printText(string $text)
    {
        $textLevel = $this->levelFactory->getTextLevelRepository()->getTextLevel();
        $textContent = new TextContent($text, $textLevel);

        $this->printContent($textContent);
    }

    /**
     * @param string $imagePath
     */
    public function printImage(string $imagePath)
    {
        $image = $this->contentFactor->getImageRepository()->getImage($imagePath);
        $pageLevel = $this->levelFactory->getPageLevelRepository()->getPageLevel();
        $textContent = new ImageContent($image, $pageLevel);

        $this->printContent($textContent);
    }

    /**
     * @param float $width
     * @param float $height
     */
    public function printRectangle(float $width, float $height)
    {
    }

    /**
     * @param BaseContent $baseContent
     */
    protected function printContent(BaseContent $baseContent)
    {
        $page = $this->contentFactor->getPageRepository()->getPage(1);
        $page->getContentsBuilder()->addContent($baseContent);
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

    /**
     * @return StateFactory
     */
    public function getStateFactory(): StateFactory
    {
        return $this->stateFactory;
    }
}
