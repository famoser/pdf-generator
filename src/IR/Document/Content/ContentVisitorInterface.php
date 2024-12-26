<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Content;

/**
 * @template T
 */
interface ContentVisitorInterface
{
    /**
     * @return T
     */
    public function visitImagePlacement(ImagePlacement $placement);

    /**
     * @return T
     */
    public function visitRectangle(Rectangle $rectangle);

    /**
     * @return T
     */
    public function visitText(Text $text);

    /**
     * @return T
     */
    public function visitParagraph(Paragraph $paragraph);
}
