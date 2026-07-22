<?php

namespace Famoser\PdfGenerator\Frontend\Resource\Font\WordSizer;

interface WordSizerInterface
{
    public function getWidth(string $word): int;

    public function getSpaceWidth(): int;
}
