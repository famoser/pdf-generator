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

class Document implements DocumentInterface
{
    /**
     * @param array $config
     * @param bool $restoreDefaults
     */
    public function configure(array $config = [], bool $restoreDefaults = true)
    {
        // TODO: Implement configure() method.
    }

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author)
    {
        // TODO: Implement setMeta() method.
    }

    /**
     * @param string $text
     * @param float $width
     */
    public function printText(string $text, float $width)
    {
        // TODO: Implement printText() method.
    }

    /**
     * @param string $imagePath
     * @param float $width
     * @param float $height
     */
    public function printImage(string $imagePath, float $width, float $height)
    {
        // TODO: Implement printImage() method.
    }

    /**
     * @param float $width
     * @param float $height
     */
    public function printRectangle(float $width, float $height)
    {
        // TODO: Implement printRectangle() method.
    }

    /**
     * @param string $filePath
     */
    public function save(string $filePath)
    {
        // TODO: Implement save() method.
    }

    /**
     * starts a region with columns.
     *
     * @param int $columnCount
     */
    public function createColumnLayout(int $columnCount)
    {
        // TODO: Implement createColumnLayout() method.
    }
}
