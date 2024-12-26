<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog\Font\Structure;

use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Token\DictionaryToken;

/**
 * specifies the character set used
 * must be equal for CMap and CIDFont which should be used together.
 *
 * @see https://www.adobe.com/content/dam/acom/en/devnet/font/pdfs/5014.CIDFont_Spec.pdf page 23ff
 */
readonly class CIDSystemInfo
{
    /**
     * @param string $registry   the font vendor
     *                           can use "famoser" here
     * @param string $ordering   the specific character set (defined by cmap)
     *                           use unique ordering for each character subset
     * @param int    $supplement a version number which can be used when adding a new character to an existing set
     */
    public function __construct(private string $registry, private string $ordering, private int $supplement = 0)
    {
    }

    public function getRegistry(): string
    {
        return $this->registry;
    }

    public function getOrdering(): string
    {
        return $this->ordering;
    }

    public function getSupplement(): int
    {
        return $this->supplement;
    }

    public function accept(CatalogVisitor $param): DictionaryToken
    {
        return $param->visitCIDSystemInfo($this);
    }
}
