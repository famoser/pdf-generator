<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Content;

interface ContentVisitorInterface
{
    public function visitImagePlacement(ImagePlacement $placement);

    public function visitRectangle(Rectangle $rectangle);

    public function visitText(Text $text);

    public function visitParagraph(Paragraph $paragraph);
}
