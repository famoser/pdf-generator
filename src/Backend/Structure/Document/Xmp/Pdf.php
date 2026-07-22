<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Xmp;

readonly class Pdf
{
    public function __construct(private ?string $keywords)
    {
    }

    public static function createEmpty(): self
    {
        return new self(null);
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }
}
