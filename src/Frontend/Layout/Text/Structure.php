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

enum Structure
{
    case Title;
    case Header_1;
    case Header_2;
    case Header_3;
    case Header_4;
    case Header_5;
    case Paragraph;
    case Aside;
}
