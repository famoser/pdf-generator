<?php

namespace Famoser\PdfGenerator\Frontend\Page;

trait DimensionTrait
{
    /**
     * @var array{float,float,float,float}|null
     */
    private ?array $cropBox = null;

    /**
     * @var array{float,float,float,float}|null
     */
    private ?array $bleedBox = null;

    /**
     * @var array{float,float,float,float}|null
     */
    private ?array $trimBox = null;

    /**
     * @var array{float,float,float,float}|null
     */
    private ?array $artBox = null;

    /**
     * @return array{float,float,float,float}|null
     */
    public function getCropBox(): ?array
    {
        return $this->cropBox;
    }

    /**
     * @param array{float,float,float,float}|null $cropBox
     */
    public function setCropBox(?array $cropBox): void
    {
        $this->cropBox = $cropBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getBleedBox(): ?array
    {
        return $this->bleedBox;
    }

    /**
     * @param array{float,float,float,float}|null $bleedBox
     */
    public function setBleedBox(?array $bleedBox): void
    {
        $this->bleedBox = $bleedBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getTrimBox(): ?array
    {
        return $this->trimBox;
    }

    /**
     * @param array{float,float,float,float}|null $trimBox
     */
    public function setTrimBox(?array $trimBox): void
    {
        $this->trimBox = $trimBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getArtBox(): ?array
    {
        return $this->artBox;
    }

    /**
     * @param array{float,float,float,float}|null $artBox
     */
    public function setArtBox(?array $artBox): void
    {
        $this->artBox = $artBox;
    }
}
