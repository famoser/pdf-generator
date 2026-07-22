<?php

namespace Famoser\PdfGenerator\Backend\Structure\Optimization;

readonly class Configuration
{
    public function __construct(private bool $autoResizeImages = true, private int $autoResizeImagesDpi = 72, private bool $createFontSubsets = true, private bool $useTTFFonts = false, private bool $useMinimalCMapSize = true)
    {
    }

    public function getAutoResizeImages(): bool
    {
        return $this->autoResizeImages;
    }

    public function getAutoResizeImagesDpi(): int
    {
        return $this->autoResizeImagesDpi;
    }

    public function getCreateFontSubsets(): bool
    {
        return $this->createFontSubsets;
    }

    public function getUseTTFFonts(): bool
    {
        return $this->useTTFFonts;
    }

    public function getMinimalCMapSize(): bool
    {
        return $this->useMinimalCMapSize;
    }
}
