<?php

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
}
