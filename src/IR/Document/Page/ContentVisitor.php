<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Page;

use PdfGenerator\IR\Document\Page\Content\ImagePlacement;
use PdfGenerator\IR\Document\Page\Content\Rectangle;
use PdfGenerator\IR\Document\Page\Content\Text;

abstract class ContentVisitor
{
    abstract public function visitImagePlacement(ImagePlacement $placement);

    abstract public function visitRectangle(Rectangle $rectangle);

    abstract public function visitText(Text $param);
}
