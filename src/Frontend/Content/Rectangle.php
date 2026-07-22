<?php

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;

readonly class Rectangle extends AbstractContent
{
    public function __construct(private float $width, private float $height, private DrawingStyle $style)
    {
        parent::__construct($this->width, $this->height);
    }

    public function getStyle(): DrawingStyle
    {
        return $this->style;
    }

    public function accept(ContentVisitorInterface $contentVisitor): void
    {
        $contentVisitor->visitRectangle($this);
    }
}
