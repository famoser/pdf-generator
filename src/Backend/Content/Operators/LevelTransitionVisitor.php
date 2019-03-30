<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Operators;

use PdfGenerator\Backend\Content\Operators\Level\PageLevel;
use PdfGenerator\Backend\Content\Operators\Level\TextLevel;

class LevelTransitionVisitor
{
    /**
     * @param PageLevel $param
     * @param PageLevel $previousState
     *
     * @return string[]
     */
    public function visitPage(PageLevel $param, PageLevel $previousState): array
    {
    }

    /**
     * @param TextLevel $param
     * @param TextLevel $previousState
     *
     * @return string[]
     */
    public function visitText(TextLevel $param, TextLevel $previousState): array
    {
    }
}
