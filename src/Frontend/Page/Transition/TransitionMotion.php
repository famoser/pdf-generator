<?php

namespace Famoser\PdfGenerator\Frontend\Page\Transition;

enum TransitionMotion
{
    case Inward;
    case Outward;

    public function isValidFor(Transition $transition): bool
    {
        return in_array($transition, [Transition::Split, Transition::Box, Transition::Fly]);
    }
}
