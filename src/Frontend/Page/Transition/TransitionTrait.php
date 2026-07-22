<?php

namespace Famoser\PdfGenerator\Frontend\Page\Transition;

trait TransitionTrait
{
    private ?int $showDuration = null;
    private ?Transition $transition = null;
    private int $transitionDuration = 1;
    private ?TransitionDimension $transitionDimension = null;
    private ?TransitionMotion $transitionMotion = null;
    private ?TransitionDirection $transitionDirection = null;
    private float $transitionScaleStartOrEnd = 1.0;
    private bool $transitionRectangular = false;

    public function setShowDuration(?int $showDuration): void
    {
        $this->showDuration = $showDuration;
    }

    public function setTransition(?Transition $transition, int $duration = 1, ?TransitionDimension $dimension = null, ?TransitionMotion $motion = null, ?TransitionDirection $direction = null, float $scaleStartOrEnd = 1.0, bool $rectangular = false): void
    {
        if (Transition::Fly !== $transition && ($rectangular || 1.0 !== $scaleStartOrEnd)) {
            throw new \Exception('Rectangular or scaleStartOrEnd are not supported for this transition: ' . $transition->name);
        }

        if ($dimension && !$dimension->isValidFor($transition)) {
            throw new \Exception('Dimension is not supported for this transition: ' . $transition->name);
        }

        if ($motion && !$motion->isValidFor($transition)) {
            throw new \Exception('Motion is not supported for this transition: ' . $transition->name);
        }

        if ($direction && !$direction->isValidFor($transition)) {
            throw new \Exception('Direction is not supported for this transition: ' . $transition->name);
        }

        $this->transition = $transition;
        $this->transitionDuration = $duration;
        $this->transitionDimension = $dimension;
        $this->transitionMotion = $motion;
        $this->transitionDirection = $direction;
        $this->transitionScaleStartOrEnd = $scaleStartOrEnd;
        $this->transitionRectangular = $rectangular;
    }

    public function getShowDuration(): ?int
    {
        return $this->showDuration;
    }

    public function getTransition(): ?Transition
    {
        return $this->transition;
    }

    public function getTransitionDuration(): int
    {
        return $this->transitionDuration;
    }

    public function getTransitionDimension(): ?TransitionDimension
    {
        return $this->transitionDimension;
    }

    public function getTransitionMotion(): ?TransitionMotion
    {
        return $this->transitionMotion;
    }

    public function getTransitionDirection(): ?TransitionDirection
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
