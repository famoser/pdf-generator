<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font\Structure;

use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Token\DictionaryToken;

/**
 * specifies the character set used
 * must be equal for CMap and CIDFont which should be used together.
 *
 * @see https://www.adobe.com/content/dam/acom/en/devnet/font/pdfs/5014.CIDFont_Spec.pdf page 23ff
 */
class CIDSystemInfo
{
    /**
     * the font vendor
     * can use "famoser" here.
     */
    private string $registry;

    /**
     * the specific character set (defined by cmap)
     * use unique ordering for each character subset.
     */
    private string $ordering;

    /**
     * a version number which can be used when adding a new character to an existing set.
     */
    private int $supplement = 0;

    public function getRegistry(): string
    {
        return $this->registry;
    }

    public function setRegistry(string $registry): void
    {
        $this->registry = $registry;
    }

    public function getOrdering(): string
    {
        return $this->ordering;
    }

    public function setOrdering(string $ordering): void
    {
        $this->ordering = $ordering;
    }

    public function getSupplement(): int
    {
        return $this->supplement;
    }

    public function setSupplement(int $supplement): void
    {
        $this->supplement = $supplement;
    }

    public function accept(CatalogVisitor $param): DictionaryToken
    {
        return $param->visitCIDSystemInfo($this);
    }
}
