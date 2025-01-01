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

interface ContentVisitorInterface
{
    public function visitImagePlacement(ImagePlacement $imagePlacement): void;

    public function visitRectangle(Rectangle $rectangle): void;

    public function visitTextBlock(TextBlock $textBlock): void;
}
