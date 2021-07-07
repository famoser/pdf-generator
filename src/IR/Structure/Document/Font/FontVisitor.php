<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Font;

interface FontVisitor
{
    public function visitDefaultFont(DefaultFont $param);

    public function visitEmbeddedFont(EmbeddedFont $param);
}
