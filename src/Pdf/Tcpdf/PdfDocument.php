<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Pdf\Tcpdf;

use PdfGenerator\Pdf\Configuration\PrintConfiguration;
use PdfGenerator\Pdf\Cursor;
use PdfGenerator\Pdf\PdfDocumentInterface;
use PdfGenerator\Pdf\PdfPageLayoutInterface;

/**
 * implements the predictable publish PdfDocumentInterface with the TCPDF library.
 *
 * Class PdfDocument
 */
class PdfDocument implements PdfDocumentInterface
{
    /**
     * @var Cursor
     */
    private $cursor;

    /**
     * @var PrintConfiguration
     */
    private $configuration;

    /**
     * @var bool
     */
    private $configurationChanged = true;

    /**
     * @var PdfPageLayoutInterface
     */
    private $pageLayout;

    /**
     * PdfDocument constructor.
     *
     * @param PdfPageLayoutInterface $pageLayout
     *
     * @throws \Exception
     */
    public function __construct(PdfPageLayoutInterface $pageLayout)
    {
        $this->pageLayout = $pageLayout;

        $this->configure();
        $this->startNewPage();

        $pageLayout->initializeLayout($this);
    }

    /**x
     * @param Cursor $cursor
     */
    public function setCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @param string $text
     * @param float $width
     */
    public function printText(string $text, float $width)
    {
        $this->ensureConfigurationApplied();
    }

    /**
     * applies the config if it has changed.
     */
    private function ensureConfigurationApplied()
    {
        if (!$this->configurationChanged) {
            return;
        }

        $this->configurationChanged = false;
    }

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author)
    {
    }

    /**
     * @param string $filePath
     */
    public function save(string $filePath)
    {
    }

    /**
     * @param float $marginLeft
     * @param float $marginTop
     * @param float $marginRight
     * @param float $marginBottom
     */
    public function setPageMargins(float $marginLeft, float $marginTop, float $marginRight, float $marginBottom)
    {
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
     * @param \Closure $printClosure
     *
     * @return bool
     */
    public function causesPageBreak(\Closure $printClosure)
    {
        list($cursorBefore, $cursorAfter) = $this->measureImpact($printClosure);

        return $cursorBefore->getPage() < $cursorAfter->getPage();
    }

    /**
     * returns the active cursor position.
     *
     * @return Cursor
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * starts a new page and puts cursor on it.
     */
    public function startNewPage()
    {
    }

    /**
     * @param array $config
     * @param bool $restoreDefaults
     *
     * @throws \Exception
     */
    public function configure(array $config = [], bool $restoreDefaults = true)
    {
        $this->configurationChanged = true;

        if ($restoreDefaults) {
            $this->configuration = new PrintConfiguration();
            $this->configuration->setConfiguration([
                PrintConfiguration::FONT_SIZE => 8,
                PrintConfiguration::TEXT_COLOR => '#000000',
            ]);
        }

        $this->configuration->setConfiguration($config);
    }

    /**
     * @param string $text
     *
     * @return float
     */
    public function calculateWidthOfText(string $text)
    {
        return 0;
    }

    /**
     * @param \Closure $printClosure
     *
     * @return Cursor[]
     */
    private function measureImpact(\Closure $printClosure)
    {
        $printClosure();

        return [$this->cursor, $this->cursor];
    }

    /**
     * @throws \Exception
     *
     * @return PrintConfiguration
     */
    public function getConfiguration()
    {
        return PrintConfiguration::createFromExisting($this->configuration);
    }

    /**
     * @param PrintConfiguration $printConfiguration
     *
     * @throws \Exception
     */
    public function setConfiguration(PrintConfiguration $printConfiguration)
    {
        $this->configuration = PrintConfiguration::createFromExisting($printConfiguration);
        $this->configurationChanged = true;
    }

    /**
     * @param \Closure $printClosure
     *
     * @return Cursor
     */
    public function cursorAfterwardsIfPrinted(\Closure $printClosure)
    {
        [, $after] = $this->measureImpact($printClosure);

        return $after;
    }

    /**
     * @param Cursor $target
     *
     * @throws \Exception
     */
    public function drawUntil(Cursor $target)
    {
        // reset cursor
        $this->setCursor($target);
    }
}
