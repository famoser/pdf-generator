<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font\Composite;

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
     *
     * @var string
     */
    private $registry;

    /**
     * the specific character set (defined by cmap)
     * use unique ordering for each character subset.
     *
     * @var string
     */
    private $ordering;

    /**
     * a version number which can be used when adding a new character to an existing set.
     *
     * @var int
     */
    private $supplement = 0;
}
