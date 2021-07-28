<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\WordSizer;

interface WordSizerInterface
{
    public function getWidth(string $word): float;

    public function getSpaceWidth(): float;
}
