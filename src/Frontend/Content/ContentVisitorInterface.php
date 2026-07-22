<?php

namespace Famoser\PdfGenerator\Frontend\Content;

interface ContentVisitorInterface
{
    public function visitImagePlacement(ImagePlacement $imagePlacement): void;

    public function visitRectangle(Rectangle $rectangle): void;

    public function visitTextBlock(TextBlock $textBlock): void;
}
