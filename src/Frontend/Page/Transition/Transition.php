<?php

namespace Famoser\PdfGenerator\Frontend\Page\Transition;

enum Transition
{
    case Split;
    case Blinds;
    case Box;
    case Wipe;
    case Dissolve;
    case Glitter;
    case Fly;
    case Push;
    case Cover;
    case Uncover;
    case Fade;
}
