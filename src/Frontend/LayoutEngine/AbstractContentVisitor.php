<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine;

use PdfGenerator\Frontend\Content\ImagePlacement;
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Spacer;

/**
 * @template T
 */
abstract class AbstractContentVisitor
{
    /**
     * @return T
     */
    abstract public function visitParagraph(Paragraph $paragraph): mixed;

    /**
     * @return T
     */
    abstract public function visitRectangle(Rectangle $rectangle): mixed;

    /**
     * @return T
     */
    abstract public function visitSpacer(Spacer $spacer): mixed;

    /**
     * @return T
     */
    abstract public function visitImagePlacement(ImagePlacement $imagePlacement): mixed;
}
