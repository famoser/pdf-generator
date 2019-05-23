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
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format25;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format3;

interface VisitorInterface
{
    /**
     * @param Format1 $format1
     *
     * @return mixed
     */
    public function visitFormat1(Format1 $format1);

    /**
     * @param Format2 $format2
     *
     * @return mixed
     */
    public function visitFormat2(Format2 $format2);

    /**
     * @param Format25 $format25
     *
     * @return mixed
     */
    public function visitFormat25(Format25 $format25);

    /**
     * @param Format3 $format3
     *
     * @return mixed
     */
    public function visitFormat3(Format3 $format3);
}
