<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Content;

use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\Layout\Content\Style\BlockStyle;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Spacer extends Content
{
    public function __construct(private readonly BlockStyle $style)
    {
        parent::__construct();
    }

    public function getStyle(): BlockStyle
    {
        return $this->style;
    }

    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitSpacer($this);
    }
}
