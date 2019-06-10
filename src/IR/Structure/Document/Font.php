<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\IR\Structure\Base\BaseDocumentStructure;

abstract class Font extends BaseDocumentStructure
{
    /**
     * sets the encoding used by the font.
     *
     * @param string $escaped
     *
     * @return string
     */
    abstract public function encode(string $escaped): string;
}
