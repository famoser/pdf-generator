<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Pdf;

use App\Service\Report\Document\Pdf\Configuration\PrintConfiguration;

interface PdfDocumentInterface
{
    const PDF_IMPLEMENTATION_TCPDF = 'tcpdf';

    /**
     * @return string
     */
    public function getPdfImplementation();

    /**
     * returns the active cursor position as an array of [$xCoordinate, $yCoordinate, $page].
     *
     * @return Cursor
     */
    public function getCursor();

    /**
     * @param Cursor $cursor
     */
    public function setCursor(Cursor $cursor);

    /**
     * @return PrintConfiguration
     */
    public function getConfiguration();

    /**
     * @param PrintConfiguration $printConfiguration
     */
    public function setConfiguration(PrintConfiguration $printConfiguration);

    /**
     * @param array $config
     * @param bool $restoreDefaults
     */
    public function configure(array $config = [], bool $restoreDefaults = true);

    /**
     * @param \Closure $printClosure
     *
     * @return Cursor
     */
    public function cursorAfterwardsIfPrinted(\Closure $printClosure);

    /**
     * @param string $text
     *
     * @return float
     */
    public function calculateWidthOfText(string $text);

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author);

    /**
     * @param float $marginLeft
     * @param float $marginTop
     * @param float $marginRight
     * @param float $marginBottom
     */
    public function setPageMargins(float $marginLeft, float $marginTop, float $marginRight, float $marginBottom);

    /**
     * starts a new page & sets the cursor to the next page.
     */
    public function startNewPage();

    /**
     * @param string $text
     * @param float $width
     */
    public function printText(string $text, float $width);

    /**
     * @param string $imagePath
     * @param float $width
     * @param float $height
     */
    public function printImage(string $imagePath, float $width, float $height);

    /**
     * @param Cursor $target
     */
    public function drawUntil(Cursor $target);

    /**
     * @param string $filePath
     */
    public function save(string $filePath);
}
