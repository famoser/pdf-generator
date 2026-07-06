<?php

namespace Famoser\PdfGenerator\Frontend\Document;

enum Rotation
{
    case Upright;
    case RotatedClockwise;
    case UpsideDown;
    case RotatedCounterclockwise;
}
