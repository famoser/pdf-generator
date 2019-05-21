<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table;

use PdfGenerator\Font\Frontend\Structure\Traits\RawContent;

/**
 * the post script table includes information needed by postscript printers.
 * is required by ttf files, but probably not useful for fonts used within pdf.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6post.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/post
 */
class PostTable
{
    /*
     * the raw post table
     * does depend on the characters included in the font.
     */
    use RawContent;
}
