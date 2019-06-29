<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page;

use PdfGenerator\IR\Structure\Document\Page\Content\ImagePlacement;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text;

abstract class ContentVisitor
{
    /**
     * @param ImagePlacement $placement
     *
     * @return mixed
     */
    abstract public function visitImagePlacement(ImagePlacement $placement);

    /**
     * @param Rectangle $rectangle
     *
     * @return mixed
     */
    abstract public function visitRectangle(Rectangle $rectangle);

    /**
     * @param Text $param
     *
     * @return mixed
     */
    abstract public function visitText(Text $param);
}
