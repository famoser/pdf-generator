<?php

namespace Famoser\PdfGenerator\Frontend\Resource\Font\WordSizer;

readonly class MonospaceProportionalWordSizer implements WordSizerInterface
{
    public function __construct(private int $characterWidth)
    {
    }

    public function getWidth(string $word): int
    {
        return mb_strlen($word) * $this->characterWidth;
    }

    public function getSpaceWidth(): int
    {
        return $this->characterWidth;
    }
}
