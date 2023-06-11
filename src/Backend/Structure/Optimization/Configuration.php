<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Optimization;

readonly class Configuration
{
    public function __construct(private bool $autoResizeImages = true, private int $autoResizeImagesDpi = 72, private bool $createFontSubsets = true, private bool $useTTFFonts = false)
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
}
