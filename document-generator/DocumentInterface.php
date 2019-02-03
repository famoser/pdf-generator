<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator;

interface DocumentInterface
{
    /**
     * @param array $config
     * @param bool $restoreDefaults
     */
    public function configure(array $config = [], bool $restoreDefaults = true);

    /**
     * @param string $title
     * @param string $author
     */
    public function setMeta(string $title, string $author);

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
     * @param float $width
     * @param float $height
     */
    public function printRectangle(float $width, float $height);

    /**
     * @param string $filePath
     */
    public function save(string $filePath);
}
