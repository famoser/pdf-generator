<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout\Text;

enum Alignment
{
    case ALIGNMENT_LEFT;
    case ALIGNMENT_CENTER;
    case ALIGNMENT_JUSTIFIED;
    case ALIGNMENT_RIGHT;
}
