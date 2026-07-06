<?php

namespace Famoser\PdfGenerator\Frontend\Document\Transition;

enum TransitionDimension
{
    case Horizontal;
    case Vertical;

    public function isValidFor(Transition $transition): bool
    {
        return in_array($transition, [Transition::Split, Transition::Blinds]);
    }
}
