<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Pdf\Mock;

use PdfGenerator\Frontend\Configuration\PrintConfiguration;
use PdfGenerator\Pdf\Cursor;
use PdfGenerator\Pdf\PdfDocumentInterface;

class PdfDocumentMock implements PdfDocumentInterface
{
    /**
     * @var Cursor
     */
    private $cursor;

    /**
     * @var PrintConfiguration
     */
    private $printConfiguration;

    /**
     * PdfDocumentMock constructor.
     */
    public function __construct()
    {
        $this->cursor = new Cursor(0, 0, 0);
        $this->printConfiguration = new PrintConfiguration();
    }

    /**
     * returns the active cursor position as an array of [$xCoordinate, $yCoordinate, $page].
     *
     * @return Cursor
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @param Cursor $cursor
     */
    public function setCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @return PrintConfiguration
     */
    public function getConfiguration()
    {
        return $this->printConfiguration;
    }

    /**
     * @param PrintConfiguration $printConfiguration
     */
    public function setConfiguration(PrintConfiguration $printConfiguration)
    {
        $this->printConfiguration = $printConfiguration;
    }

    /**
     * @param array $config
     * @param bool $restoreDefaults
     *
     * @throws \Exception
     */
    public function configure(array $config = [], bool $restoreDefaults = true)
    {
        $this->printConfiguration->setConfiguration($config);
    }

    /**
     * @param \Closure $printClosure
     *
     * @return Cursor
     */
    public function cursorAfterwardsIfPrinted(\Closure $printClosure)
    {
        return $this->cursor;
    }

    /**
     * @param string $text
     *
     * @return float
     */
    public function calculateWidthOfText(string $text)
    {
        return 20;
    }

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author)
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
     * starts a new page & sets the cursor to the next page.
     */
    public function startNewPage()
    {
    }

    /**
     * @param string $text
     * @param float $width
     */
    public function printText(string $text, float $width)
    {
    }

    /**
     * @param string $imagePath
     * @param float $width
     * @param float $height
     */
    public function printImage(string $imagePath, float $width, float $height)
    {
    }

    /**
     * @param Cursor $target
     */
    public function drawUntil(Cursor $target)
    {
    }

    /**
     * @param string $filePath
     */
    public function save(string $filePath)
    {
    }
}
