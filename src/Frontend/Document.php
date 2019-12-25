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
    public function configure(array $config = [], bool $restoreDefaults = true)
    {
        // TODO: Implement configure() method.
    }

    public function setMeta(string $title, string $author)
    {
        // TODO: Implement setMeta() method.
    }

    public function printText(string $text, float $width)
    {
        // TODO: Implement printText() method.
    }

    public function printImage(string $imagePath, float $width, float $height)
    {
        // TODO: Implement printImage() method.
    }

    public function printRectangle(float $width, float $height)
    {
        // TODO: Implement printRectangle() method.
    }

    public function save(string $filePath)
    {
        // TODO: Implement save() method.
    }

    /**
     * starts a region with columns.
     */
    public function createColumnLayout(int $columnCount)
    {
        // TODO: Implement createColumnLayout() method.
    }
}
