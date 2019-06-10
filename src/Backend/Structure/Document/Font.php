<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;

abstract class Font extends BaseDocumentStructure
{
    /**
     * @var string
     */
    private $baseFont;

    /**
     * Font constructor.
     *
     * @param string $baseFont
     */
    public function __construct(string $baseFont)
    {
        $this->baseFont = $baseFont;
    }

    /**
     * @return string
     */
    public function getBaseFont(): string
    {
        return $this->baseFont;
    }
}