<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 2/3/19
 * Time: 5:07 PM
 */

namespace DocumentGenerator\Document;


interface PrintDocumentInterface
{
    /**
     * @param array $config
     * @param bool $restoreDefaults
     */
    public function configure(array $config = [], bool $restoreDefaults = true);

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
}