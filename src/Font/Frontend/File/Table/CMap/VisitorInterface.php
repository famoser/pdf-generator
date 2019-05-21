<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\CMap;

use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format0;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format12;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format6;

interface VisitorInterface
{
    /**
     * @param Format0 $format0
     *
     * @return mixed
     */
    public function visitFormat0(Format0 $format0);

    /**
     * @param Format4 $format4
     *
     * @return mixed
     */
    public function visitFormat4(Format4 $format4);

    /**
     * @param Format6 $format6
     *
     * @return mixed
     */
    public function visitFormat6(Format6 $format6);

    /**
     * @param Format12 $format12
     *
     * @return mixed
     */
    public function visitFormat12(Format12 $format12);
}
