<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak\FontSizer;

interface FontSizer
{
    public function getWidth(string $word): float;

    public function getSpaceWidth(): float;

    public function getAscender();

    public function getDescender();

    public function getLineGap();

    public function getBaselineToBaselineDistance();
}
