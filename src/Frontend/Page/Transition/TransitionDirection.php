<?php

namespace Famoser\PdfGenerator\Frontend\Page\Transition;

enum TransitionDirection
{
    case LeftToRight;
    case BottomToTop;
    case RightToLeft;
    case TopToBottom;
    case TopLeftToBottomRight;
    case None;

    public function isValidFor(Transition $transition): bool
    {
        if (Transition::Wipe === $transition && in_array($this, [self::BottomToTop, self::RightToLeft], true)) {
            return true;
        }

        if (Transition::Glitter === $transition && self::TopLeftToBottomRight === $this) {
            return true;
        }

        if (Transition::Fly === $transition && self::None === $this) {
            return true;
        }

        if (in_array($transition, [Transition::Wipe, Transition::Glitter, Transition::Fly, Transition::Uncover, Transition::Push]) && in_array($this, [self::LeftToRight, self::TopToBottom], true)) {
            return true;
        }

        return false;
    }
}
