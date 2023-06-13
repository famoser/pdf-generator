<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use PdfGenerator\Frontend\Layout\Base\BaseBlock;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

abstract class Content extends BaseBlock
{
    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitContent($this);
    }
}
