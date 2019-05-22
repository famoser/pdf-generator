<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Post;

use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format1;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Frontend\StreamReader;

class FormatReader
{
    public function readFormat(StreamReader $streamReader, float $format)
    {
        switch ($format) {
            case 1.0:
                return new Format1();
            case 2.0:
                return $this->readFormat2($streamReader);
        }
    }

    /**
     * @param StreamReader $streamReader
     *
     * @return Format2
     */
    private function readFormat2(StreamReader $streamReader)
    {
        $format2 = new Format2();

        return $format2;
    }
}
