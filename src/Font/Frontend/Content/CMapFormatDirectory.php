<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Content;

use PdfGenerator\Font\Frontend\Structure\Table\CMap\Format\Format4;

class CMapFormatDirectory
{
    /**
     * @var Format4
     */
    private $format4;

    /**
     * @return Format4
     */
    public function getFormat4(): Format4
    {
        return $this->format4;
    }

    /**
     * @param Format4 $format4
     */
    public function setFormat4(Format4 $format4): void
    {
        $this->format4 = $format4;
    }
}
