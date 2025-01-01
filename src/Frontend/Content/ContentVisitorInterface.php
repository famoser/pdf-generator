<?php

namespace Famoser\PdfGenerator\Frontend\Content;

interface ContentVisitorInterface
{
    public function visitImagePlacement(ImagePlacement $imagePlacement);

    public function visitRectangle(Rectangle $rectangle);

    public function visitTextBlock(TextBlock $textBlock);
}
