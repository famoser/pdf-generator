<?php

namespace Famoser\PdfGenerator\IR\Document\Page;

readonly class PageProperties
{
    /**
     * @param array{float,float,float,float}      $mediaBox
     * @param array{float,float,float,float}|null $cropBox
     * @param array{float,float,float,float}|null $bleedBox
     * @param array{float,float,float,float}|null $trimBox
     * @param array{float,float,float,float}|null $artBox
     */
    public function __construct(
        private array $mediaBox, private ?array $cropBox = null, private ?array $bleedBox = null, private ?array $trimBox = null, private ?array $artBox = null,
        private ?int $rotate = null,
        private ?float $userUnit = null,
        private ?int $showDuration = null,
        private ?string $transition = null, private int $transitionDuration = 1, private ?string $transitionDimension = null, private ?string $transitionMotion = null, private ?string $transitionDirection = null, private float $transitionScaleStartOrEnd = 1.0, private bool $transitionRectangular = false)
    {
    }

    /**
     * @return array{float,float,float,float}
     */
    public function getMediaBox(): array
    {
        return $this->mediaBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getCropBox(): ?array
    {
        return $this->cropBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getBleedBox(): ?array
    {
        return $this->bleedBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getTrimBox(): ?array
    {
        return $this->trimBox;
    }

    /**
     * @return array{float,float,float,float}|null
     */
    public function getArtBox(): ?array
    {
        return $this->artBox;
    }

    public function getRotate(): ?int
    {
        return $this->rotate;
    }

    public function getUserUnit(): ?float
    {
        return $this->userUnit;
    }

    public function getShowDuration(): ?int
    {
        return $this->showDuration;
    }

    public function getTransition(): ?string
    {
        return $this->transition;
    }

    public function getTransitionDuration(): int
    {
        return $this->transitionDuration;
    }

    public function getTransitionDimension(): ?string
    {
        return $this->transitionDimension;
    }

    public function getTransitionMotion(): ?string
    {
        return $this->transitionMotion;
    }

    public function getTransitionDirection(): ?string
    {
        return $this->transitionDirection;
    }

    public function getTransitionScaleStartOrEnd(): float
    {
        return $this->transitionScaleStartOrEnd;
    }

    public function getTransitionRectangular(): bool
    {
        return $this->transitionRectangular;
    }
}
