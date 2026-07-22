<?php

namespace Famoser\PdfGenerator\Frontend\Content;

abstract readonly class AbstractContent
{
    public function __construct(private float $width, private float $height)
    {
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    abstract public function accept(ContentVisitorInterface $contentVisitor): void;
}
