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

class Configuration
{
    /**
     * @var bool
     */
    private $autoResizeImages = false;

    /**
     * @var int
     */
    private $autoResizeImagesDpi = 72;

    /**
     * @var bool
     */
    private $createFontSubsets = true;

    /**
     * @var bool
     */
    private $useTTFFonts = false;

    public function getAutoResizeImages(): bool
    {
        return $this->autoResizeImages;
    }

    public function setAutoResizeImages(bool $autoResizeImages): void
    {
        $this->autoResizeImages = $autoResizeImages;
    }

    public function getAutoResizeImagesDpi(): int
    {
        return $this->autoResizeImagesDpi;
    }

    public function setAutoResizeImagesDpi(int $autoResizeImagesDpi): void
    {
        $this->autoResizeImagesDpi = $autoResizeImagesDpi;
    }

    public function getCreateFontSubsets(): bool
    {
        return $this->createFontSubsets;
    }

    public function setCreateFontSubsets(bool $createFontSubsets): void
    {
        $this->createFontSubsets = $createFontSubsets;
    }

    public function getUseTTFFonts(): bool
    {
        return $this->useTTFFonts;
    }

    public function setUseTTFFonts(bool $useTTFFonts): void
    {
        $this->useTTFFonts = $useTTFFonts;
    }
}
