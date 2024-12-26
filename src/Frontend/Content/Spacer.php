<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use Famoser\PdfGenerator\Frontend\Printer;

class Spacer extends AbstractContent
{
    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitSpacer($this);
    }

    public function print(Printer $printer, float $width, float $height): void
    {
        // empty on purpose; the spacer has no content
    }
}
