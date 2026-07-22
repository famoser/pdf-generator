<?php

namespace Famoser\PdfGenerator\IR\Document\Resource\Font;

/**
 * @template T
 */
interface FontVisitor
{
    /**
     * @return T
     */
    public function visitDefaultFont(DefaultFont $param);

    /**
     * @return T
     */
    public function visitEmbeddedFont(EmbeddedFont $param);
}
